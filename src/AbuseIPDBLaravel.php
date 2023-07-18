<?php 

namespace AbuseipdbLaravel;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

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

        if($this->endpoints[$endpointName] == null){
            //return error
        }
        else if ($this->endpoints[$endpointName] != $adjustedRequestMethod){
            //return error
        }

        $this->headers['Accept'] = $acceptType;
        $this->headers['Key'] = env('ABUSEIPDB_API_KEY');

        $specificEndpoint = $this->api_url . $endpointName;

        //verify false only here for local development purpose
        $client = Http::withHeaders($this->headers)->withOptions(['verify' => false]);

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

    /* makes call to the reports endpoint of api */
    public function reports($ipAddress, $maxAgeInDays = null, $page = null, $perPage = null) : ?Response {

        $parameters = ['ipAddress' => $ipAddress];

        //only send nullable parameters if present
        if(isset($maxAgeInDays)){ $parameters['maxAgeInDays'] = $maxAgeInDays; }
        if(isset($page)){ $parameters['page'] = $page; }
        if(isset($perPage)){ $parameters['perPage'] = $perPage; }
        
        return $this->makeRequest('reports', 'get', $parameters);
    }

    /* makes call to the blacklist endpoint of api */
     public function blackList($plaintext = null, $confidenceMinimun = null, $limit = null, $onlyCountries = null, $exceptCountries = null, $ipVersion = null) : ?Response {
        $parameters = [];

        if(isset($confidenceMinimum)){ $parameters['confidenceMinimum'] = $confidenceMinimum; }
        if(isset($limit)){ $parameters['limit'] = $limit; }
        if(isset($onlyCountries)){ $parameters['onlyCountries'] = $onlyCountries; }
        if(isset($exceptCountries)){ $parameters['exceptCountries'] = $exceptCountries; }
        if(isset($ipVersion)){ $parameters['ipVersion'] = $ipVersion; }
 
        return isset($plaintext) ?  $this->makeRequest('blacklist', 'get', $parameters, 'text/plain') : $this->makeRequest('blacklist', 'get', $parameters);
    
    } 

    /* makes call to report endpoint of api */
    public function report($ip, $categories, $comment = null) : ?Response {

        $parameters = ['ip'=> $ip, 'categories' => $categories];

        //only send nullable parameters if present
        if(isset($comment)){
            $parameters['comment'] = $comment;
        }
        return $this->makeRequest('report', 'post', $parameters);
    }

    /* makes call to check-block endpoint of api */
    public function checkBlock($network, $maxAgeInDays = null) : ?Response  {

        $parameters = ['network' => $network];

        if(isset($maxAgeInDays)){ $parameters['maxAgeInDays'] = $maxAgeInDays;}

        return $this->makeRequest('check-block', 'get', $parameters);
    }

    /* makes call to bulk-report endpoint of api */
    public function bulkReport($file) : ?Response {

        $parameters = ['multipart' => [
            'name' => 'csv',
            'contents' => fopen($file, 'r')
        ]];

        return $this->makeRequest('bulk-report', 'post', $parameters);
    }

    /* makes call to clear-address endpoint of api */
    public function clearAddress($ipAddress) : ?Response {

        $parameters = ['ipAddress' => $ipAddress];

        return $this->makeRequest('clear-address', 'delete', $parameters);
    }
}

?>