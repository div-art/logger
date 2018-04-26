<?php

namespace DivArt\Logger;

use Illuminate\Support\ServiceProvider;

class LoggerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/logger.php', 'path'
        );

        $this->publishes([
            __DIR__ . '/config' => config_path(),
        ]);

        $this->loadViewsFrom(
            __DIR__. '/views/', 'div-art'
        );

        $this->loadRoutesFrom(
            __DIR__ . '/web/routes.php'
        );
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('logger', function () {
            return new Logger;
        });
    }
}
