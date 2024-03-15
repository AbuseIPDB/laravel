<?php

namespace AbuseIPDB;

use AbuseIPDB\Exceptions\InvalidParameterException;
use AbuseIPDB\Exceptions\MissingAPIKeyException;
use AbuseIPDB\Exceptions\PaymentRequiredException;
use AbuseIPDB\Exceptions\TooManyRequestsException;
use AbuseIPDB\Exceptions\UnconventionalErrorException;
use AbuseIPDB\Exceptions\UnprocessableContentException;
use DateTime;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AbuseIPDBLaravel
{
    /**
     * API endpoints available with their respective HTTP request verbs
     *
     * @var array
     */
    public const ENDPOINTS = [
        'check' => 'get',
        'reports' => 'get',
        'blacklist' => 'get',
        'report' => 'post',
        'check-block' => 'get',
        'bulk-report' => 'post',
        'clear-address' => 'delete',
    ];

    /**
     * Attacks categories available with their respective identifier
     *
     * @var array
     */
    public const CATEGORIES = [
        'DNS_Compromise' => 1,
        'DNS_Poisoning' => 2,
        'Fraud_Orders' => 3,
        'DDoS_Attack' => 4,
        'FTP_Brute_Force' => 5,
        'Ping_of_Death' => 6,
        'Phishing' => 7,
        'Fraud_VoIP' => 8,
        'Open_Proxy' => 9,
        'Web_Spam' => 10,
        'Email_Spam' => 11,
        'Blog_Spam' => 12,
        'VPN_IP' => 13,
        'Port_Scan' => 14,
        'Hacking' => 15,
        'SQL_Injection' => 16,
        'Spoofing' => 17,
        'Brute_Force' => 18,
        'Bad_Web_Bot' => 19,
        'Exploited_Host' => 20,
        'Web_App_Attack' => 21,
        'SSH' => 22,
        'IoT_Targeted' => 23,
    ];

    /**
     * The "client" to make requests with
     *
     * @var PendingRequest
     */
    private $client;

    /**
     * Lazy loads client and sets base url
     *
     * @throws MissingAPIKeyException
     */
    private function lazyLoadSetup()
    {
        if (! config('abuseipdb.api_key')) {
            throw new MissingAPIKeyException('ABUSEIPDB_API_KEY must be set in .env with an AbuseIPBD API key.');
        }

        $this->client = Http::withHeaders([
            'X-Request-Source' => 'Laravel_'.app()->version().';Laravel_'.config('abuseipdb.version').';',
            'Key' => config('abuseipdb.api_key'),
        ])->withOptions([
            'verify' => ! app()->islocal(),
        ])->baseUrl(config('abuseipdb.base_url'));
    }

    /**
     * Function that all requests will be passed through
     *
     * @throws UnprocessableContentException
     * @throws TooManyRequestsException
     * @throws PaymentRequiredException
     * @throws UnconventionalErrorException
     */
    private function makeRequest($endpointName, $parameters, $acceptType = 'application/json', string $fileContents = null): ?Response
    {
        if (! $this->client) {
            $this->lazyLoadSetup();
        }

        $requestMethod = self::ENDPOINTS[$endpointName];

        $this->client->withHeader('Accept', $acceptType);

        if ($fileContents) {
            $this->client->attach('csv', $fileContents, 'report.csv');
        }

        /**
         * @var Response
         */
        $response = $this->client->$requestMethod($endpointName, $parameters);

        $status = $response->status();

        if ($status === 200) {
            return $response;
        }

        $message = 'AbuseIPDB: '.$response->object()->errors[0]->detail;

        match ($status) {
            429 => throw new TooManyRequestsException($message),
            402 => throw new PaymentRequiredException($message),
            422 => throw new UnprocessableContentException($message),
            default => throw new UnconventionalErrorException($message),
        };
    }

    /**
     * Checks an IP address against the AbuseIPDB database
     *
     * @param string $ipAddress The IP address to check
     * @param int $maxAgeInDays The maximum age of reports to return
     * @param bool $verbose Whether to include verbose information (reports)
     * @throws InvalidParameterException
     */
    public function check(string $ipAddress, int $maxAgeInDays = 30, bool $verbose = false): ResponseObjects\CheckResponse
    {
        if ($maxAgeInDays < 1 || $maxAgeInDays > 365) {
            throw new Exceptions\InvalidParameterException('maxAgeInDays must be between 1 and 365.');
        }

        $parameters = [];
        $parameters['ipAddress'] = $ipAddress;
        $parameters['maxAgeInDays'] = $maxAgeInDays;
        if ($verbose) {
            $parameters['verbose'] = $verbose;
        }

        $httpResponse = $this->makeRequest('check', $parameters);

        return new ResponseObjects\CheckResponse($httpResponse);
    }

    /**
     * Reports an IP address to AbuseIPDB
     *
     * @param string $ip The IP address to report
     * @param array|int $categories The categories to report the IP address for
     * @param string|null $comment A comment to include with the report
     * @param DateTime|null $timestamp A timestamp to include with the report
     * @throws \AbuseIPDB\Exceptions\InvalidParameterException
     */
    public function report(string $ip, array|int $categories, string $comment = null, DateTime $timestamp = null): ResponseObjects\ReportResponse
    {
        foreach ((array) $categories as $cat) {
            if (! in_array($cat, self::CATEGORIES)) {
                throw new Exceptions\InvalidParameterException('Individual category must be a valid category.');
            }
        }

        $parameters = [];
        $parameters['ip'] = $ip;
        $parameters['categories'] = $categories;
        if ($comment) {
            $parameters['comment'] = $comment;
        }
        if ($timestamp) {
            $parameters['timestamp'] = $timestamp->format(DateTime::ATOM);
        }

        $httpResponse = $this->makeRequest('report', $parameters);

        return new ResponseObjects\ReportResponse($httpResponse);
    }

    /**
     * Get the reports for a single IP address (v4 or v6)
     *
     * @param string $ipAddress The IP address to get reports for
     * @param int $maxAgeInDays The maximum age of reports to return
     * @param int $page The page number to get
     * @param int $perPage The number of reports to get per page
     * @throws \AbuseIPDB\Exceptions\InvalidParameterException
     */
    public function reports(string $ipAddress, int $maxAgeInDays = 30, int $page = 1, int $perPage = 25): ResponseObjects\ReportsPaginatedResponse
    {
        if ($maxAgeInDays < 1 || $maxAgeInDays > 365) {
            throw new InvalidParameterException('maxAgeInDays must be between 1 and 365.');
        }

        if ($page < 1) {
            throw new InvalidParameterException('page must be at least 1.');
        }

        if ($perPage < 1 || $perPage > 100) {
            throw new InvalidParameterException('perPage must be between 1 and 100.');
        }

        $parameters = [];
        $parameters['ipAddress'] = $ipAddress;
        $parameters['maxAgeInDays'] = $maxAgeInDays;
        $parameters['page'] = $page;
        $parameters['perPage'] = $perPage;

        $response = $this->makeRequest('reports', $parameters);

        return new ResponseObjects\ReportsPaginatedResponse($response);
    }

    /**
     * Gets the AbuseIPDB blacklist
     * 
     * @param int $confidenceMinimum The minimum confidence score to include an IP in the blacklist
     * @param int $limit The maximum number of blacklisted IPs to return
     * @param bool $plaintext Whether to return the blacklist in plaintext (a plain array of IPs)
     * @param array $onlyCountries Only include IPs from these countries (use 2-letter country codes)
     * @param array $exceptCountries Exclude IPs from these countries (use 2-letter country codes)
     * @param int|null $ipVersion The IP version to return (4 or 6), defaults to both
     * @throws \AbuseIPDB\Exceptions\InvalidParameterException
     */
    public function blacklist(int $confidenceMinimum = 100, int $limit = 10000, bool $plaintext = false, $onlyCountries = [], $exceptCountries = [], int $ipVersion = null): ResponseObjects\BlacklistResponse|ResponseObjects\BlacklistPlaintextResponse
    {
        if ($confidenceMinimum < 25 || $confidenceMinimum > 100) {
            throw new InvalidParameterException('confidenceMinimum must be between 25 and 100.');
        }

        foreach ($onlyCountries as $countryCode) {
            if (strlen($countryCode) != 2) {
                throw new InvalidParameterException('Country codes must be 2 characters long.');
            }
        }

        foreach ($exceptCountries as $countryCode) {
            if (strlen($countryCode) != 2) {
                throw new InvalidParameterException('Country codes must be 2 characters long.');
            }
        }

        if ($ipVersion && $ipVersion != 4 && $ipVersion != 6) {
            throw new InvalidParameterException('ipVersion must be 4 or 6.');
        }

        $parameters = [];
        $parameters['confidenceMinimum'] = $confidenceMinimum;
        $parameters['limit'] = $limit;
        if ($ipVersion) {
            $parameters['ipVersion'] = $ipVersion;
        }
        if ($onlyCountries) {
            $parameters['onlyCountries'] = $onlyCountries;
        }
        if ($exceptCountries) {
            $parameters['exceptCountries'] = $exceptCountries;
        }

        if ($plaintext) {
            $parameters['plaintext'] = $plaintext;
            $httpResponse = $this->makeRequest('blacklist', $parameters, 'text/plain');

            return new ResponseObjects\BlacklistPlaintextResponse($httpResponse);
        } else {
            $httpResponse = $this->makeRequest('blacklist', $parameters);

            return new ResponseObjects\BlacklistResponse($httpResponse);
        }
    }

    /**
     * Checks an entire subnet against the AbuseIPDB database
     * 
     * @param string $network The network to check in CIDR notation (e.g. 127.0.0.1/28)
     * @param int $maxAgeInDays The maximum age of reports to return
     * @throws \AbuseIPDB\Exceptions\InvalidParameterException
     */
    public function checkBlock(string $network, int $maxAgeInDays = 30): ResponseObjects\CheckBlockResponse
    {
        if ($maxAgeInDays < 1 || $maxAgeInDays > 365) {
            throw new InvalidParameterException('maxAgeInDays must be between 1 and 365.');
        }

        $parameters = [];
        $parameters['network'] = $network;
        $parameters['maxAgeInDays'] = $maxAgeInDays;

        $response = $this->makeRequest('check-block', $parameters);

        return new ResponseObjects\CheckBlockResponse($response);
    }

    /**
     * Reports multiple IP addresses to AbuseIPDB in bulk from a csv
     * 
     * @param string $csvFileContents The contents of the csv file to upload
     */
    public function bulkReport(string $csvFileContents): ResponseObjects\BulkReportResponse
    {
        $response = $this->makeRequest('bulk-report', [], fileContents: $csvFileContents);

        return new ResponseObjects\BulkReportResponse($response);
    }

    /**
     * Deletes your reports for a specific address from the AbuseIPDB database
     * 
     * @param string $ipAddress The IP address to clear reports for
     */
    public function clearAddress(string $ipAddress): ResponseObjects\ClearAddressResponse
    {
        $parameters = [];
        $parameters['ipAddress'] = $ipAddress;

        $response = $this->makeRequest('clear-address', $parameters);

        return new ResponseObjects\ClearAddressResponse($response);
    }
}
