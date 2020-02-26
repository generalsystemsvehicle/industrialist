<?php

namespace GeneralSystemsVehicle\Industrialist\Contracts;

use OneLogin\Saml2\Utils as OneLogin_Saml2_Utils;
use OneLogin\Saml2\Error as OneLogin_Saml2_Error;
use GeneralSystemsVehicle\Industrialist\Models\User;

interface Driver
{
    /**
     * Initializes the SP SAML instance.
     *
     * @param string $idpKey The array key of the Identity Provider you wish to use from the config file.
     *
     * @return Driver
     */
    public static function create(string $idpKey): Driver;

    /**
     * Initiates the drivers logout process
     */
    public function logout();

    /**
     * Processes the configuration file for the driver and produces XML metadata
     *
     * @return mixed xml string metadata
     */
    public function metadata();

    /**
     * Process the SAML Logout Response / Logout Request sent by the IdP.
     *
     * @param bool        $keepLocalSession             When false will destroy the local session, otherwise will keep it
     * @param string|null $requestId                    The ID of the LogoutRequest sent by this SP to the IdP
     * @param bool        $retrieveParametersFromServer True if we want to use parameters from $_SERVER to validate the signature
     * @param Callable    $cbDeleteSession              Callback to be executed to delete session
     * @param bool        $stay                         True if we want to stay (returns the url string) False to redirect
     *
     * @return string|null
     *
     * @throws OneLogin_Saml2_Error
     */
    public function processLogout(
        bool $keepLocalSession = false,
        ?string $requestId = null,
        bool $retrieveParametersFromServer = false,
        ?callable $cbDeleteSession = null,
        bool $stay = false
    );

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
    ): ?string;

    /**
     * Processes the response from the remote and generates a user object.
     *
     * @return User
     */
    public function user();
}
