<?php

namespace GeneralSystemsVehicle\Industrialist\Contracts;

interface Auth
{
    /**
     * Initiates the SSO process.
     *
     * @param string|null $returnTo The target URL the user should be returned to after login.
     * @param array<string, mixed> $parameters Extra parameters to be added to the GET
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

    /**
     * Process the SAML Logout Response / Logout Request sent by the IdP.
     *
     * @param bool        $keepLocalSession             When false will destroy the local session, otherwise will keep it
     * @param string|null $requestId                    The ID of the LogoutRequest sent by this SP to the IdP
     * @param bool        $retrieveParametersFromServer True if we want to use parameters from $_SERVER to validate the signature
     * @param callable|null $cbDeleteSession            Callback to be executed to delete session
     * @param bool        $stay                         True if we want to stay (returns the url string) False to redirect
     *
     * @return string|null
     */
    public function processSLO($keepLocalSession = false, $requestId = null, $retrieveParametersFromServer = false, $cbDeleteSession = null, $stay = false);

    /**
     * Process the SAML AuthnResponse sent by the IdP
     *
     * @param string|null $requestId The ID of the AuthnRequest sent by this SP to the IdP
     *
     * @return void
     */
    public function processResponse($requestId = null);

    /**
     * Returns if there were any error
     *
     * @return array<string, mixed>
     */
    public function getErrors();

    /**
     * Returns the reason for the last error
     *
     * @return string|null
     */
    public function getLastErrorReason();

    /**
     * Returns the nameID
     *
     * @return string|null
     */
    public function getNameId();

    /**
     * Returns the nameID Format
     *
     * @return string|null
     */
    public function getNameIdFormat();

    /**
     * Returns if user is authenticated
     *
     * @return bool
     */
    public function isAuthenticated();

    /**
     * Returns the SessionIndex
     *
     * @return string|null
     */
    public function getSessionIndex();

    /**
     * Returns the SessionNotOnOrAfter
     *
     * @return int|null
     */
    public function getSessionExpiration();

    /**
     * Returns the ID of the last request
     *
     * @return string|null
     */
    public function getLastRequestID();

    /**
     * Returns the most recently-constructed/processed XML SAML request
     *
     * @return string|null
     */
    public function getLastRequestXML();

    /**
     * Returns the most recently-constructed/processed XML SAML response
     *
     * @return string|null
     */
    public function getLastResponseXML();

    /**
     * Returns the set of SAML attributes
     *
     * @return array<string, mixed>
     */
    public function getAttributes();

    /**
     * Returns the set of SAML attributes indexed by FriendlyName
     *
     * @return array<string, mixed>
     */
    public function getAttributesWithFriendlyName();
}
