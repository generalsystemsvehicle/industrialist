<?php

namespace GeneralSystemsVehicle\Industrialist\Lib;

use GeneralSystemsVehicle\Industrialist\Exceptions\BadIdentityProviderKeyException;

class Settings
{
    /**
     * Initializes the SP SAML settings.
     *
     * @param string $idpKey The array key of the Identity Provider you wish to use from the config file.
     *
     * @return Array
     *
     * @throws BadIdentityProviderKeyException
     */
    public static function create(string $idpKey): array
    {
        $industrialist_settings = (array) config('industrialist');
        $idp_config_path = "industrialist.identity_providers.{$idpKey}";
        $idp_settings = config($idp_config_path . '.idp');
        $sp_settings = config($idp_config_path . '.sp');

        if (!$idp_settings) {
            throw new BadIdentityProviderKeyException();
        }

        $industrialist_settings['idp'] = $idp_settings;
        $industrialist_settings['sp'] = $sp_settings;
        unset($industrialist_settings['identity_providers']);

        return $industrialist_settings;
    }
}
