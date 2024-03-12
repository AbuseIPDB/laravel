<?php

namespace AbuseIPDB\ResponseObjects\ExtraClasses;

class BlacklistedIP
{
    /* properties found in a blacklisted IP */
    public string $ipAddress;

    public string $countryCode;

    public int $abuseConfidenceScore;

    public string $lastReportedAt;

    public function __construct($report)
    {
        /* assign properties from a response's report and places it into object */
        $this->ipAddress = $report->ipAddress;
        $this->countryCode = $report->countryCode;
        $this->abuseConfidenceScore = $report->abuseConfidenceScore;
        $this->lastReportedAt = $report->lastReportedAt;
    }
}
