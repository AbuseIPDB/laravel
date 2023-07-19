<?php 
namespace AbuseipdbLaravel\ResponseObjects\ExtraClasses;

class CheckReport {
    public string $reportedAt;
    public string $comment;
    public array $categories;
    public int $reporterId;
    public string $reporterCountryCode;
    public string $reporterCountryName;

    public function __construct($props){
        
    }
}

?>