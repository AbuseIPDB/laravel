<?php

namespace AbuseIPDB\ResponseObjects;

use AbuseIPDB\ResponseObjects\ExtraClasses\BlacklistedIP;
use DateTime;
use Illuminate\Http\Client\Response;

class BlacklistResponse extends AbuseResponse
{
    /**
     * @var BlacklistedIP[]
     */
    public $blacklistedIPs;

    public DateTime $generatedAt;

    public function __construct(Response $response)
    {
        parent::__construct($response);

        $fullObject = $this->object();
        $data = $fullObject->data;
        $meta = $fullObject->meta;

        $this->generatedAt = DateTime::createFromFormat(DateTime::ATOM, $meta->generatedAt);

        $this->blacklistedIPs = [];
        foreach ($data as $blacklistedIP) {
            $this->blacklistedIPs[] = new BlacklistedIP($blacklistedIP);
        }
    }
}
