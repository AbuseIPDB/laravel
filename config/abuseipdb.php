<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | Specify your AbuseIPDB API key here.
    |
    */
    'api_key' => env('ABUSEIPDB_API_KEY'),
    'base_url' => env('ABUSEIPDB_API_BASE_URL', 'https://api.abuseipdb.com/api/v2/'),
    'version' => 'dev-main',
];
