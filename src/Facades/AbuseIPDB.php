<?php

namespace AbuseIPDB\Facades;

use AbuseIPDB\AbuseIPDBLaravel;
use Illuminate\Support\Facades\Facade;

class AbuseIPDB extends Facade
{
    protected static function getFacadeAccessor()
    {
        return AbuseIPDBLaravel::class;
    }
}
