<?php

namespace Riverbedlab\Industrialist\Drivers;

use OneLogin\Saml2\Auth as OneLogin_Saml2_Auth;
use OneLogin\Saml2\Error as OneLogin_Saml2_Error;
use OneLogin\Saml2\Utils as OneLogin_Saml2_Utils;
use Riverbedlab\Industrialist\Contracts\Driver;
use Riverbedlab\Industrialist\Exceptions\BadIdentityProviderKeyException;
use Riverbedlab\Industrialist\Models\User;

class Saml2 implements Driver
{
    /**
     * @var OneLogin_Saml2_Auth
     */
    protected $auth;

    protected $processedResponse = false;

    /**
     * Initializes the SP SAML instance.
     *
     * @param OneLogin_Saml2_Auth $auth A configured auth instance setup for the desired IdP
     *
     */
    public function __construct(OneLogin_Saml2_Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Initializes the SP SAML instance.
     *
     * @param string $idpKey The array key of the Identity Provider you wish to use from the config file.
     *
     * @return Driver
     */
    public static function create(string $idpKey): Driver
    {
        // dd(static::createSettings($idpKey));
        $auth = new OneLogin_Saml2_Auth(static::createSettings($idpKey));

        return new static($auth);
    }

    /**
     * Initializes the SP SAML settings.
     *
     * @param string $idpKey The array key of the Identity Provider you wish to use from the config file.
     *
     * @return Array
     *
     * @throws BadIdentityProviderKeyException
     */
    public static function createSettings(string $idpKey): array
    {
        $industrialist_settings = config('industrialist');
        $idp_config_path = "industrialist.identity_providers.{$idpKey}";
        $idp_settings = config($idp_config_path);

        if (!$idp_settings) {
            throw new BadIdentityProviderKeyException();
        }

        $industrialist_settings['idp'] = $idp_settings;

        if ($idp_sp_settings = config($idp_config_path . '.sp')) {
            $industrialist_settings['sp'] = array_replace_recursive(
                $industrialist_settings['sp'],
                $idp_sp_settings
            );
        }

        return $industrialist_settings;
    }

    /**
     * Getter for processedResponse attribute
     *
     * @return boolean
     */
    public function getProcessedResponse()
    {
        return $this->processedResponse;
    }

    /**
     * Redirects to the Identity Providers logout endpoint
     *
     */
    public function logout()
    {
        return $this->auth->logout();
    }

    /**
     * Processes the response from the remote and generates a user object.
     *
     * @return mixed xml string metadata
     */
    public function metadata()
    {
        return $this->auth->getSettings()->getSPMetadata();
    }

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
    ) {
        return $this->auth->processSLO(
            $keepLocalSession,
            $requestId,
            $retrieveParametersFromServer,
            $cbDeleteSession,
            $stay
        );
    }

    /**
     * Process the saml response using the underlying OneLogin functionality.
     */
    public function processResponse()
    {
        if ($this->processedResponse === false) {
            $this->auth->processResponse();
            $this->processedResponse = true;
        }
    }

    /**
     * Start a saml2 login, redirecting to the IdP configured on this instance.
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
    ): ?string {
        return $this->auth->login(
            $returnTo,
            $parameters,
            $forceAuthn,
            $isPassive,
            $stay,
            $setNameIdPolicy,
            $nameIdValueReq
        );
    }

    /**
     * Processes the response from the remote and generates a user object.
     *
     * @return User
     */
    public function user()
    {
        $this->processResponse();
        $user = new User();
        $user->setNameId($this->auth->getNameId());
        $user->setNameIdFormat($this->auth->getNameIdFormat());
        $user->setIsAuthenticated($this->auth->isAuthenticated());
        $user->setSessionIndex($this->auth->getSessionIndex());
        $user->setSessionExpiration($this->auth->getSessionExpiration());
        $user->setErrors($this->auth->getErrors());
        $user->setErrorReason($this->auth->getLastErrorReason());
        $user->setLastRequestId($this->auth->getLastRequestID());
        $user->setLastRequest($this->auth->getLastRequestXML());
        $user->setLastResponse($this->auth->getLastResponseXML());
        $user->setAttributes($this->auth->getAttributes());
        $user->setAttributesWithFriendlyName(
            $this->auth->getAttributesWithFriendlyName()
        );

        return $user;
    }
}
