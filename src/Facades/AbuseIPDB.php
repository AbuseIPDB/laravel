<?php
 namespace AbuseIPDB\Facades;

 use Illuminate\Support\Facades\Facade;
 use AbuseIPDB\AbuseIPDBLaravel;

 class AbuseIPDB extends Facade{

    protected static function getFacadeAccessor(){
        return AbuseIPDBLaravel::class;
    }
 }
?>