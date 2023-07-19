<?php 
namespace AbuseipdbLaravel\ResponseObjects\ExtraClasses;

class CheckReport {
    
    public string $reportedAt;
    public string $comment;
    public array $categories;
    public int $reporterId;
    public string $reporterCountryCode;
    public string $reporterCountryName;

    public function __construct($report){

        $this->reportedAt = $report -> reportedAt;
        $this->comment = $report -> comment;
        $this->categories = $report -> categories;
        $this->reporterId = $report -> reporterId;
        $this->reporterCountryCode = $report -> reporterCountryCode;
        $this->reporterCountryName = $report -> reporterCountryName;

    }
}

?>