<?php

namespace GeneralSystemsVehicle\Industrialist\Tests;

use GeneralSystemsVehicle\Industrialist\Drivers\Saml2;
use GeneralSystemsVehicle\Industrialist\Exceptions\BadIdentityProviderKeyException;
use GeneralSystemsVehicle\Industrialist\Facades\Industrialist as IndustrialistFacade;
use GeneralSystemsVehicle\Industrialist\Providers\ServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('industrialist', include app_path('../../../../../tests/fixtures/config.php'));
    }

    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'industrialist' => IndustrialistFacade::class,
        ];
    }

    public function testDriverWithBadKey()
    {
        $this->expectException(BadIdentityProviderKeyException::class);
        IndustrialistFacade::driver('foo');
    }

    public function testDriverWithGoodKey()
    {
        $driver = IndustrialistFacade::driver('my_idp_key');
        $this->assertEquals($driver instanceof Saml2, true);
    }
}
