<?php
namespace AbuseIPDB\Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected $enablesPackageDiscoveries = true;
    
    //register packages to the test class
    protected function getPackageProviders($app){
        return [
            'AbuseIPDB\Providers\AbuseIPDBLaravelServiceProvider',
        ];
    }

    //register aliases to the test classes
    protected function getPackageAliases($app){
        return [
            'AbuseIPDB' => 'AbuseIPDB\Facades\AbuseIPDB',
        ];
    }

    public function setUp() : void{
        parent::setUp();
    }

}
