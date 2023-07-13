<?php
 namespace AbuseipdbLaravel;

 use Illuminate\Support\Facades\Facade;
 use AbuseipdbLaravel\AbuseIPDBLaravel as AbuseIPDB;

 class AbuseIPDBLaravelFacade extends Facade{

    protected static function getFacadeAccessor(){
        return AbuseIPDB::class;
    }
 }
?>