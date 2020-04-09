<?php

namespace GeneralSystemsVehicle\Industrialist\Exceptions;

use Exception;

class BadIdentityProviderKeyException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'The identity provider key is not found in the config.';
}
