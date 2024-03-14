<?php

namespace AbuseIPDB\ResponseObjects;

use Illuminate\Http\Client\Response;

class ClearAddressResponse extends AbuseResponse
{
    public int $numReportsDeleted;

    public function __construct(Response $response)
    {
        parent::__construct($response);

        $data = $this->object()->data;

        $this->numReportsDeleted = $data->numReportsDeleted;
    }
}
