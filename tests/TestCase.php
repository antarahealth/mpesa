<?php

namespace Kabangi\Mpesa\Tests;

use Mockery;
use PHPUnit\Framework\TestCase as PHPUnit;
use Kabangi\Mpesa\Engine\Core;
use Kabangi\Mpesa\Native\NativeCache;
use Kabangi\Mpesa\Native\NativeConfig;

class TestCase extends PHPUnit
{
    /**
     * Engine Core.
     *
     * @var Engine
     **/
    protected $engine;

    /**
     * Set mocks.
     **/
    public function setUp()
    {
        $config       = new NativeConfig();
        $cache        = new NativeCache($config);
        $this->engine = new Core($config, $cache);
    }
}
