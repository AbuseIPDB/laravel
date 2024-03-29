<?php

namespace AbuseIPDB\ResponseObjects\ExtraClasses;

use DateTime;

class BlacklistedIP
{
    public string $ipAddress;

    public string $countryCode;

    public int $abuseConfidenceScore;

    public DateTime $lastReportedAt;

    public function __construct($ipData)
    {
        $this->ipAddress = $ipData->ipAddress;
        $this->countryCode = $ipData->countryCode;
        $this->abuseConfidenceScore = $ipData->abuseConfidenceScore;
        $this->lastReportedAt = DateTime::createFromFormat(DateTime::ATOM, $ipData->lastReportedAt);
    }
}
