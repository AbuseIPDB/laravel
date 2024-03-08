<?php

namespace AbuseIPDB\Tests;

use AbuseIPDB\AbuseIPDBExceptionReporter;

class TestSuspiciousOperationHandling extends TestCase
{
    public function testSuspiciousOperationReportSuccess()
    {
        // check that the response went through
        $response = AbuseIPDBExceptionReporter::reportSuspiciousOperationException();
        $this->assertNotEmpty($response);
    }
}
