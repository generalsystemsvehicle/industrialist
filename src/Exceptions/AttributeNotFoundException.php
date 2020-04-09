<?php

namespace GeneralSystemsVehicle\Industrialist\Exceptions;

use Exception;

class AttributeNotFoundException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'The attribute requested does not exist.';
}
