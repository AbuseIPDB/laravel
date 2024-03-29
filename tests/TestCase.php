<?php

namespace AbuseIPDB\Tests;

use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected $enablesPackageDiscoveries = true;

    // register packages to the test class
    protected function getPackageProviders($app)
    {
        return [
            'AbuseIPDB\Providers\AbuseIPDBLaravelServiceProvider',
        ];
    }

    // register aliases to the test classes
    protected function getPackageAliases($app)
    {
        return [
            'AbuseIPDB' => 'AbuseIPDB\Facades\AbuseIPDB',
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->useEnvironmentPath(__DIR__.'/..');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);
        parent::getEnvironmentSetUp($app);
    }
}
