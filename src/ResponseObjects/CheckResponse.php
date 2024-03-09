<?php

namespace AbuseIPDB\ResponseObjects;

use AbuseIPDB\ResponseObjects\ExtraClasses\CheckReport;
use Illuminate\Http\Client\Response as HttpResponse;

class CheckResponse extends AbuseResponse
{
    /* these properties reflect the body of a response from the check endpoint */
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
        // construct new instance of the parent object
        parent::__construct($httpResponse);

        // grab the body of the response as an object
        $data = $this->object()->data;

        // assign respective properties from response to the object
        $this->ipAddress = $data->ipAddress;
        $this->isPublic = $data->isPublic;
        $this->ipVersion = $data->ipVersion;
        $this->isWhitelisted = $data->isWhitelisted ?? false;
        $this->abuseConfidenceScore = $data->abuseConfidenceScore;
        $this->countryCode = $data->countryCode ?? '';
        $this->usageType = $data->usageType ?? '';
        $this->isp = $data->isp;
        $this->domain = $data->domain ?? '';
        $this->hostnames = $data->hostnames;
        $this->isTor = $data->isTor;
        $this->totalReports = $data->totalReports;
        $this->numDistinctUsers = $data->numDistinctUsers;
        $this->lastReportedAt = $data->lastReportedAt ?? '';

        // if not given because verbose was not passed in, then set these to blank values
        $this->countryName = $data->countryName ?? '';

        /*if there are reports, meaning that the verbose parameter was set for the request,
        then loop through the reports and create new instances of the report object for each individual report,
        then add it to the array of reports accesible by the CheckResponse object
        */
        if (isset($data->reports)) {
            foreach ($data->reports as $report) {
                array_push($this->reports, new CheckReport($report));
            }
        }
    }
}
