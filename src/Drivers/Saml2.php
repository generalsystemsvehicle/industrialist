<?php

namespace GeneralSystemsVehicle\Industrialist\Drivers;

use GeneralSystemsVehicle\Industrialist\Contracts\Auth;
use GeneralSystemsVehicle\Industrialist\Contracts\Driver;
use GeneralSystemsVehicle\Industrialist\Exceptions\ProcessingResponseFailedException;
use GeneralSystemsVehicle\Industrialist\Lib\OneLoginAuth;
use GeneralSystemsVehicle\Industrialist\Lib\Settings;
use GeneralSystemsVehicle\Industrialist\Models\User;

class Saml2 implements Driver
{
    /**
     * @var Auth
     */
    protected $auth;

    /**
     * Initializes the SP SAML instance.
     *
     * @param Auth $auth A configured auth instance setup for the desired IdP
     *
     */
    final public function __construct(Auth $auth)
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
     * @return  string|null
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
    ): ?string {
        $result = $this->auth->processSLO(
            $keepLocalSession,
            $requestId,
            $retrieveParametersFromServer,
            $cbDeleteSession,
            $stay
        );
        return is_string($result) ? $result : null;
    }

    /**
     * Start a saml2 login, redirecting to the IdP configured on this instance.
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

        $errors = $this->auth->getErrors();
        if (count((array) $errors) !== 0) {
            $lastError = $this->auth->getLastErrorReason();
            $errorMessage = '';
            if (is_string($lastError)) {
                $errorMessage = $lastError;
            }
            throw new ProcessingResponseFailedException($errorMessage);
        }

        $user = new User();
        
        $nameId = $this->auth->getNameId();
        $user->setNameId(is_string($nameId) ? $nameId : null);
        
        $nameIdFormat = $this->auth->getNameIdFormat();
        $user->setNameIdFormat(is_string($nameIdFormat) ? $nameIdFormat : null);
        
        $user->setIsAuthenticated((bool) $this->auth->isAuthenticated());
        
        $sessionIndex = $this->auth->getSessionIndex();
        $user->setSessionIndex(is_string($sessionIndex) ? $sessionIndex : null);
        
        $sessionExpiration = $this->auth->getSessionExpiration();
        $user->setSessionExpiration(is_int($sessionExpiration) ? $sessionExpiration : null);
        
        $user->setErrors((array) $errors);
        
        $errorReason = $this->auth->getLastErrorReason();
        $user->setErrorReason(is_string($errorReason) ? $errorReason : null);
        
        $lastRequestId = $this->auth->getLastRequestID();
        $user->setLastRequestId(is_string($lastRequestId) ? $lastRequestId : null);
        
        $lastRequest = $this->auth->getLastRequestXML();
        $user->setLastRequest(is_string($lastRequest) ? $lastRequest : null);
        
        $lastResponse = $this->auth->getLastResponseXML();
        $user->setLastResponse(is_string($lastResponse) ? $lastResponse : null);
        
        $attributes = $this->auth->getAttributes();
        $user->setAttributes($attributes);
        
        $attributesWithFriendlyName = $this->auth->getAttributesWithFriendlyName();
        $user->setAttributesWithFriendlyName($attributesWithFriendlyName);

        return $user;
    }
}
