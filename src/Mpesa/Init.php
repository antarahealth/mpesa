<?php

namespace Kabangi\Mpesa;

use Kabangi\Mpesa\Engine\Core;
use Kabangi\Mpesa\Engine\Cache;
use Kabangi\Mpesa\Engine\Config;
use Kabangi\Mpesa\Engine\MpesaTrait;
use Kabangi\Mpesa\Auth\Authenticator;
use Kabangi\Mpesa\Engine\CurlRequest;
/**
 * Class Mpesa
 *
 * @category PHP
 *
 * @author   Julius Kabangi <Kabangijulius@gmail.com>
 */
class Init
{
    use MpesaTrait;

    /**
     * @var Core
     */
    private $engine;

    /**
     * Mpesa constructor.
     *
     */
    public function __construct($myconfig = []){
        $config = new Config($myconfig);
        $cache = new Cache($config);
        $auth = new Authenticator();
        $httpClient = new CurlRequest();
        $this->engine = new Core($config, $cache,$httpClient,$auth);
    }
}
