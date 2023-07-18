<?php

namespace AbuseipdbLaravel;

use AbuseipdbLaravel\Facades\AbuseIPDB;

class AbuseIPDBExceptionReporter { 

public static function reportSuspiciousOperationException() : void {
    $attackingAddress = request()->ip();
    $params = "";
    foreach (request()->all() as $param => $value) {
        $params .= print_r($param, true) . ": " . print_r($value, true) . "\n";
    }
    $comment = "Suspicious Operation. Request content:\n" . $params;

    $response = AbuseIPDB::report(ip: $attackingAddress, categories: 21, comment: $comment);
    //dd($response);

}

} 

?>