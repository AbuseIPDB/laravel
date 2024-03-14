<?php

namespace AbuseIPDB\ResponseObjects;

use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Support\Collection;

class BlacklistPlaintextResponse extends AbuseResponse
{
    public $blacklistedIPs;

    public function __construct(HTTPResponse $httpResponse)
    {
        parent::__construct($httpResponse);

        $textResponse = $this->body();

        $this->blacklistedIPs = new Collection(explode("\n", $textResponse));
    }
}
