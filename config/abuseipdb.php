<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | Specify your AbuseIPDB API key in your env file. You can obtain one by
    | signing up for free at https://www.abuseipdb.com/register.
    |
    */

    'api_key' => env('ABUSEIPDB_API_KEY'),
    'base_url' => env('ABUSEIPDB_API_BASE_URL', 'https://api.abuseipdb.com/api/v2/'),
    'version' => '1.0.10',
];
