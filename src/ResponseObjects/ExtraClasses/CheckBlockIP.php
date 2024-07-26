<?php

namespace AbuseIPDB\ResponseObjects\ExtraClasses;

use DateTime;

class CheckBlockIP
{
    public string $ipAddress;

    public int $numReports;

    public DateTime $mostRecentReport;

    public ?string $countryCode;

    public int $abuseConfidenceScore;

    public function __construct($ipData)
    {
        $this->ipAddress = $ipData->ipAddress;
        $this->numReports = $ipData->numReports;
        $this->mostRecentReport = DateTime::createFromFormat(DateTime::ATOM, $ipData->mostRecentReport);
        $this->countryCode = $ipData->countryCode ?? null;
        $this->abuseConfidenceScore = $ipData->abuseConfidenceScore;
    }
}
