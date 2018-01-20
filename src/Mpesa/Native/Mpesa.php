<?php

namespace Kabangi\Mpesa\Native;

use Kabangi\Mpesa\Engine\Core;
use Kabangi\Mpesa\Native\NativeCache;
use Kabangi\Mpesa\Native\NativeConfig;
use Kabangi\Mpesa\Engine\MpesaTrait;

/**
 * Class Mpesa
 *
 * @category PHP
 *
 * @author   Julius Kabangi <Kabangijulius@gmail.com>
 */
class Mpesa
{
    use MpesaTrait;
    /**
     * @var Mpesa
     */
    private $engine;

    /**
     * Mpesa constructor.
     *
     */
    public function __construct($myconfig = []){
        $config = new NativeConfig($myconfig);
        $cache = new NativeCache($config);
        $this->engine = new Core($config, $cache);
    }
}
