<?php

namespace AbuseIPDB\ResponseObjects;

use AbuseIPDB\ResponseObjects\ExtraClasses\BlacklistedIP;
use Illuminate\Http\Client\Response;
use DateTime;

class BlacklistResponse extends AbuseResponse
{
    /**
     * @var BlacklistedIP[]
     */
    public $blacklistedIPs;

    public Datetime $generatedAt;

    public function __construct(Response $response)
    {
        parent::__construct($response);

        $fullObject = $this->object();
        $data = $fullObject->data;
        $meta = $fullObject->meta;

        $this->generatedAt = DateTime::createFromFormat(DateTime::ATOM, $meta->generatedAt);

        $this->blacklistedIPs = [];
        foreach ($data as $blacklistedIP) {
            array_push($this->blacklistedIPs, new BlacklistedIP($blacklistedIP));
        }
    }
}
