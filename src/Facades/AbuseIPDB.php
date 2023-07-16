<?php
 namespace AbuseipdbLaravel\Facades;

 use Illuminate\Support\Facades\Facade;
 use AbuseipdbLaravel\AbuseIPDBLaravel;

 class AbuseIPDB extends Facade{

    protected static function getFacadeAccessor(){
        return AbuseIPDBLaravel::class;
    }
 }
?>