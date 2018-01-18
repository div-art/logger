<?php


namespace Divart\Logger;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Route;

class LoggerServiceProvider extends ServiceProvider{


    /**
     * Register any package services.
     *
     * @return void
     */

    public function register()
    {
        $this->app->bind('logger', function ($app)
        {
            return new Logger;
        });
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/Http/routes.php');
        $this->loadViewsFrom(__DIR__.'/views', 'logger');
        $this->publishes([__DIR__.'/views' => resource_path('views/logger')], 'logger-views');
        $this->publishes([__DIR__ . "/config/config.php" => config_path('logger.php')], 'logger-config');
        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'logger');
    }

}