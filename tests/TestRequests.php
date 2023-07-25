<?php

namespace AbuseipdbLaravel\Tests;

use AbuseipdbLaravel\Tests\TestCase;
use Illuminate\Http\Client\Response;
use AbuseipdbLaravel\ResponseObjects;
use AbuseipdbLaravel\Facades\AbuseIPDB;

class TestRequests extends TestCase
{

    public function testIlluminateResponseType()
    {
        $response = AbuseIPDB::makeRequest('check', ['ipAddress'=>'127.0.0.1']);
        $this->assertInstanceOf(\Illuminate\Http\Client\Response::class,$response);
    }
    public function testCheckResponseType()
    {
        $response = AbuseIPDB::check('127.0.0.1');
        $this->assertInstanceOf(ResponseObjects\CheckResponse::class,$response);
    }
    public function testReportResponseType()
    {
        $response = AbuseIPDB::report('127.0.0.2', 21);
        $this->assertInstanceOf(ResponseObjects\ReportResponse::class,$response);
    }
}
