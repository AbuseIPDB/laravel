<?php

namespace AbuseIPDB\ResponseObjects;

use AbuseIPDB\ResponseObjects\ExtraClasses\ReportInfo;
use Illuminate\Http\Client\Response;

class ReportsPaginatedResponse extends AbuseResponse
{
    public int $total;

    public int $page;

    public int $count;

    public int $perPage;

    public int $lastPage;

    public ?string $nextPageUrl;

    public ?string $previousPageUrl;

    /**
     * @var ReportInfo[]
     */
    public array $results;

    public function __construct(Response $response)
    {
        parent::__construct($response);

        $data = $this->object()->data;

        $this->total = $data->total;
        $this->page = $data->page;
        $this->perPage = $data->perPage;
        $this->lastPage = $data->lastPage;
        $this->nextPageUrl = $data->nextPageUrl;
        $this->previousPageUrl = $data->previousPageUrl;

        $this->results = [];
        foreach ($data->results as $result) {
            array_push($this->results, new ReportInfo($result));
        }
    }
}
