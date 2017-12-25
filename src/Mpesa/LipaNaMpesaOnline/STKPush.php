<?php

namespace Kabangi\Mpesa\LipaNaMpesaOnline;

use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Kabangi\Mpesa\Engine\Core;
use Kabangi\Mpesa\Repositories\EndpointsRepository;

class STKPush{

    protected $pushEndpoint;

    protected $engine;

    protected $validationRules = [
        'BusinessShortCode' => 'required | number',
        'Password' => 'required',
        'Timestamp' => 'required',
        'TransactionType' => 'required',
        'Amount' => 'required | number',
        'PartyA' => 'required',
        'PartyB' => 'required',
        'PhoneNumber' => 'required',
        'CallBackURL' => 'required | website',
        'AccountReference' => 'required',
        'TransactionDesc' => 'required'
    ];

    /**
     * STK constructor.
     *
     * @param Core $engine
     */
    public function __construct(Core $engine)
    {
        $this->engine       = $engine;
        $this->pushEndpoint = EndpointsRepository::build(MPESA_STK_PUSH);
        $this->engine->validator->add($this->validationRules);
    }

    private function validateRequestParams($params){
        if ($this->engine->validator->validate($params)) {
            return true;
        }else{
            return json_encode($this->engine->validator->getMessages());
        }
    }

    /**
     * Initiate STK push request
     * 
     * @param Array $params
     * 
    */
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
            'CallBackURL'       => $this->engine->config->get('mpesa.lnmo.callback'),
            'TransactionType'   => $this->engine->config->get('mpesa.lnmo.default_transaction_type'),
            'Password'          => $password,
            'PartyB'            => $shortCode,
            'Timestamp'         => $time,
        ];

        // This gives precedence to params coming from user allowing them to override config params
        $body = array_merge($configParams,$userParams);
        
        // Validate $body based on the daraja docs.
        $validationResponse = $this->validateRequestParams($body);
        if($validationResponse !== true){
            throw new Exception($validationResponse);
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
