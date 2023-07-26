<?php 

namespace AbuseipdbLaravel\Tests; 

use AbuseipdbLaravel\Tests\TestCase;
use AbuseipdbLaravel\Exceptions;
use AbuseipdbLaravel\Facades\AbuseIPDB;
use AbuseipdbLaravel\AbuseIPDBExceptionReporter;

class TestSuspiciousOperationHandling extends TestCase {

    public function testSuspiciousOperationReportSuccess(){

        //check that the response went through
        $response = AbuseIPDBExceptionReporter::reportSuspiciousOperationException();
        $this -> assertNotEmpty($response);

    }
}