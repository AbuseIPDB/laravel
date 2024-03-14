<?php

namespace AbuseIPDB;

use AbuseIPDB\Exceptions\InvalidParameterException;
use AbuseIPDB\Exceptions\MissingAPIKeyException;
use AbuseIPDB\Exceptions\PaymentRequiredException;
use AbuseIPDB\Exceptions\TooManyRequestsException;
use AbuseIPDB\Exceptions\UnconventionalErrorException;
use AbuseIPDB\Exceptions\UnprocessableContentException;
use AbuseIPDB\ResponseObjects\ReportsPaginatedResponse;
use AbuseIPDB\ResponseObjects\BlacklistResponse;
use AbuseIPDB\ResponseObjects\BlacklistPlaintextResponse;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AbuseIPDBLaravel
{
    /**
     * The AbuseIPDB API base url
     * 
     * @var string
     */
    private string $baseUrl;

    /**
     * API endpoints available with their respective HTTP request verbs
     *
     * @var string[]
     */
    private array $endpoints = [
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
     * @var string[]
     */
    private array $categories = [
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

    private $client;

    /**
     * Function that all requests will be passed through
     *
     * @throws MissingAPIKeyException
     * @throws UnprocessableContentException
     * @throws TooManyRequestsException
     * @throws PaymentRequiredException
     * @throws UnconventionalErrorException
     */
    private function makeRequest($endpointName, $parameters, $acceptType = 'application/json'): ?Response
    {
        if (! $this->client) {
            if (! config('abuseipdb.api_key')) {
                throw new MissingAPIKeyException('ABUSEIPDB_API_KEY must be set in .env with an AbuseIPBD API key.');
            }

            $this->baseUrl = config('abuseipdb.base_url');
            $this->client = Http::withHeaders([
                'X-Request-Source' => 'Laravel_' . app()->version() . ';Laravel_' . config('abuseipdb.version') . ';',
                'Key' => config('abuseipdb.api_key'),
            ])->withOptions(['verify' => ! app()->islocal()]);
        }

        $requestMethod = $this->endpoints[$endpointName];

        $this->client->withHeader('Accept', $acceptType);

        /** 
         * @var Response $response 
         */
        $response = $this->client->$requestMethod($this->baseUrl.$endpointName, $parameters);

        $status = $response->status();

        if ($status === 200) {
            return $response;
        }

        // check for different possible error codes
        $message = 'AbuseIPDB: '.$response->object()->errors[0]->detail;

        match ($status) {
            429 => throw new Exceptions\TooManyRequestsException($message),
            402 => throw new Exceptions\PaymentRequiredException($message),
            422 => throw new Exceptions\UnprocessableContentException($message),
            default => throw new Exceptions\UnconventionalErrorException($message),
        };
    }

    /**
     * Checks an IP address against the AbuseIPDB database
     * 
     * @throws InvalidParameterException
     */
    public function check(string $ipAddress, int $maxAgeInDays = 30, bool $verbose = false): ResponseObjects\CheckResponse
    {
        $parameters['ipAddress'] = $ipAddress;
        // only send nullable parameters if present
        if ($maxAgeInDays) {
            if ($maxAgeInDays >= 1 && $maxAgeInDays <= 365) {
                $parameters['maxAgeInDays'] = $maxAgeInDays;
            } else {
                throw new Exceptions\InvalidParameterException('maxAgeInDays must be between 1 and 365.');
            }
        }

        if ($verbose) {
            $parameters['verbose'] = $verbose;
        }

        $httpResponse = $this->makeRequest('check', $parameters);

        return new ResponseObjects\CheckResponse($httpResponse);
    }

    /**
     * Reports an IP address to AbuseIPDB
     * 
     * @throws \AbuseIPDB\Exceptions\InvalidParameterException
     */
    public function report(string $ip, array|int $categories, string $comment = ''): ResponseObjects\ReportResponse
    {
        foreach ((array) $categories as $cat) {
            if (! in_array($cat, $this->categories)) {
                throw new Exceptions\InvalidParameterException('Individual category must be a valid category.');
            }
        }

        $parameters = ['ip' => $ip, 'categories' => $categories];

        // only send nullable parameters if present
        if ($comment) {
            $parameters['comment'] = $comment;
        }

        $httpResponse = $this->makeRequest('report', $parameters);

        return new ResponseObjects\ReportResponse($httpResponse);
    }

    /**
     * Get the reports for a single IP address (v4 or v6)
     *
     * @throws \AbuseIPDB\Exceptions\InvalidParameterException
     */
    public function reports(string $ipAddress, int $maxAgeInDays = 30, int $page = 1, int $perPage = 25): ReportsPaginatedResponse
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

        $response = $this->makeRequest('reports', [
            'ipAddress' => $ipAddress,
            'maxAgeInDays' => $maxAgeInDays,
            'page' => $page,
            'perPage' => $perPage,
        ]);

        return new ReportsPaginatedResponse($response);
    }

    /**
     * Gets the AbuseIPDB blacklist
     * 
     * @throws \AbuseIPDB\Exceptions\InvalidParameterException
     */
    public function blacklist(int $confidenceMinimum = 100, int $limit = 10000, bool $plaintext = false, $onlyCountries = [], $exceptCountries = [], $ipVersion = null): BlacklistResponse|BlacklistPlaintextResponse
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

        if (isset($ipVersion) && $ipVersion != 4 && $ipVersion != 6) {
            throw new InvalidParameterException('ipVersion must be 4 or 6.');
        }

        $parameters = [
            'confidenceMinimum' => $confidenceMinimum,
            'limit' => $limit,
        ];

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
            $acceptType = 'text/plain';
            $parameters['plaintext'] = $plaintext;
        } else {
            $acceptType = 'application/json';
        }

        $httpResponse = $this->makeRequest('blacklist', $parameters, $acceptType);

        if ($plaintext) {
            return new BlacklistPlaintextResponse($httpResponse);
        } else {
            return new BlacklistResponse($httpResponse);
        }
    }

}
