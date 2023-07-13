<?php 

namespace AbuseipdbLaravel;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class AbuseIPDBLaravel{

    /* function that all requests will be passed through */
    private function makeRequest($endpointName, $requestMethod = null, $parameters, $acceptType = 'application/json') : ?Response{
      
        $specificEndpoint = 'https://api.abuseipdb.com/api/v2/' . $endpointName;

        $client = Http::withHeaders([
            'Accept' => $acceptType,
            'Key' => env('ABUSEIPDB_API_KEY')
        ])->withOptions(['verify' => false]);

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

    public function checkEndpoint($ipAddress, $maxAgeInDays = null, $verbose = null) : ?Response {

        $parameters = ['ipAddress' => $ipAddress];

        //only send nullable parameters if present
        if(isset($maxAgeInDays)){ $parameters['maxAgeInDays'] = $maxAgeInDays; }
        if(isset($verbose)){ $parameters['verbose'] = $verbose; }

       return $this->makeRequest('check', 'get', $parameters);
    }

    public function reportsEndpoint($ipAddress, $maxAgeInDays = null, $page = null, $perPage = null) : ?Response {

        $parameters = ['ipAddress' => $ipAddress];

        //only send nullable parameters if present
        if(isset($maxAgeInDays)){ $parameters['maxAgeInDays'] = $maxAgeInDays; }
        if(isset($page)){ $parameters['page'] = $page; }
        if(isset($perPage)){ $parameters['perPage'] = $perPage; }
        
        return $this->makeRequest('reports', 'get', $parameters);
    }

     public function blackListEndpoint($confidenceMinimun = null, $limit = null, $plaintext = null, $onlyCountries = null, $exceptCountries = null, $ipVersion = null) : ?Response {
        $parameters = [];

         if(isset($confidenceMinimum)){ $parameters['confidenceMinimum'] = $confidenceMinimum; }
        if(isset($limit)){ $parameters['limit'] = $limit; }
        if(isset($plaintext)){ $parameters['plaintext'] = $plaintext; }
        if(isset($onlyCountries)){ $parameters['onlyCountries'] = $onlyCountries; }
        if(isset($exceptCountries)){ $parameters['exceptCountries'] = $exceptCountries; }
        if(isset($ipVersion)){ $parameters['ipVersion'] = $ipVersion; }
 
        return $this->makeRequest('blacklist', 'get', $parameters);

    } 

    public function reportEndpoint($ip, $categories, $comment = null) : ?Response {

        $parameters = ['ip'=> $ip, 'categories' => $categories];

        //only send nullable parameters if present
        if(isset($comment)){
            $parameters['comment'] = $comment;
        }
        return $this->makeRequest('report', 'post', $parameters);
    }

    public function checkBlockEndpoint($network, $maxAgeInDays = null) : ?Response  {

        $parameters = ['network' => $network];

        if(isset($maxAgeInDays)){ $parameters['maxAgeInDays'] = $maxAgeInDays;}

        return $this->makeRequest('check-block', 'get', $parameters);
    }

    public function bulkReportEndpoint($file) : ?Response {

        $parameters = ['multipart' => [
            'name' => 'csv',
            'contents' => fopen($file, 'r')
        ]];

        return $this->makeRequest('bulk-report', 'post', $parameters);
    }

    public function clearAddressEndPoint($ipAddress) : ?Response {

        $parameters = ['ipAddress' => $ipAddress];

        return $this->makeRequest('clear-address', 'delete', $parameters);
    }
}

?>