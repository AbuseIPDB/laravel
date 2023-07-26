<?php 

namespace AbuseipdbLaravel\ResponseObjects;

use AbuseipdbLaravel\ResponseObjects\AbuseResponse;
use Illuminate\Http\Client\Response as HttpResponse;


class ReportResponse extends AbuseResponse{
    
    /* properties that reflect the data from the report response */
    public string $ipAddress;
    public int $abuseConfidenceScore; 

    public function __construct(HttpResponse $httpResponse){

        //construct instance of the parent
        parent::__construct($httpResponse);

        //grab data from the request body
        $data = $this -> object()->data;

        //assign respective properties from request body to the object
        $this -> ipAddress = $data->ipAddress;
        $this -> abuseConfidenceScore = $data->abuseConfidenceScore; 
    }
}