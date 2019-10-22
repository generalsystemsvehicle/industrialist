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
    protected $industrialist_settings = [];
    protected $processedResponse = false;

    /**
     * @var \OneLogin_Saml2_Auth
     */
    protected $auth;

    /**
     * Initializes the SP SAML instance.
     *
     * @param string $idpKey The array key of the Identity Provider you wish to use from the config file.
     *
     * @return Driver
     *
     * @throws BadIdentityProviderKeyException
     */
    public static function create(string $idpKey): Driver
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

        return new static($industrialist_settings);
    }

    /**
     * Initializes the SP SAML instance.
     *
     * @param array $industrialist_settings An array with a definition compatible with OneLogin_Saml_Settings
     *
     */
    public function __construct(array $industrialist_settings)
    {
        $this->industrialist_settings = $industrialist_settings;
        $this->auth = new OneLogin_Saml2_Auth($this->industrialist_settings);
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
    ): OneLogin_Saml2_Utils {
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
     * Processes the response from the remote and generates a user object.
     *
     * @return Riverbedlab\Industrialist\Models\User
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
