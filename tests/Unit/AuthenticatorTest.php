<?php

namespace Kabangi\Mpesa\Tests\Unit;

use Kabangi\Mpesa\Auth\Authenticator;
use Kabangi\Mpesa\Tests\TestCase;
use Kabangi\Mpesa\Engine\Core;
use Kabangi\Mpesa\Exceptions\ConfigurationException;


class AuthenticatorTest extends TestCase{

    public function setUp()
    {
        parent::setUp();
        $this->cleanCache();
    }

    private function cleanCache()
    {
        $file = __DIR__ . '/../../cache/.mpc';
        if (\is_file($file)) {
            \unlink($file);
        }
    }

    /**
     * Test that authenticator works.
     *
     * @test
     **/
    public function testAuthentication(){
        $this->httpClient->method('execute')
        ->will($this->returnValue('{"access_token":"asdasdsad"}'));
        
        $this->httpClient->method('getInfo')
        ->will($this->returnValue(200));

        $auth   = new Authenticator();
        $auth->setEngine($this->engine);
        
        $token  = $auth->authenticate();
        $this->assertInternalType('string', $token);
    }
}
