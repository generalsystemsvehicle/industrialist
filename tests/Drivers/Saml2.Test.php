<?php

namespace Riverbedlab\Industrialist\Tests\Drivers;

use Carbon\Carbon;
use DOMDocument;
use OneLogin\Saml2\Utils as OneLogin_Saml2_Utils;
use Orchestra\Testbench\TestCase;
use Riverbedlab\Industrialist\Drivers\Saml2;
use Riverbedlab\Industrialist\Exceptions\BadIdentityProviderKeyException;
use Riverbedlab\Industrialist\Lib\OneLoginAuth;
use Riverbedlab\Industrialist\Lib\Settings;
use Riverbedlab\Industrialist\Providers\ServiceProvider;

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
        $app['config']->set('industrialist', include app_path('../../../../../tests/fixtures/config.php'));
    }

    protected function createAuthMock()
    {
        $settings = Settings::create('my_idp_key');
        $loginUrl = $settings['idp']['singleSignOnService']['url'];
        $logoutUrl = $settings['idp']['singleLogoutService']['url'];
        $auth = $this->createMock(OneLoginAuth::class);
        $auth->method('processResponse')->willReturn(true);
        $auth->method('processSLO')->willReturn(null);
        $auth->method('login')->willReturn(redirect($loginUrl));
        $auth->method('logout')->willReturn(redirect($logoutUrl));
        $auth->method('getNameId')->willReturn('user@domain.tld');
        $auth->method('getNameIdFormat')->willReturn('urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress');
        $auth->method('isAuthenticated')->willReturn(true);
        $auth->method('getSessionIndex')->willReturn('FOOLIBRARY_a8a10b0fa413341868046c7fe13244f812ae5874');
        $auth->method('getSessionExpiration')->willReturn(Carbon::now()->addHours(72)->toISOString());
        $auth->method('getErrors')->willReturn([]);
        $auth->method('getLastErrorReason')->willReturn(null);
        $auth->method('getLastRequestID')->willReturn(null);
        $auth->method('getLastRequestXML')->willReturn(null);
        $auth->method('getLastResponseXML')->willReturn(file_get_contents(app_path('../../../../../tests/fixtures/SAMLResponse')));
        $auth->method('getAttributes')->willReturn([
            'first_name' => [
                'Foo'
            ],
            'last_name' => [
                'Bar'
            ],
        ]);
        $auth->method('getAttributesWithFriendlyName')->willReturn([]);
        return $auth;
    }

    protected function createDriver()
    {
        $auth = $this->createAuthMock();
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
        $this->assertEquals($user->getNameIdFormat(), 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress');
        $this->assertEquals($user->getSessionIndex(), 'FOOLIBRARY_a8a10b0fa413341868046c7fe13244f812ae5874');
        $this->assertEquals($user->getAttributes()['first_name'][0], 'Foo');
    }

    public function testMetadata()
    {
        $driver = Saml2::create('my_idp_key');
        $metadata = $driver->metadata();
        $xml = new DOMDocument();
        $xml->loadXML($metadata);
        $keys = $xml->getElementsByTagName('AssertionConsumerService');
        $this->assertEquals(count($keys), 1);
        $this->assertEquals($keys->item(0)->getAttribute('Location'), 'http://saml.test/sso/saml2/acs');
    }
}
