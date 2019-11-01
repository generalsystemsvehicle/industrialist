<?php

namespace Riverbedlab\Industrialist\Lib;

use OneLogin\Saml2\Auth as OneLogin_Saml2_Auth;
use Riverbedlab\Industrialist\Contracts\Auth;
use Riverbedlab\Industrialist\Contracts\Driver;

class OneLoginAuth extends OneLogin_Saml2_Auth implements Auth
{
    /**
     * Processes the configuration file for the driver and produces XML metadata
     *
     * @return mixed xml string metadata
     */
    public function metadata()
    {
        return $this->getSettings()->getSPMetadata();
    }
}
