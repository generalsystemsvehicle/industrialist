<?php

namespace Riverbedlab\Industrialist\Contracts;

use OneLogin\Saml2\Utils as OneLogin_Saml2_Utils;

interface Driver
{
    /**
     * Initializes the SP SAML instance.
     *
     * @param string $idpKey The array key of the Identity Provider you wish to use from the config file.
     *
     * @return Driver
     *
     * @throws BadIdentityProviderKeyException
     */
    public static function create(string $idpKey): Driver;

    /**
     * Initializes the Driver instance.
     *
     * @param array $industrialist_settings An array configuring the driver appropriately for it's type
     *
     */
    public function __construct(array $industrialist_settings);

    /**
     * Start a driver login, redirecting to the authority configured on this instance.
     *
     * @param string|null $returnTo The target URL the user should be returned to after login.
     * @param array $parameters Extra parameters to be added to the GET
     * @param bool $forceAuthn When true the AuthNRequest will set the ForceAuthn='true'
     * @param bool $isPassive When true the AuthNRequest will set the Ispassive='true'
     * @param bool $stay True if we want to stay (returns the url string) False to redirect
     * @param bool $setNameIdPolicy When true the AuthNRueqest will set a nameIdPolicy element
     * @param string $nameIdValueReq Indicates to the IdP the subject that should be authenticated
     *
     * @return string|null If $stay is True, it return a string with the SLO URL + LogoutRequest + parameters
     */
    public function redirect(
        ?string $returnTo = null,
        array $parameters = [],
        bool $forceAuthn = false,
        bool $isPassive = false,
        bool $stay = false,
        bool $setNameIdPolicy = true,
        ?string $nameIdValueReq = null
    ): OneLogin_Saml2_Utils;
}
