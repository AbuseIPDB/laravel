<?php

namespace AbuseIPDB\ResponseObjects;

use Illuminate\Http\Client\Response;

class ReportResponse extends AbuseResponse
{
    public string $ipAddress;

    public int $abuseConfidenceScore;

    public function __construct(Response $response)
    {
        parent::__construct($response);

        $data = $this->object()->data;

        $this->ipAddress = $data->ipAddress;
        $this->abuseConfidenceScore = $data->abuseConfidenceScore;
    }
}
