<?php

namespace Kabangi\Mpesa\Tests\Unit;

use Kabangi\Mpesa\Auth\Authenticator;
use PHPUnit\Framework\TestCase;
use Kabangi\Mpesa\Engine\Core;
use Kabangi\Mpesa\Exceptions\ConfigurationException;
use Kabangi\Mpesa\Native\NativeCache;
use Kabangi\Mpesa\Native\NativeConfig;

class AuthenticatorTest extends TestCase
{
    protected $config;
    protected $cache;

    protected function setUp()
    {
        parent::setUp();
        $this->cleanCache();
        $this->config =new NativeConfig();
        $this->cache  = new NativeCache($this->config);
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
    public function testAuthentication()
    {
        $engine  = new Core($this->config, $this->cache);
        $auth    = new Authenticator($engine);
        $token   = $auth->authenticate();
        $this->assertEquals('access', $token);
    }

    /**
     * Test that authenticator works.
     *
     * @test
     **/
    public function testAuthenticationFailure()
    {
        $this->expectException(ConfigurationException::class);
        $engine  = new Core($this->config, $this->cache);
        $auth    = new Authenticator($engine);
        $auth->authenticate();
    }
}
