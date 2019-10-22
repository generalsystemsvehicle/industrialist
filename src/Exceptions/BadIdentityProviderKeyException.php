<?php

namespace Riverbedlab\Industrialist\Exceptions;

use Exception;

class BadIdentityProviderKeyException extends Exception
{
    protected $message = 'The identity provider key is not found in the config.';
}
