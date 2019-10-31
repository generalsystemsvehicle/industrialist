<?php

namespace Riverbedlab\Industrialist\Tests\Drivers;

use Carbon\Carbon;
use OneLogin\Saml2\Auth as OneLogin_Saml2_Auth;
use OneLogin\Saml2\Utils as OneLogin_Saml2_Utils;
use Orchestra\Testbench\TestCase;
use Riverbedlab\Industrialist\Drivers\Saml2;
use Riverbedlab\Industrialist\Exceptions\BadIdentityProviderKeyException;
use Riverbedlab\Industrialist\Providers\ServiceProvider;

/**
 * Override time() in the current namespace for testing.
 *
 * @return int
 */
function time()
{
    return ReferenceTest::$now ?: \time();
}

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

    protected function createOneLoginSaml2AuthMock()
    {
        $settings = Saml2::createSettings('my_idp_key');
        $url = $settings['idp']['singleSignOnService']['url'];
        $auth = $this->createMock(OneLogin_Saml2_Auth::class);
        $auth->method('processResponse')->willReturn(true);
        $auth->method('login')->willReturn(redirect($url));
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

    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'industrialist' => Industrialist::class,
        ];
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
        $settings = Saml2::createSettings('my_idp_key');
        $url = $settings['idp']['singleSignOnService']['url'];
        $auth = $this->createOneLoginSaml2AuthMock();
        $driver = new Saml2($auth);
        $response = $driver->redirect();
        $this->assertStringContainsString('Location:', $response);
        $this->assertStringContainsString($url, $response);
    }

    public function testProcessResponse()
    {
        $auth = $this->createOneLoginSaml2AuthMock();
        $driver = new Saml2($auth);
        $driver->processResponse();
        $this->assertEquals($driver->getProcessedResponse(), true);
    }

    public function testUser()
    {
        $auth = $this->createOneLoginSaml2AuthMock();
        $driver = new Saml2($auth);
        $user = $driver->user();
        $this->assertEquals($user->getNameId(), 'user@domain.tld');
        $this->assertEquals($user->getNameIdFormat(), 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress');
        $this->assertEquals($user->getSessionIndex(), 'FOOLIBRARY_a8a10b0fa413341868046c7fe13244f812ae5874');
        $this->assertEquals($user->getAttributes()['first_name'][0], 'Foo');
    }
}
