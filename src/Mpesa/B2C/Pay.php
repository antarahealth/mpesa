<?php

namespace Kabangi\Mpesa\B2C;

use GuzzleHttp\Exception\RequestException;
use Kabangi\Mpesa\Engine\Core;
use Kabangi\Mpesa\Repositories\EndpointsRepository;

class Pay {

    protected $pushEndpoint;

    protected $engine;

    protected $validationRules = [
        'InitiatorName:InitiatorName' => 'required()({label} is required)',
        'SecurityCredential:SecurityCredential' => 'required()({label} is required)',
        'CommandID:CommandID' => 'required()({label} is required)',
        'PartyA:PartyA' => 'required()({label} is required)',
        'PartyB:PartyB' => 'required()({label} is required)',
        'QueueTimeOutURL:QueueTimeOutURL' => 'required()({label} is required)',
        'ResultURL:ResultURL' => 'required()({label} is required)',
        'Remarks:Remarks' => 'required()({label} is required)',
        'Amount:Amount' => 'required()({label} is required)'
    ];

    /**
     * STK constructor.
     *
     * @param Core $engine
     */
    public function __construct(Core $engine)
    {
        $this->engine       = $engine;
        $this->pushEndpoint = EndpointsRepository::build(MPESA_B2C);
        $this->engine->addValidationRules($this->validationRules);
    }

    /**
     * Initiate the registration process.
     *
     * @param null $amount
     * @param null $number
     * @param null $description
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function submit($params = []){
        // Make sure all the indexes are in Uppercases as shown in docs
        $userParams = [];
        foreach ($params as $key => $value) {
            $userParams[ucwords($key)] = $value;
        }
        
        $isSandbox = $this->engine->config->get('mpesa.is_sandbox');
        if($isSandbox === true){
            // Simulate using the test phone number otherwise it won't work.
            $userParams['PartyB'] = $this->engine->config->get('mpesa.b2c.test_phone_number');
        }

        $shortCode = $this->engine->config->get('mpesa.b2c.short_code');
        $successCallback  = $this->engine->config->get('mpesa.b2c.result_url');
        $timeoutCallback  = $this->engine->config->get('mpesa.b2c.timeout_url');
        $initiator  = $this->engine->config->get('mpesa.b2c.initiator_name');
        // TODO: Compute
        $securityCredential  = $this->engine->config->get('mpesa.b2c.security_credential');
        $commandId  = $this->engine->config->get('mpesa.b2c.default_command_id');

        $configParams = [
            'InitiatorName'     => $initiator,
            'SecurityCredential'=> $securityCredential,
            'CommandID'         => $commandId,
            'PartyA'            => $shortCode,
            'QueueTimeOutURL'   => $timeoutCallback,
            'ResultURL'         => $successCallback,
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
