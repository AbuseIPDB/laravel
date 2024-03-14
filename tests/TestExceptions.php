<?php

namespace AbuseIPDB\Tests;

use AbuseIPDB\Exceptions;
use AbuseIPDB\Facades\AbuseIPDB;

class TestExceptions extends TestCase
{
    public function testInvalidParameter()
    {
        $this->expectException(Exceptions\InvalidParameterException::class);
        AbuseIPDB::check('127.0.0.1', 367);
    }

    public function testTooManyRequests()
    {
        $this->expectException(Exceptions\TooManyRequestsException::class);
        AbuseIPDB::report('127.0.0.2', 21);
        AbuseIPDB::report('127.0.0.2', 21);
    }
}
