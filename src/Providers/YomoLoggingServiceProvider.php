<?php

namespace Yomo7\Logging\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class YomoLoggingServiceProvider extends ServiceProvider
{
    public function register()
    {

    }

    public function boot(Request $request)
    {
        $this->publishes([
            realpath(dirname(__FILE__) . '/../../config/logging.php') => config_path('logging.php'),
        ]);

        $this->app['config']->set('logging', require realpath(dirname(__FILE__) . '/../../config/logging.php'));
    }
}