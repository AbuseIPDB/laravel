<?php

namespace AbuseIPDB;

use AbuseIPDB\Facades\AbuseIPDB;

class AbuseIPDBExceptionReporter
{
    /**
     * Report a suspicious operation to AbuseIPDB, gathering information from the current request
     */
    public static function reportSuspiciousOperationException(): ResponseObjects\ReportResponse|false
    {
        $attackingAddress = request()->ip();

        $params = '';
        foreach (request()->all() as $param => $value) {
            $params .= print_r($param, true).': '.print_r($value, true)."\n";
        }

        $comment = "Suspicious Operation. Request content:\n".$params;

        try {
            $response = AbuseIPDB::report(ip: $attackingAddress, categories: 21, comment: $comment);

            return $response;
        } catch (Exceptions\TooManyRequestsException $e) {
            // Do nothing
        } catch (\Exception $e) {
            // Do nothing
        }

        return false;
    }
}
