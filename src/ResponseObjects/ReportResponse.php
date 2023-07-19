<?php 

namespace AbuseipdbLaravel\ResponseObjects;

use AbuseipdbLaravel\ResponseObjects\AbuseResponse;
use Illuminate\Http\Client\Response as HttpResponse;


class ReportResponse extends AbuseResponse{
    
    protected string $ipAddress;
    protected int $abuseConfidenceScore; 

    public function __construct(HttpResponse $httpResponse){

        parent::__construct($httpResponse);

        $data = $httpResponse -> object();
        dd($data);

        $this -> ipAddress = $data->ipAddress;
        $this -> abuseConfidenceScore = $data->abuseConfidenceScore; 
    }
}