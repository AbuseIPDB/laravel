<?php

namespace AbuseIPDB\Tests;

use AbuseIPDB\Facades\AbuseIPDB;
use AbuseIPDB\ResponseObjects;

class TestRequests extends TestCase
{
    public function testIlluminateResponseType()
    {
        $response = AbuseIPDB::makeRequest('check', ['ipAddress' => '127.0.0.1']);
        $this->assertInstanceOf(\Illuminate\Http\Client\Response::class, $response);
    }

    public function testCheckResponseType()
    {
        $response = AbuseIPDB::check('127.0.0.1');
        $this->assertInstanceOf(ResponseObjects\CheckResponse::class, $response);
    }

    public function testAbuseResponseProperties()
    {
        $response = AbuseIPDB::check('127.0.0.1');
        $this->assertObjecthasProperty('x_ratelimit_limit', $response);
        $this->assertObjecthasProperty('x_ratelimit_remaining', $response);
        $this->assertObjecthasProperty('content_type', $response);
        $this->assertObjecthasProperty('cache_control', $response);
        $this->assertObjecthasProperty('cf_cache_status', $response);
    }

    public function testCheckResponseProperties()
    {
        $response = AbuseIPDB::check('127.0.0.1');
        $this->assertObjectHasProperty('ipAddress', $response);
        $this->assertObjectHasProperty('isPublic', $response);
        $this->assertObjectHasProperty('ipVersion', $response);
        $this->assertObjectHasProperty('isWhitelisted', $response);
        $this->assertObjectHasProperty('abuseConfidenceScore', $response);
        $this->assertObjectHasProperty('countryCode', $response);
        $this->assertObjectHasProperty('usageType', $response);
        $this->assertObjectHasProperty('isp', $response);
        $this->assertObjectHasProperty('domain', $response);
        $this->assertObjectHasProperty('hostnames', $response);
        $this->assertObjectHasProperty('isTor', $response);
        $this->assertObjectHasProperty('totalReports', $response);
        $this->assertObjectHasProperty('numDistinctUsers', $response);
        $this->assertObjectHasProperty('lastReportedAt', $response);
        $this->assertObjectHasProperty('countryName', $response);
        $this->assertObjectHasProperty('reports', $response);
    }

    public function testCheckResponseWithoutVerbose()
    {
        $response = AbuseIPDB::check(env('BAD_IP_TO_TEST'));
        $this->assertEmpty($response->reports);
        $this->assertEmpty($response->countryName);
    }

    // testing with real ip, testing with 127.0.0.1 will not have a countryName
    public function testCheckResponseWithVerbose()
    {
        $response = AbuseIPDB::check(env('BAD_IP_TO_TEST'), verbose: 1);
        $this->assertNotEmpty($response->reports);
        $this->assertNotEmpty($response->countryName);
    }

    // array used to ensure that ipAddresses are not reused for different report endpoint tests
    public $testIPAddresses = [
        '127.0.0.4',
        '127.0.0.5',
    ];

    public function testReportResponseType()
    {
        $response = AbuseIPDB::report($this->testIPAddresses[0], 21);
        $this->assertInstanceOf(ResponseObjects\ReportResponse::class, $response);
    }

    public function testReportResponseProperties()
    {
        $response = AbuseIPDB::report($this->testIPAddresses[1], 21);
        $this->assertObjectHasProperty('ipAddress', $response);
        $this->assertObjectHasProperty('abuseConfidenceScore', $response);
    }

    public function testReportsPaginatedResponseType(): void
    {
        $response = AbuseIPDB::reports(env('BAD_IP_TO_TEST'));
        $this->assertInstanceOf(ResponseObjects\ReportsPaginatedResponse::class, $response);
    }

    public function testReportsPaginatedResultsType(): void
    {
        $response = AbuseIPDB::reports(env('BAD_IP_TO_TEST'));

        $this->assertContainsOnlyInstancesOf(
            ResponseObjects\ExtraClasses\ResultReports::class,
            $response->results
        );
    }

    public function testBlacklistResponseType(): void
    {
        $response = AbuseIPDB::blacklist();
        $this->assertInstanceOf(ResponseObjects\BlacklistResponse::class, $response);
    }

    public function testBlacklistResultsType(): void
    {
        $response = AbuseIPDB::blacklist();

        $this->assertContainsOnlyInstancesOf(
            ResponseObjects\ExtraClasses\BlacklistedIP::class,
            $response->blacklistedIPs
        );
    }
}
