<?php
namespace AbuseipdbLaravel\ResponseObjects;

use Illuminate\Http\Client\Response as HttpResponse;

class AbuseResponse {
    
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
        
        $this->date = $headers["Date"];
        $this->content_type = $headers["Content-Type"]; 
        $this->transfer_encoding = $headers["Transfer-Encoding"];
        $this->connection = $headers["Connection"];
        $this->cache_control = $headers["Cache-Control"];
        $this->x_ratelimit_limit = $headers["X-RateLimit-Limit"];
        $this->x_ratelimit_remaining = $headers["X-RateLimit-Remaining"];
        $this->cf_cache_status = $headers["CF-Cache-Status"];
        $this->report_to = $headers["Report-To"];
        $this->nel = $headers["NEL"]; 
        $this->server = $headers["Server"];
        $this->cf_ray = $headers["CF-RAY"];
        $this->alt_svc = $headers["alt-svc"];
    }
}

?>