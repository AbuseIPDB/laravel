<?php

namespace AbuseIPDB\ResponseObjects;

use Illuminate\Http\Client\Response;

class AbuseResponse extends Response
{
    public int $x_ratelimit_limit;

    public int $x_ratelimit_remaining;

    public string $content_type;

    public string $cache_control;

    public string $cf_cache_status;

    public function __construct(Response $response)
    {
        parent::__construct($response);

        $this->content_type = $this->header('Content-Type');
        $this->cache_control = $this->header('Cache-Control');
        $this->x_ratelimit_limit = $this->header('X-RateLimit-Limit');
        $this->x_ratelimit_remaining = $this->header('X-RateLimit-Remaining');
        $this->cf_cache_status = $this->header('CF-Cache-Status');
    }
}
