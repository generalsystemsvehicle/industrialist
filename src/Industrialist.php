<?php

namespace GeneralSystemsVehicle\Industrialist;

use GeneralSystemsVehicle\Industrialist\Drivers\Saml2;
use GeneralSystemsVehicle\Industrialist\Contracts\Driver;

class Industrialist
{
    /**
     * Instances the appropriate auth driver for the provided key
     *
     * @param string $key The array key of the Identity Provider you wish to use from the config file.
     *
     * @return Driver
     */
    public function driver(string $key): Driver
    {
        return Saml2::create($key);
    }
}
