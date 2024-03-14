<?php

namespace AbuseIPDB\ResponseObjects\ExtraClasses;

class BlacklistedIP
{
    public string $ipAddress;

    public string $countryCode;

    public int $abuseConfidenceScore;

    public string $lastReportedAt;

    public function __construct($report)
    {
        $this->ipAddress = $report->ipAddress;
        $this->countryCode = $report->countryCode;
        $this->abuseConfidenceScore = $report->abuseConfidenceScore;
        $this->lastReportedAt = $report->lastReportedAt;
    }
}
