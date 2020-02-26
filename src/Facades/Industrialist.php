<?php

namespace GeneralSystemsVehicle\Industrialist\Facades;

use Illuminate\Support\Facades\Facade;

class Industrialist extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'industrialist';
    }
}
