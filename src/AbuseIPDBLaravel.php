<?php

namespace AbuseipdbLaravel;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AbuseIPDBLaravel
{

    private $api_url = 'https://api.abuseipdb.com/api/v2/';
    private $headers = [];

    private $endpoints = [
        'check' => 'get',
        'reports' => 'get',
        'blacklist' => 'get',
        'report' => 'post',
        'check-block' => 'get',
        'bulk-report' => 'post',
        'clear-address' => 'delete',
    ];

    /* function that all requests will be passed through */
    public function makeRequest($endpointName, $parameters, $acceptType = 'application/json'): ?Response
    {

        //check that endpoint passed in exists for the api
        if ($this->endpoints[$endpointName] == null) {
            throw new Exceptions\InvalidEndpointException("Endpoint name given is invalid.");
        }

        $requestMethod = $this->endpoints[$endpointName];

        //check that accept type is application json, or plaintext for blacklist
        if ($acceptType != 'application/json' && ($acceptType == 'text/plain' && $endpoint != 'blacklist')) {
            throw new Exceptions\InvalidAcceptTypeException("Accept Type given may not be used.");
        }

        $this->headers['Accept'] = $acceptType;

        if(env('ABUSEIPDB_API_KEY') != null){
            $this->headers['Key'] = env('ABUSEIPDB_API_KEY');
        }else{
            throw new Exceptions\MissingAPIKeyException("ABUSEIPDB_API_KEY must be set in .env with an AbuseIPBD API key.");
        }
        $client = Http::withHeaders($this->headers);

        //verify false here for local development purposes
        if (env('APP_ENV') == 'local') {
            $client->withOptions(['verify' => false]);
        }

        $response = $client->$requestMethod($this->api_url . $endpointName, $parameters);

        $status = $response->status();

        if ($status == 200) {
            return $response;
        } else {
            $message = "AbuseIPDB: " . $response->object()->errors[0]->detail;
            if ($status == 429) {
                throw new Exceptions\TooManyRequestsException($message);
            }
            if ($status == 402) {
                throw new Exceptions\PaymentRequiredException($message);
            }
            if ($status == 422) {
                throw new Exceptions\UnprocessableContentException($message);
            }
        }
        return null;
    }

    /* makes call to the check endpoint of api */
    public function check($ipAddress, $maxAgeInDays = null, $verbose = null): ResponseObjects\CheckResponse
    {

        if (!isset($ipAddress)) {
            throw new Exceptions\MissingParameterException("ipAddress must be sent with check request.");
        }
        $parameters = ['ipAddress' => $ipAddress];

        //only send nullable parameters if present
        if (isset($maxAgeInDays)) {
            if ($maxAgeInDays >= 1 && $maxAgeInDays <= 365) {
                $parameters['maxAgeInDays'] = $maxAgeInDays;
            } else {
                throw new Exceptions\InvalidParameterException("maxAgeInDays must be between 1 and 365.");
            }

        }
        if (isset($verbose)) {$parameters['verbose'] = $verbose;}

        $httpResponse = $this->makeRequest('check', $parameters);

       // $status = $httpResponse->status();

        return new ResponseObjects\CheckResponse($httpResponse);
        /* else {
          $message = $httpResponse->object()->errors[0]->detail;
            if ($status == 429) {
                throw new Exceptions\TooManyRequestsException($message);
            }
            if ($status == 402) {
                throw new Exceptions\PaymentRequiredException($message);
            }
            if ($status == 422) {
                throw new Exceptions\UnprocessableContentException($message);
            }
        }
 */
        //new ResponseObjects\ErrorResponse($httpResponse);
    }

    /* makes call to report endpoint of api */
    public function report($ip, $categories, $comment = null): ResponseObjects\ReportResponse
    {

        if (!isset($ip)) {
            throw new Exceptions\MissingParameterException("ip must be sent with report request.");
        }
        if (!isset($categories)) {
            throw new Exceptions\MissingParameterException("categories must be sent with check request.");
        }
        /* if(!is_numeric($categories) || $categories > 30 || $categories < 1){
        //return error: categories must be an AbuseIPDB category number between 1 and 30
        }
         */
        $parameters = ['ip' => $ip, 'categories' => $categories];

        //only send nullable parameters if present
        if (isset($comment)) {
            $parameters['comment'] = $comment;
        }

        $httpResponse = $this->makeRequest('report', $parameters);

      //  $status = $httpResponse->status();
       
        return new ResponseObjects\ReportResponse($httpResponse);
         /* else {
            $message = "AbuseIPDB: " . $httpResponse->object()->errors[0]->detail;
            if ($status == 429) {
                throw new Exceptions\TooManyRequestsException($message);
            }
            if ($status == 402) {
                throw new Exceptions\PaymentRequiredException($message);
            }
            if ($status == 422) {
                throw new Exceptions\UnprocessableContentException($message);
            }
        } */

    }

}
