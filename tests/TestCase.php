<?php
namespace AbuseipdbLaravel\Tests;

use Mockery;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected $enablesPackageDiscoveries = true;
    
    //register packages to the test class
    protected function getPackageProviders($app){
        return [
            'AbuseipdbLaravel\Providers\AbuseIPDBLaravelServiceProvider',
        ];
    }

    //register aliases to the test classes
    protected function getPackageAliases($app){
        return [
            'AbuseIPDB' => 'AbuseipdbLaravel\Facades\AbuseIPDB',
        ];
    }

    public function setUp() : void{
        parent::setUp();
    }

}
