<?php

namespace Riverbedlab\Industrialist\Drivers;

use Riverbedlab\Industrialist\Contracts\Auth;
use Riverbedlab\Industrialist\Contracts\Driver;
use Riverbedlab\Industrialist\Exceptions\ProcessingResponseFailedException;
use Riverbedlab\Industrialist\Lib\OneLoginAuth;
use Riverbedlab\Industrialist\Lib\Settings;
use Riverbedlab\Industrialist\Models\User;

class Saml2 implements Driver
{
    protected $auth;

    /**
     * Initializes the SP SAML instance.
     *
     * @param Auth $auth A configured auth instance setup for the desired IdP
     *
     */
    public function __construct(Auth $auth)
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
        $settings = Settings::create($idpKey);
        $auth = new OneLoginAuth($settings);
        return new static($auth);
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
        return $this->auth->metadata();
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
     * throw new AttributeNotFoundException();
     */
    public function user()
    {
        $this->auth->processResponse();

        if (count($this->auth->getErrors()) !== 0) {
            throw new ProcessingResponseFailedException(
                $this->auth->getLastErrorReason()
            );
        }

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
