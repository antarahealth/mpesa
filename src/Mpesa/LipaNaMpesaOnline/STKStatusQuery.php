<?php

namespace Kabangi\Mpesa\LipaNaMpesaOnline;

use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Kabangi\Mpesa\Engine\Core;
use Kabangi\Mpesa\Repositories\EndpointsRepository;

class STKStatusQuery{
    protected $pushEndpoint;

    protected $engine;

    protected $validationRules = [
        'BusinessShortCode:BusinessShortCode' => 'required()({label} is required) | number',
        'Password:Password' => 'required()({label} is required)',
        'Timestamp:Timestamp' => 'required()({label} is required)',
        'CheckoutRequestID:CheckoutRequestID' => 'required()({label} is required)'
    ];

    /**
     * STKStatusQuery constructor.
     *
     * @param Core $engine
     */
    public function __construct(Core $engine)
    {
        $this->engine  = $engine;
        $this->pushEndpoint = EndpointsRepository::build(MPESA_STK_PUSH_STATUS_QUERY);
        $this->engine->addValidationRules($this->validationRules);
    }

    public function submit($params = []){
        // Make sure all the indexes are in Uppercases as shown in docs
        $userParams = [];
        foreach ($params as $key => $value) {
            $userParams[ucwords($key)] = $value;
        }

        $time      = Carbon::now()->format('YmdHis');
        $shortCode = $this->engine->config->get('mpesa.short_code');
        $passkey   = $this->engine->config->get('mpesa.lnmo.passkey');
        $password  = \base64_encode($shortCode . $passkey . $time);

        // Computed and params from config file.
        $configParams = [
            'BusinessShortCode' => $shortCode,
            'Password'          => $password,
            'Timestamp'         => $time,
        ];

        // This gives precedence to params coming from user allowing them to override config params
        $body = array_merge($configParams,$userParams);
        
        // Validate $body based on the daraja docs.
        $validationResponse = $this->engine->validateParams($body);
        if($validationResponse !== true){
            return $validationResponse;
        }

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
