<?php

namespace AbuseIPDB\Facades;

use AbuseIPDB\ResponseObjects\CheckResponse;
use AbuseIPDB\ResponseObjects\ReportResponse;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Facade;
use AbuseIPDB\AbuseIPDBLaravel;

/**
 * @method static Response|null makeRequest($endpointName, $parameters, $acceptType = 'application/json')
 * @method static CheckResponse check(string $ipAddress, int $maxAgeInDays = 30, bool $verbose = false)
 * @method static ReportResponse report(string $ip, array|int $categories, string $comment = '')
 *
 * @see AbuseIPDBLaravel
 */
class AbuseIPDB extends Facade
{
    protected static function getFacadeAccessor()
    {
        return AbuseIPDBLaravel::class;
    }
}
