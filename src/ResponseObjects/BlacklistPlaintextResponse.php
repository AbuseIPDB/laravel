<?php

namespace AbuseIPDB\ResponseObjects;

use DateTime;
use Illuminate\Http\Client\Response;

class BlacklistPlaintextResponse extends AbuseResponse
{
    /**
     * @var string[]
     */
    public $blacklistedIPs;

    public DateTime $generatedAt;

    public function __construct(Response $response)
    {
        parent::__construct($response);

        $textResponse = $this->body();

        $this->generatedAt = DateTime::createFromFormat(DateTime::ATOM, $this->header('X-Generated-At'));

        $this->blacklistedIPs = explode("\n", $textResponse);
    }
}
