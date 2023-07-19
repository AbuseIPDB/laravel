<?php 

namespace AbuseipdbLaravel\ResponseObjects;

use AbuseipdbLaravel\ResponseObjects\AbuseResponse;
use Illuminate\Http\Client\Response as HttpResponse;


class ReportResponse extends AbuseResponse{
    
    public string $ipAddress;
    public int $abuseConfidenceScore; 

    public function __construct(HttpResponse $httpResponse){

        parent::__construct($httpResponse);

        $data = $this -> object()->data;

        $this -> ipAddress = $data->ipAddress;
        $this -> abuseConfidenceScore = $data->abuseConfidenceScore; 
    }
}