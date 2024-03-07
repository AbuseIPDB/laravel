<?php

namespace AbuseIPDB\Providers;

use AbuseIPDB\AbuseIPDBLaravel;
use Illuminate\Support\ServiceProvider;

class AbuseIPDBLaravelServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/abuseipdb.php', 'abuseipdb');
        $this->app->singleton(AbuseIPDBLaravel::class);
    }

    public function provides()
    {
        return [AbuseIPDBLaravel::class];
    }
}
