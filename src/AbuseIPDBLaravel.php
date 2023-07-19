<?php 

namespace AbuseipdbLaravel;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use AbuseipdbLaravel\ResponseObjects\ReportResponse;

class AbuseIPDBLaravel {

    private $api_url = 'https://api.abuseipdb.com/api/v2/';
    private $headers = [];

    private $endpoints = [
    'check' => 'get', 
    'reports' => 'get', 
    'blacklist' =>'get',
    'report' => 'post',
    'check-block' => 'get',
    'bulk-report' => 'post',
    'clear-address' => 'delete',
    ];

    /* function that all requests will be passed through */
    public function makeRequest($endpointName, $requestMethod = null, $parameters, $acceptType = 'application/json') : ?Response{
       
        //convert request method to all lowercase, in case method is spelled with different case conventions
        $adjustedRequestMethod = strtolower($requestMethod);

        //check that endpoint passed in exists for the api
        if($this->endpoints[$endpointName] == null){
            //return error
        }

        //check that the request method passed in matches method for the endpoint
        if ($this->endpoints[$endpointName] != $adjustedRequestMethod){
            //return error
        }

        //check that accept type is application json, or plaintext for blacklist 
        if($acceptType != 'application/json' && ($acceptType == 'text/plain' && $endpoint != 'blacklist')){
            //return error
        }


        $this->headers['Accept'] = $acceptType;
        $this->headers['Key'] = env('ABUSEIPDB_API_KEY');

        $specificEndpoint = $this->api_url . $endpointName;

        $client = Http::withHeaders($this->headers);

        //verify false here for local development purposes
        if(env('APP_ENV') == 'local'){
            $client->withOptions(['verify' => false]);
        }

        if($requestMethod == 'post'){
            return $client->post($specificEndpoint, $parameters);
        }
        else if($requestMethod == 'get'){
            return $client->get($specificEndpoint, $parameters);
        } 
        else if ($requestMethod == 'delete'){
            return $client->delete($specificEndpoint, $parameters);
        }
        else {
            return null;
        }

    }

    /* makes call to the check endpoint of api */
    public function check($ipAddress, $maxAgeInDays = null, $verbose = null) : ?Response {

        $parameters = ['ipAddress' => $ipAddress];

        //only send nullable parameters if present
        if(isset($maxAgeInDays)){ $parameters['maxAgeInDays'] = $maxAgeInDays; }
        if(isset($verbose)){ $parameters['verbose'] = $verbose; }

       return $this->makeRequest('check', 'get', $parameters);
    }

    /* makes call to report endpoint of api */
    public function report($ip, $categories, $comment = null) : ResponseObjects\ReportResponse | ResponseObjects\ErrorResponse {

        $parameters = ['ip'=> $ip, 'categories' => $categories];

        //only send nullable parameters if present
        if(isset($comment)){
            $parameters['comment'] = $comment;
        }
        $httpResponse = $this->makeRequest('report', 'post', $parameters);

        return $httpResponse -> status() == 200 ? 
        new ResponseObjects\ReportResponse($httpResponse) :
        new ResponseObjects\ErrorResponse($httpResponse);
        

    }

}

?>