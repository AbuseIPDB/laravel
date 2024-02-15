<?php

namespace AbuseIPDB\Tests;

use AbuseIPDB\Exceptions;
use AbuseIPDB\Facades\AbuseIPDB;

class TestExceptions extends TestCase
{
    /* accept type text/html will throw errors */
    public function testInvalidAcceptType()
    {
        $this->expectException(Exceptions\InvalidAcceptTypeException::class);
        AbuseIPDB::makeRequest('check', [], acceptType: 'text/html');
    }

    /* endpoint name 'reporting'will throw errors */
    public function testInvalidEndpoint()
    {
        $this->expectException(Exceptions\InvalidEndpointException::class);
        AbuseIPDB::makeRequest('reporting', []);
    }

    /* max age in days of 367 will throw error */
    public function testInvalidParameter()
    {
        $this->expectException(Exceptions\InvalidParameterException::class);
        AbuseIPDB::check('127.0.0.1', 367);
    }

    public function testTooManyRequests()
    {

        $this->expectException(Exceptions\TooManyRequestsException::class);
        //double reporting 127.0.0.1 within 15 minutes should not be allowed, will throw error
        AbuseIPDB::report('127.0.0.2', 21);
        AbuseIPDB::report('127.0.0.2', 21);

    }

    public function testUnprocessableContent()
    {
        $this->expectException(Exceptions\UnprocessableContentException::class);
        //category of 31 will throw an unprocessable error
        AbuseIPDB::makeRequest('report', ['ip' => '127.0.0.1', 'categories' => [21, 31]]);
    }
}
