<?php

namespace AbuseIPDB\ResponseObjects;

use Illuminate\Http\Client\Response as HttpResponse;
use AbuseIPDB\ResponseObjects\ExtraClasses\BlacklistedIP;

class BlacklistResponse extends AbuseResponse
{
    public $blacklistedIPs = [];

    public function __construct(HTTPResponse $httpResponse)
    {
        parent::__construct($httpResponse);

        $data = $this->object()->data;

        foreach ($data as $blacklistedIP) {
            $this->blacklistedIPs[] = new BlacklistedIP($blacklistedIP);
        }
    }
}
