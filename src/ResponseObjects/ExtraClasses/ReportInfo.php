<?php

namespace AbuseIPDB\ResponseObjects\ExtraClasses;

use DateTime;

class ReportInfo
{
    public DateTime $reportedAt;

    public string $comment;

    public array $categories;

    public int $reporterId;

    public ?string $reporterCountryCode;

    public ?string $reporterCountryName;

    public function __construct($report)
    {
        $this->reportedAt = DateTime::createFromFormat(DateTime::ATOM, $report->reportedAt);
        $this->comment = $report->comment;
        $this->categories = $report->categories;
        $this->reporterId = $report->reporterId;
        $this->reporterCountryCode = $report->reporterCountryCode ?? null;
        $this->reporterCountryName = $report->reporterCountryName ?? null;
    }
}
