<?php

namespace Riverbedlab\Industrialist\Tests;

use Riverbedlab\Industrialist\Drivers\Saml2;
use Riverbedlab\Industrialist\Exceptions\BadIdentityProviderKeyException;
use Riverbedlab\Industrialist\Facades\Industrialist as IndustrialistFacade;
use Riverbedlab\Industrialist\Providers\ServiceProvider;
use Orchestra\Testbench\TestCase;

class Industrialist extends TestCase
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
