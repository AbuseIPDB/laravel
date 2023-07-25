<?php
namespace AbuseipdbLaravel\Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected $enablesPackageDiscoveries = true;
    protected $client; //to be used for mock guzzlehttp instance

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

    public function setUp(){
        parent::setUp();
        $this->mockClient();
    }

    protected function mockClient(){
        $this->client = Mockery::mock(\GuzzleHttp\Client::class);
        $this->app->instance(\GuzzleHttp\Client::class, $this->client);

    }

}
