<?php

namespace AbuseipdbLaravel\ResponseObjects;

use AbuseipdbLaravel\ResponseObjects\AbuseResponse;
use Illuminate\Http\Client\Response as HttpResponse;
use AbuseipdbLaravel\ResponseObjects\ExtraClasses\CheckReport;

class CheckResponse extends AbuseResponse
{

    public string $ipAddress;
    public bool $isPublic;
    public int $ipVersion;
    public bool $isWhitelisted;
    public int $abuseConfidenceScore;
    public string $countryCode;
    public string $usageType;
    public string $isp;
    public string $domain;
    public array $hostnames;
    public bool $isTor;
    public int $totalReports;
    public int $numDistinctUsers;
    public string $lastReportedAt;
    public string $countryName;
    public array $reports = [];

    public function __construct(HTTPResponse $httpResponse)
    {

        parent::__construct($httpResponse);

        $data = $this->object()->data;

        $this->ipAddress = $data -> ipAddress;
        $this->isPublic = $data -> isPublic;
        $this->ipVersion = $data -> ipVersion;
        $this->isWhitelisted = $data -> isWhitelisted;
        $this->abuseConfidenceScore = $data -> abuseConfidenceScore;
        $this->countryCode = $data -> countryCode ?? '';
        $this->usageType = $data -> usageType;
        $this->isp = $data -> isp;
        $this->domain = $data -> domain ?? '';
        $this->hostnames = $data -> hostnames;
        $this->isTor = $data -> isTor;
        $this->totalReports = $data -> totalReports;
        $this->numDistinctUsers = $data -> numDistinctUsers;
        $this->lastReportedAt = $data -> lastReportedAt;

        //if not given because verbose was not passed in, then set these to blank values
        $this->countryName = $data -> countryName ?? '';

        if(isset($data -> reports)){
            foreach($data -> reports as $report){
                array_push($this -> reports, new CheckReport($report));
            }
        }
        


    }
}
