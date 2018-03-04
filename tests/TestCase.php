<?php

namespace Kabangi\Mpesa\Tests;

use Mockery;
use PHPUnit\Framework\TestCase as PHPUnit;
use Kabangi\Mpesa\Engine\Core;
use Kabangi\Mpesa\Engine\Cache;
use Kabangi\Mpesa\Engine\Config;
use Kabangi\Mpesa\Auth\Authenticator;
use Kabangi\Mpesa\Contracts\HttpRequest;

class TestCase extends PHPUnit
{
    /**
     * Engine Core.
     *
     * @var Engine
     **/
    public $engine;

    public $httpClient;

    public $auth;

    /**
     * Set mocks.
     **/
    public function setUp()
    {
        $config       = new Config();
        $cache        = new Cache($config);
        $this->httpClient = $this->createMock(HttpRequest::class);
        $this->auth = $this->createMock(Authenticator::class);
        $this->engine  = new Core($config, $cache,$this->httpClient,$this->auth);
    }

    public function mockAuth(){
    }
}
