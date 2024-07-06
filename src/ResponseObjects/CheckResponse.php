<?php

namespace AbuseIPDB\ResponseObjects;

use AbuseIPDB\ResponseObjects\ExtraClasses\ReportInfo;
use DateTime;
use Illuminate\Http\Client\Response;

class CheckResponse extends AbuseResponse
{
    public string $ipAddress;

    public bool $isPublic;

    public int $ipVersion;

    public ?bool $isWhitelisted;

    public int $abuseConfidenceScore;

    public ?string $countryCode;

    public string $usageType;

    public string $isp;

    public ?string $domain;

    public array $hostnames;

    public bool $isTor;

    public int $totalReports;

    public int $numDistinctUsers;

    public ?DateTime $lastReportedAt;

    public ?string $countryName;

    /**
     * @var ReportInfo[]
     */
    public array $reports;

    public function __construct(Response $response)
    {
        parent::__construct($response);

        $data = $this->object()->data;

        $this->ipAddress = $data->ipAddress;
        $this->isPublic = $data->isPublic;
        $this->ipVersion = $data->ipVersion;
        $this->isWhitelisted = $data->isWhitelisted;
        $this->abuseConfidenceScore = $data->abuseConfidenceScore;
        $this->countryCode = $data->countryCode ?? null;
        $this->usageType = $data->usageType;
        $this->isp = $data->isp;
        $this->domain = $data->domain ?? null;
        $this->hostnames = $data->hostnames;
        $this->isTor = $data->isTor;
        $this->totalReports = $data->totalReports;
        $this->numDistinctUsers = $data->numDistinctUsers;
        $this->countryName = $data->countryName ?? null;

        $lastReportedAtParsed = DateTime::createFromFormat(DateTime::ATOM, $data->lastReportedAt);
        $this->lastReportedAt = $lastReportedAtParsed ?: null;

        $this->reports = [];
        if (isset($data->reports)) {
            foreach ($data->reports as $report) {
                array_push($this->reports, new ReportInfo($report));
            }
        }
    }
}
