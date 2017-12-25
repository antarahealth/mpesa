<?php

namespace Kabangi\Mpesa\LipaNaMpesaOnline;

use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Kabangi\Mpesa\Engine\Core;
use Kabangi\Mpesa\Repositories\EndpointsRepository;

class STKStatusQuery{
    protected $pushEndpoint;

    protected $engine;

    /**
     * STKStatusQuery constructor.
     *
     * @param Core $engine
     */
    public function __construct(Core $engine)
    {
        $this->engine  = $engine;
        $this->pushEndpoint = EndpointsRepository::build(MPESA_STK_PUSH_STATUS_QUERY);
    }

    public function submit($checkoutRequestID = null){
        $time      = Carbon::now()->format('YmdHis');
        $shortCode = $this->engine->config->get('mpesa.short_code');
        $passkey   = $this->engine->config->get('mpesa.lnmo.passkey');
        $password  = \base64_encode($shortCode . $passkey . $time);
        
        $body = [
            'BusinessShortCode' => $shortCode,
            'Password'          => $password,
            'Timestamp'         => $time,
            'CheckoutRequestID'   => $checkoutRequestID,
        ];

        try {
            return $this->engine->makePostRequest([
                'endpoint' => $this->pushEndpoint,
                'body' => $body
            ]);
        } catch (RequestException $exception) {
            return \json_decode($exception->getResponse()->getBody());
        }
    }
}
