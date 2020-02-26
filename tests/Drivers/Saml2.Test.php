<?php

namespace GeneralSystemsVehicle\Industrialist\Tests\Drivers;

use Carbon\Carbon;
use DOMDocument;
use OneLogin\Saml2\Utils as OneLogin_Saml2_Utils;
use Orchestra\Testbench\TestCase;
use GeneralSystemsVehicle\Industrialist\Drivers\Saml2;
use GeneralSystemsVehicle\Industrialist\Exceptions\BadIdentityProviderKeyException;
use GeneralSystemsVehicle\Industrialist\Exceptions\ProcessingResponseFailedException;
use GeneralSystemsVehicle\Industrialist\Lib\OneLoginAuth;
use GeneralSystemsVehicle\Industrialist\Lib\Settings;
use GeneralSystemsVehicle\Industrialist\Providers\ServiceProvider;

class Saml2Test extends TestCase
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
        $app['config']->set(
            'industrialist',
            include app_path('../../../../../tests/fixtures/config.php')
        );
    }

    protected function createAuthMock($methodOverrides = [])
    {
        $settings = Settings::create('my_idp_key');
        $loginUrl = $settings['idp']['singleSignOnService']['url'];
        $logoutUrl = $settings['idp']['singleLogoutService']['url'];
        $auth = $this->createMock(OneLoginAuth::class);
        $auth
            ->method('processResponse')
            ->willReturn($methodOverrides['processResponse'] ?? true);
        $auth
            ->method('processSLO')
            ->willReturn($methodOverrides['processSLO'] ?? null);
        $auth
            ->method('login')
            ->willReturn($methodOverrides['login'] ?? redirect($loginUrl));
        $auth
            ->method('logout')
            ->willReturn($methodOverrides['logout'] ?? redirect($logoutUrl));
        $auth->method('getNameId')->willReturn('user@domain.tld');
        $auth
            ->method('getNameIdFormat')
            ->willReturn(
                $methodOverrides['getNameIdFormat'] ??
                    'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress'
            );
        $auth
            ->method('isAuthenticated')
            ->willReturn($methodOverrides['isAuthenticated'] ?? true);
        $auth
            ->method('getSessionIndex')
            ->willReturn(
                $methodOverrides['getSessionIndex'] ??
                    'FOOLIBRARY_a8a10b0fa413341868046c7fe13244f812ae5874'
            );
        $auth->method('getSessionExpiration')->willReturn(
            $methodOverrides['getSessionExpiration'] ??
                Carbon::now()
                    ->addHours(72)
                    ->toISOString()
        );
        $auth
            ->method('getErrors')
            ->willReturn($methodOverrides['getErrors'] ?? []);
        $auth
            ->method('getLastErrorReason')
            ->willReturn($methodOverrides['getLastErrorReason'] ?? null);
        $auth
            ->method('getLastRequestID')
            ->willReturn($methodOverrides['getLastRequestID'] ?? null);
        $auth
            ->method('getLastRequestXML')
            ->willReturn($methodOverrides['getLastRequestXML'] ?? null);
        $auth
            ->method('getLastResponseXML')
            ->willReturn(
                $methodOverrides['getLastResponseXML'] ??
                    file_get_contents(
                        app_path('../../../../../tests/fixtures/SAMLResponse')
                    )
            );
        $auth->method('getAttributes')->willReturn(
            $methodOverrides['getAttributes'] ?? [
                'first_name' => ['Foo'],
                'last_name' => ['Bar']
            ]
        );
        $auth
            ->method('getAttributesWithFriendlyName')
            ->willReturn(
                $methodOverrides['getAttributesWithFriendlyName'] ?? []
            );
        return $auth;
    }

    protected function createDriver()
    {
        $auth = $this->createAuthMock();
        return new Saml2($auth);
    }

    protected function createDriverWithBadUserResponse()
    {
        $auth = $this->createAuthMock([
            'getErrors' => ['invalid_response'],
            'getLastErrorReason' =>
                "Invalid audience for this Response (expected 'http://saml.test/auth/saml/', got 'http://saml.test/auth/saml"
        ]);
        return new Saml2($auth);
    }

    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    public function testCreatingWithBadKey()
    {
        $this->expectException(BadIdentityProviderKeyException::class);
        Saml2::create('foo');
    }

    public function testCreatingWithGoodKey()
    {
        $driver = Saml2::create('my_idp_key');
        $this->assertEquals($driver instanceof Saml2, true);
    }

    public function testRedirect()
    {
        $settings = Settings::create('my_idp_key');
        $url = $settings['idp']['singleSignOnService']['url'];
        $driver = $this->createDriver();
        $response = $driver->redirect();
        $this->assertStringContainsString('Location:', $response);
        $this->assertStringContainsString($url, $response);
    }

    public function testLogout()
    {
        $settings = Settings::create('my_idp_key');
        $url = $settings['idp']['singleLogoutService']['url'];
        $driver = $this->createDriver();
        $response = $driver->logout();
        $this->assertStringContainsString('Location:', $response);
        $this->assertStringContainsString($url, $response);
    }

    public function testProcessLogout()
    {
        $driver = $this->createDriver();
        $logout = $driver->processLogout();
        $this->assertEquals($logout, null);
    }

    public function testUser()
    {
        $driver = $this->createDriver();
        $user = $driver->user();
        $this->assertEquals($user->getNameId(), 'user@domain.tld');
        $this->assertEquals(
            $user->getNameIdFormat(),
            'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress'
        );
        $this->assertEquals(
            $user->getSessionIndex(),
            'FOOLIBRARY_a8a10b0fa413341868046c7fe13244f812ae5874'
        );
        $this->assertEquals($user->getAttributes()['first_name'][0], 'Foo');
    }

    public function testUserWithBadProcessing()
    {
        $this->expectException(ProcessingResponseFailedException::class);
        $driver = $this->createDriverWithBadUserResponse();
        $user = $driver->user();
    }

    public function testMetadata()
    {
        $driver = Saml2::create('my_idp_key');
        $metadata = $driver->metadata();
        $xml = new DOMDocument();
        $xml->loadXML($metadata);
        $keys = $xml->getElementsByTagName('AssertionConsumerService');
        $this->assertEquals(count($keys), 1);
        $this->assertEquals(
            $keys->item(0)->getAttribute('Location'),
            'http://saml.test/sso/saml2/acs'
        );
    }
}
