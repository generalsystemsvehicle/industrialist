<?php

namespace GeneralSystemsVehicle\Industrialist\Contracts;

interface Auth
{
    /**
     * Initiates the SSO process.
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
    public function login($returnTo = null, array $parameters = array(), $forceAuthn = false, $isPassive = false, $stay = false, $setNameIdPolicy = true, $nameIdValueReq = null);

    /**
     * Initiates the drivers logout process
     *
     * @return  string|null
     */
    public function logout();

    /**
     * Processes the configuration file for the driver and produces XML metadata
     *
     * @return mixed xml string metadata
     */
    public function metadata();
}
