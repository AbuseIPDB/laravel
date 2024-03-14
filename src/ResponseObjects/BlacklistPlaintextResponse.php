<?php

namespace AbuseIPDB\ResponseObjects;

use Illuminate\Http\Client\Response;

class BlacklistPlaintextResponse extends AbuseResponse
{
    /**
     * @var string[]
     */
    public $blacklistedIPs;

    public function __construct(Response $response)
    {
        parent::__construct($response);

        $textResponse = $this->body();

        $this->blacklistedIPs = explode("\n", $textResponse);
    }
}
