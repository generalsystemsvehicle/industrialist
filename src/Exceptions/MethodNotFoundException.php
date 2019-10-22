<?php

namespace Riverbedlab\Industrialist\Exceptions;

use Exception;

class MethodNotFoundException extends Exception
{
    protected $message = 'The method called does not exist.';
}