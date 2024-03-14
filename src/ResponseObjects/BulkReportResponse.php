<?php

namespace AbuseIPDB\ResponseObjects;

use AbuseIPDB\ResponseObjects\ExtraClasses\BulkInvalidReport;
use Illuminate\Http\Client\Response;

class BulkReportResponse extends AbuseResponse
{
    public int $savedReports;

    /**
     * @var BulkInvalidReport[]
     */
    public array $invalidReports;

    public function __construct(Response $response)
    {
        parent::__construct($response);

        $data = $this->object()->data;

        $this->savedReports = $data->savedReports;

        $this->invalidReports = [];
        if (isset($data->invalidReports)) {
            foreach ($data->invalidReports as $invalidReport) {
                array_push($this->invalidReports, new BulkInvalidReport($invalidReport));
            }
        }
    }
}
