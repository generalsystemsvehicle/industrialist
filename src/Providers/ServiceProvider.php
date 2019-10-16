<?php

namespace Riverbedlab\Industrialist\Providers;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/industrialist.php';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('industrialist.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'industrialist'
        );

        $this->app->bind('industrialist', function () {
            return new Industrialist();
        });
    }
}
