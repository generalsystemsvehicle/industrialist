<?php

namespace Riverbedlab\Samltron\Facades;

use Illuminate\Support\Facades\Facade;

class Samltron extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'samltron';
    }
}
