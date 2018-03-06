<?php

namespace Kabangi\Mpesa\LipaNaMpesaOnline;

use Kabangi\Mpesa\Engine\Core;

class STKPush{

    protected $endpoint = 'mpesa/stkpush/v1/processrequest';

    protected $engine;

    protected $validationRules = [
        'BusinessShortCode:BusinessShortCode' => 'required()({label} is required) | number',
        'Password:Password' => 'required()({label} is required)',
        'Timestamp:Timestamp' => 'required()({label} is required)',
        'TransactionType:TransactionType' => 'required()({label} is required)',
        'Amount:Amount' => 'required()({label} is required) | number()({label} should be a numeric value)',
        'PartyA:Party A' => 'required()({label} is required)',
        'PartyB:PartyB' => 'required()({label} is required)',
        'PhoneNumber:PhoneNumber' => 'required()({label} is required)',
        'CallBackURL:CallBackURL' => 'required()({label} is required) | website',
        'AccountReference:AccountReference' => 'required()({label} is required)',
        'TransactionDesc:TransactionDesc' => 'required()({label} is required)'
    ];

    /**
     * STK constructor.
     *
     * @param Core $engine
     */
    public function __construct(Core $engine)
    {
        $this->engine       = $engine;
        $this->engine->setValidationRules($this->validationRules);
    }
    

    /**
     * Initiate STK push request
     * 
     * @param Array $params
     * 
    */
    public function submit($params = [],$appName='default'){
        // Make sure all the indexes are in Uppercases as shown in docs
        $userParams = [];
        foreach ($params as $key => $value) {
            $userParams[ucwords($key)] = $value;
        }

        $time      = $this->engine->getCurrentRequestTime();
        $shortCode = $this->engine->config->get('mpesa.lnmo.short_code');
        $passkey   = $this->engine->config->get('mpesa.lnmo.passkey');
        $password  = \base64_encode($shortCode . $passkey . $time);

        // Computed and params from config file.
        $configParams = [
            'BusinessShortCode' => $shortCode,
            'CallBackURL'       => $this->engine->config->get('mpesa.lnmo.callback'),
            'TransactionType'   => $this->engine->config->get('mpesa.lnmo.default_transaction_type'),
            'Password'          => $password,
            'PartyB'            => $shortCode,
            'Timestamp'         => $time,
        ];

        // This gives precedence to params coming from user allowing them to override config params
        $body = array_merge($configParams,$userParams);
        if(empty($body['PartyA']) && !empty($body['PhoneNumber'])){
            $body['PartyA'] = $body['PhoneNumber'];
        }
        
        return $this->engine->makePostRequest([
            'endpoint' => $this->endpoint,
            'body' => $body
        ],$appName);
    }
}
