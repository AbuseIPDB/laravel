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
    public function makeRequest($endpointName, $parameters, $acceptType = 'application/json') : ?Response{
       
        //check that endpoint passed in exists for the api
        if($this->endpoints[$endpointName] == null){
            //return error
        } 

        $requestMethod = $this->endpoints[$endpointName];


        //check that accept type is application json, or plaintext for blacklist 
        if($acceptType != 'application/json' && ($acceptType == 'text/plain' && $endpoint != 'blacklist')){
            //return error
        }

        $this->headers['Accept'] = $acceptType;
        $this->headers['Key'] = env('ABUSEIPDB_API_KEY');

        $client = Http::withHeaders($this->headers);

        //verify false here for local development purposes
        if(env('APP_ENV') == 'local'){
            $client->withOptions(['verify' => false]);
        }
     
        return $client->$requestMethod($this->api_url . $endpointName, $parameters);

    }

    /* makes call to the check endpoint of api */
    public function check($ipAddress, $maxAgeInDays = null, $verbose = null) : ResponseObjects\CheckResponse | ResponseObjects\ErrorResponse {

        if(!isset($ipAddress)){
            //return error: ipAddress must be sent with check request
        }
        $parameters = ['ipAddress' => $ipAddress];

        //only send nullable parameters if present
        if(isset($maxAgeInDays)){ $parameters['maxAgeInDays'] = $maxAgeInDays; }
        if(isset($verbose)){ $parameters['verbose'] = $verbose; }

        $httpResponse = $this->makeRequest('check', $parameters);

        return $httpResponse -> status() == 200 ? 
        new ResponseObjects\CheckResponse($httpResponse) :
        new ResponseObjects\ErrorResponse($httpResponse);
    }
    

    /* makes call to report endpoint of api */
    public function report($ip, $categories, $comment = null) : ResponseObjects\ReportResponse | ResponseObjects\ErrorResponse {

        if(!isset($ip)){ 
            //return error: ip must be sent with request
        }
        if(!isset($categories)){
            //return error: categories must be sent with request
        }
        if(!is_numeric($categories) || $categories > 30 || $categories < 1){
            //return error: categories must be an AbuseIPDB category number between 1 and 30 
        }

        $parameters = ['ip'=> $ip, 'categories' => $categories];
        
        //only send nullable parameters if present
        if(isset($comment)){
            $parameters['comment'] = $comment;
        }
        $httpResponse = $this->makeRequest('report', $parameters);

        return $httpResponse -> status() == 200 ? 
        new ResponseObjects\ReportResponse($httpResponse) :
        new ResponseObjects\ErrorResponse($httpResponse);
    
    }

}

?>