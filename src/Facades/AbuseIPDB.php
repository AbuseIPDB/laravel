<?php

namespace AbuseIPDB\Facades;

use AbuseIPDB\AbuseIPDBLaravel;
use Illuminate\Support\Facades\Facade;
use Datetime;

/**
 * @method static CheckResponse check(string $ipAddress, int $maxAgeInDays = 30, bool $verbose = false)
 * @method static ReportResponse report(string $ip, array|int $categories, string $comment = null, DateTime $timestamp = null)
 * @method static ReportsPaginatedResponse reports(string $ipAddress, int $maxAgeInDays = 30, int $page = 1, int $perPage = 25)
 * @method static BlacklistResponse|BlacklistPlaintextResponse blacklist(int $confidenceMinimum = 100, int $limit = 10000, bool $plaintext = false, $onlyCountries = [], $exceptCountries = [], int $ipVersion = null)
 * @method static CheckBlockResponse checkBlock(string $network, int $maxAgeInDays = 30)
 * @method static BulkReportResponse bulkReport(string $csvFileContents)
 * @method static ClearAddressResponse clearAddress(string $ip)
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
