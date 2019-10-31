<?php

namespace Riverbedlab\Industrialist\Providers;

use Riverbedlab\Industrialist\Industrialist;
use Riverbedlab\Industrialist\Traits\MergeRecursiveConfigFrom;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    use MergeRecursiveConfigFrom;

    const CONFIG_PATH = __DIR__ . '/../../config/industrialist.php';
    const SP_CONFIG_PATH =
        __DIR__ . '/../../config/defaults/service_provider.php';
    const IDP_CONFIG_PATH =
        __DIR__ . '/../../config/defaults/identity_provider.php';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes(
            [
                self::CONFIG_PATH => config_path('industrialist.php')
            ],
            'config'
        );

        foreach (config('industrialist.identity_providers') as $key => $idp) {
            $this->mergeRecursiveConfigFrom(
                self::IDP_CONFIG_PATH,
                'industrialist.identity_providers.' . $key . '.settings'
            );
        }
        $this->mergeRecursiveConfigFrom(
            self::SP_CONFIG_PATH,
            'industrialist.sp'
        );
        $this->mergeConfigFrom(self::CONFIG_PATH, 'industrialist');
    }

    /**
     * Register bindings in the container. Merges configuration sources.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('industrialist', function () {
            return new Industrialist();
        });
    }
}
