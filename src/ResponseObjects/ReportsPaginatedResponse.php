<?php

namespace AbuseIPDB\ResponseObjects;

use AbuseIPDB\ResponseObjects\ExtraClasses\ResultReports;
use Illuminate\Http\Client\Response;

class ReportsPaginatedResponse extends AbuseResponse
{
    public int $total;
    public int $page;
    public int $count;
    public int $perPage;
    public int $lastPage;
    public string $nextPageUrl;
    public ?string $previousPageUrl;

    /**
     * @var ResultReports[]
     */
    public array $results;

    public function __construct(Response $response)
    {
        parent::__construct($response);

        $responseData = $response->json('data');

        $this->total = $responseData['total'];
        $this->page = $responseData['page'];
        $this->perPage = $responseData['perPage'];
        $this->lastPage = $responseData['lastPage'];
        $this->nextPageUrl = $responseData['nextPageUrl'];
        $this->previousPageUrl = $responseData['previousPageUrl'];

        $this->results = array_map(
            fn($result) => new ResultReports($result),
            $responseData['results']
        );
    }
}
