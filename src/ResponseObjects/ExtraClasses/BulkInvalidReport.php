<?php

namespace AbuseIPDB\ResponseObjects\ExtraClasses;

class BulkInvalidReport
{
    public string $error;

    public string $input;

    public int $rowNumber;

    public function __construct($invalidReport)
    {
        $this->error = $invalidReport->error;
        $this->input = $invalidReport->input;
        $this->rowNumber = $invalidReport->rowNumber;
    }
}
