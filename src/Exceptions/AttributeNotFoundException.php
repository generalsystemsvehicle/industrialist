<?php

namespace Riverbedlab\Industrialist\Exceptions;

use Exception;

class AttributeNotFoundException extends Exception
{
    protected $message = 'The attribute requested does not exist.';
}
