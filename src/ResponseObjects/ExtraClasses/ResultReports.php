<?php

namespace AbuseIPDB\ResponseObjects\ExtraClasses;

class ResultReports
{
    public string $reportedAt;
    public string $comment;
    public array $categories;
    public int $reporterId;
    public string $reporterCountryCode;
    public string $reporterCountryName;

    public function __construct(array $result)
    {
        $this->reportedAt = $result['reportedAt'];
        $this->comment = $result['comment'];
        $this->categories = $result['categories'];
        $this->reporterId = $result['reporterId'];
        $this->reporterCountryCode = $result['reporterCountryCode'];
        $this->reporterCountryName = $result['reporterCountryName'];
    }
}