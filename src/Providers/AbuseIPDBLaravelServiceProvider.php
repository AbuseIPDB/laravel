<?php 

namespace AbuseIPDB\Providers;

use Illuminate\Support\ServiceProvider;
use AbuseIPDB\AbuseIPDBLaravel;

class AbuseIPDBLaravelServiceProvider extends ServiceProvider{

    public function boot(){

    }

    public function register(){

        $this->app->singleton(AbuseIPDBLaravel::class);
        
    }

    public function provides(){
        return [AbuseIPDBLaravel::class];
    }

}

?>