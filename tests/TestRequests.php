<?php

namespace AbuseIPDB\Tests;

use AbuseIPDB\Facades\AbuseIPDB;
use AbuseIPDB\ResponseObjects;

class TestRequests extends TestCase
{
    public function testCheckResponseType()
    {
        $response = AbuseIPDB::check('127.0.0.1');
        $this->assertInstanceOf(ResponseObjects\CheckResponse::class, $response);
    }

    public function testMultipleCheckResponses()
    {
        $response = AbuseIPDB::check('127.0.0.1');
        $this->assertInstanceOf(ResponseObjects\CheckResponse::class, $response);
        $response = AbuseIPDB::check('127.0.0.2');
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

    public function testCheckResponseWithVerbose()
    {
        $response = AbuseIPDB::check(env('BAD_IP_TO_TEST'), verbose: 1, maxAgeInDays: 365);
        $this->assertNotEmpty($response->reports);
        $this->assertNotEmpty($response->countryName);
    }

    public function testReportResponseType()
    {
        $response = AbuseIPDB::report('127.0.0.4', 21);
        $this->assertInstanceOf(ResponseObjects\ReportResponse::class, $response);
    }

    public function testReportResponseProperties()
    {
        $response = AbuseIPDB::report('127.0.0.5', 21);
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
            ResponseObjects\ExtraClasses\ReportInfo::class,
            $response->results
        );
    }

    public function testBlacklistResponseType(): void
    {
        $response = AbuseIPDB::blacklist(limit: 20);
        $this->assertInstanceOf(ResponseObjects\BlacklistResponse::class, $response);
    }

    public function testBlacklistResultsType(): void
    {
        $response = AbuseIPDB::blacklist(limit: 20);
        $this->assertContainsOnlyInstancesOf(
            ResponseObjects\ExtraClasses\BlacklistedIP::class,
            $response->blacklistedIPs
        );
    }

    public function testCheckBlockResponseType(): void
    {
        $response = AbuseIPDB::checkBlock('127.0.0.1/28');
        $this->assertInstanceOf(ResponseObjects\CheckBlockResponse::class, $response);
    }

    public function testBulkReport(): void
    {
        $csvData = "IP,Categories,ReportDate,Comment\n127.0.0.1,\"18,22\",2018-01-01T10:00:01-04:00,\"Failed password\"";
        $response = AbuseIPDB::bulkReport($csvData);
        $this->assertInstanceOf(ResponseObjects\BulkReportResponse::class, $response);
    }

    public function testClearAddress(): void
    {
        AbuseIPDB::report('127.0.0.7', 21);
        $response = AbuseIPDB::clearAddress('127.0.0.7');
        $this->assertInstanceOf(ResponseObjects\ClearAddressResponse::class, $response);
    }

    public function testReportParams(): void
    {
        $response = AbuseIPDB::report('127.0.0.8', [18, 22], 'Bad IP', new \DateTime());
        $this->assertInstanceOf(ResponseObjects\ReportResponse::class, $response);
    }
}
