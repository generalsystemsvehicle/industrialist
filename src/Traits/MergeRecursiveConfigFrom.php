<?php

namespace GeneralSystemsVehicle\Industrialist\Traits;

use Arr;

trait MergeRecursiveConfigFrom
{
    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param  string  $path
     * @param  string  $key
     * @return void
     */
    protected function mergeRecursiveConfigFrom($path, $key)
    {
        if (!$this->app->configurationIsCached()) {
            $config = (array) config($key, []);
            $merging = require $path;
            $merged = array_replace_recursive($merging, $config);
            config([$key => $merged]);
        }
    }
}
