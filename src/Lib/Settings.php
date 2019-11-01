<?php

namespace Riverbedlab\Industrialist\Lib;

use Riverbedlab\Industrialist\Exceptions\BadIdentityProviderKeyException;

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
}
