<?php

namespace AbuseIPDB\ResponseObjects;

use AbuseIPDB\ResponseObjects\ExtraClasses\CheckBlockIP;
use Illuminate\Http\Client\Response;

class CheckBlockResponse extends AbuseResponse
{
    public string $networkAddress;

    public string $netmask;

    public string $minAddress;

    public string $maxAddress;

    public int $numPossibleHosts;

    public string $addressSpaceDesc;

    /**
     * @var CheckBlockIP[]
     */
    public $reportedAddress;

    public function __construct(Response $response)
    {
        parent::__construct($response);

        $data = $this->object()->data;

        $this->networkAddress = $data->networkAddress;
        $this->netmask = $data->netmask;
        $this->minAddress = $data->minAddress;
        $this->maxAddress = $data->maxAddress;
        $this->numPossibleHosts = $data->numPossibleHosts;
        $this->addressSpaceDesc = $data->addressSpaceDesc;

        $this->reportedAddress = [];
        if (isset($data->reportedAddress)) {
            foreach ($data->reportedAddress as $IPinfo) {
                array_push($this->reportedAddress, new CheckBlockIP($IPinfo));
            }
        }
    }
}
