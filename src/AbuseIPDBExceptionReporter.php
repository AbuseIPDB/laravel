<?php

namespace AbuseipdbLaravel;

use AbuseidbLaravel\Exceptions\TooManyRequestsException;
use AbuseipdbLaravel\Facades\AbuseIPDB;

class AbuseIPDBExceptionReporter
{

    public static function reportSuspiciousOperationException(): ?\ResponseObjects\ReportResponse
    {
        //grab the ip address from the request, which is deemed suspicious by the exception being thrown
        $attackingAddress = request()->ip();

        /* Extract request body info to add to the report comment */
        $params = "";
        foreach (request()->all() as $param => $value) {
            $params .= print_r($param, true) . ": " . print_r($value, true) . "\n";
        }

        //Formulate the comment to be sent to api
        $comment = "Suspicious Operation. Request content:\n" . $params;

        /* send the report in a try-catch, so if a 429 is encountered it will not throw an instrusive error */
        try {
            $response = AbuseIPDB::report(ip: $attackingAddress, categories: 21, comment: $comment);
            return $response; //return the response if it goes through
        } catch (TooManyRequestsException $e) {
            /* This will neglect the exception by default
            If you would like to implement any logging you may do so here
            */
        }
        return null;
    }
}
