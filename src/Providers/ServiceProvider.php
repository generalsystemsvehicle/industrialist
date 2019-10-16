<?php

namespace Riverbedlab\Samltron\Providers;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__ . '/../config/samltron.php';

    public function boot()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('samltron.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'samltron'
        );

        $this->app->bind('samltron', function () {
            return new Samltron();
        });
    }
}
