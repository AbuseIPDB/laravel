<?php 

namespace AbuseipdbLaravel\Tests; 

use AbuseipdbLaravel\Tests\TestCase;
use AbuseipdbLaravel\Exceptions;
use AbuseipdbLaravel\Facades\AbuseIPDB;
use AbuseipdbLaravel\AbuseIPDBExceptionReporter;

class TestSuspiciousOperationHandling extends TestCase {

    public function testSuspiciousOperationReportSuccess(){
        $response = AbuseIPDBExceptionReporter::reportSuspiciousOperationException();
        $this -> assertEquals(200, $response->status());

    }

    public function testSuspiciousOperationReportFailure(){
        $this -> expectException(Exceptions\TooManyRequestsException::class);
        $response = AbuseIPDBExceptionReporter::reportSuspiciousOperationException();
    }
}