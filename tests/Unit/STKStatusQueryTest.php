<?php

namespace Kabangi\Mpesa\Tests\Unit;

use Kabangi\Mpesa\Tests\TestCase;
use Kabangi\Mpesa\LipaNaMpesaOnline\STKStatusQuery;
use Kabangi\Mpesa\Auth\Authenticator;
use Kabangi\Mpesa\Engine\Core;
use Kabangi\Mpesa\Contracts\ConfigurationStore;
use Kabangi\Mpesa\Exceptions\ConfigurationException;
use Kabangi\Mpesa\Exceptions\MpesaException;

class STKStatusQueryTest extends TestCase{

    public function setUp()
    {
        parent::setUp();
        $this->cleanCache();
    }

    private function cleanCache(){
        $file = __DIR__ . '/../../cache/.mpc';
        if (\is_file($file)) {
            \unlink($file);
        }
    }

    /**
     * Test submitting payment request without params throws an error.
     * 
     */
    public function testSubmitWithoutParams(){
        $b2c = new STKStatusQuery($this->engine);
        $this->expectException(ConfigurationException::class);
        $results = $b2c->submit();
    }

    /**
     * Test submitting payment request with appropriate param works.
     * 
     */
    public function testSubmitWithParams(){
        $b2c = new STKStatusQuery($this->engine);
        
        $this->httpClient->method('getInfo')
        ->will($this->returnValue(500));

        $this->expectException(MpesaException::class);
        // Test with null params should throw an error.
        $results = $b2c->submit([
            'CheckoutRequestID' => '254723731241',
        ]);
        fwrite(STDERR, print_r($results, TRUE));
    }
}
