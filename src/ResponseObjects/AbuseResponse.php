<?php
namespace AbuseipdbLaravel\ResponseObjects;

use Illuminate\Http\Client\Response as HttpResponse;

class AbuseResponse {
    
    protected int $status_code;
    protected string $reason;
    
    //to store information from the request headers
    protected $date;
    protected $content_type; 
    protected $transfer_encoding;
    protected $connection;
    protected $cache_control;
    protected $x_ratelimit_limit;
    protected $x_ratelimit_remaining;
    protected $cf_cache_status;
    protected $report_to;
    protected $nel; 
    protected $server;
    protected $cf_ray;
    protected $alt_svc; 

    

    public function __construct(HttpResponse $httpResponse){

        $headers = $httpResponse -> headers();
        
        $this -> status_code = $httpResponse -> status();
        $this -> reason = $httpResponse -> reason();

        $this->date = $headers["Date"][0];
        $this->content_type = $headers["Content-Type"][0]; 
        $this->transfer_encoding = $headers["Transfer-Encoding"][0];
        $this->connection = $headers["Connection"][0];
        $this->cache_control = $headers["Cache-Control"][0];
        $this->x_ratelimit_limit = $headers["X-RateLimit-Limit"][0];
        $this->x_ratelimit_remaining = $headers["X-RateLimit-Remaining"][0];
        $this->cf_cache_status = $headers["CF-Cache-Status"][0];
        $this->report_to = $headers["Report-To"][0];
        $this->nel = $headers["NEL"][0]; 
        $this->server = $headers["Server"][0];
        $this->cf_ray = $headers["CF-RAY"][0];
        $this->alt_svc = $headers["alt-svc"][0];
    }
}

?>