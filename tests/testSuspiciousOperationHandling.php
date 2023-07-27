<?php 

namespace AbuseIPDB\Tests; 

use AbuseIPDB\Tests\TestCase;
use AbuseIPDB\Exceptions;
use AbuseIPDB\Facades\AbuseIPDB;
use AbuseIPDB\AbuseIPDBExceptionReporter;

class TestSuspiciousOperationHandling extends TestCase {

    public function testSuspiciousOperationReportSuccess(){

        //check that the response went through
        $response = AbuseIPDBExceptionReporter::reportSuspiciousOperationException();
        $this -> assertNotEmpty($response);
    }
}