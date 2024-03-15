<?php

namespace AbuseIPDB\ResponseObjects;

use Illuminate\Http\Client\Response;
use DateTime;

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
