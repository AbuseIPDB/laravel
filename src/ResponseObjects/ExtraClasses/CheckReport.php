<?php

namespace AbuseIPDB\ResponseObjects\ExtraClasses;

class CheckReport
{
    /* properties found in a check response reports section */
    public string $reportedAt;

    public string $comment;

    public array $categories;

    public int $reporterId;

    /**
     * @var string|null
     */
    public $reporterCountryCode;

    public string $reporterCountryName;

    public function __construct($report)
    {
        /* assign properties from a response's report and places it into object */
        $this->reportedAt = $report->reportedAt;
        $this->comment = $report->comment;
        $this->categories = $report->categories;
        $this->reporterId = $report->reporterId;
        $this->reporterCountryCode = $report->reporterCountryCode ?? '';
        $this->reporterCountryName = $report->reporterCountryName ?? '';
    }
}
