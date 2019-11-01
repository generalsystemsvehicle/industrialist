<?php

namespace Riverbedlab\Industrialist\Contracts;

interface Auth
{
    /**
     * Initiates the drivers login process
     */
    public function login();

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
}
