<?php

namespace AbuseIPDB\Tests;

use AbuseIPDB\AbuseIPDBExceptionReporter;
use Illuminate\Http\Request;

class TestSuspiciousOperationHandling extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $request = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.6']);
        $this->app->instance('request', $request);
    }

    public function testSuspiciousOperationReportSuccess()
    {
        $response = AbuseIPDBExceptionReporter::reportSuspiciousOperationException();
        $this->assertNotEmpty($response);
    }
}
