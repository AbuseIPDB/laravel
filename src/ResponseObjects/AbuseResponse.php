<?php
namespace AbuseipdbLaravel\ResponseObjects;

use Illuminate\Http\Client\Response as HttpResponse;

class AbuseResponse extends HttpResponse {
    
    /* these properties are headers that can be specifically useful for AbuseIPDB information
    These will be accessible by the child objects
    */
    public int $x_ratelimit_limit;
    public int $x_ratelimit_remaining;
    public string $content_type;
    public string $cache_control;
    public string $cf_cache_status;

    public function __construct(HttpResponse $httpResponse){

    parent::__construct($httpResponse);

        /* grabbing and assigning headers directly from the request */
        $this->content_type = $this->header("Content-Type"); 
        $this->cache_control = $this->header("Cache-Control");
        $this->x_ratelimit_limit = $this->header("X-RateLimit-Limit");
        $this->x_ratelimit_remaining = $this->header("X-RateLimit-Remaining");
        $this->cf_cache_status = $this->header("CF-Cache-Status");
    }

}

?>