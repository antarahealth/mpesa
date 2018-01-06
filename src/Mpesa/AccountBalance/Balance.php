<?php

namespace Kabangi\Mpesa\AccountBalance;

use Kabangi\Mpesa\Engine\Core;

class Balance {

    protected $endpoint = 'mpesa/accountbalance/v1/query';
    
    protected $engine;

    protected $validationRules = [
        'Initiator:Initiator' => 'required()({label} is required)',
        'SecurityCredential:SecurityCredential' => 'required()({label} is required)',
        'CommandID:CommandID' => 'required()({label} is required)',
        'PartyA:PartyA' => 'required()({label} is required)',
        'IdentifierType:IdentifierType' => 'required()({label} is required)',
        'Remarks:Remarks' => 'required()({label} is required)',
        'QueueTimeOutURL:QueueTimeOutURL' => 'required()({label} is required)',
        'PhoneNumber:PhoneNumber' => 'required()({label} is required)',
        'CallBackURL:CallBackURL' => 'required()({label} is required) | website',
        'ResultURL:ResultURL' => 'required()({label} is required)'
    ];

    /**
     * STK constructor.
     *
     * @param Core $engine
     */
    public function __construct(Core $engine)
    {
        $this->engine       = $engine;
        $this->engine->addValidationRules($this->validationRules);
    }

    /**
     * Initiate the balance query process.
     *
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

        $shortCode = $this->engine->config->get('mpesa.account_balance.short_code');
        $successCallback  = $this->engine->config->get('mpesa.account_balance.result_url');
        $timeoutCallback  = $this->engine->config->get('mpesa.account_balance.timeout_url');
        $initiator  = $this->engine->config->get('mpesa.account_balance.initiator_name');
        $commandId  = $this->engine->config->get('mpesa.account_balance.default_command_id');
        
        // TODO: Compute
        $securityCredential  = $this->engine->config->get('mpesa.account_balance.security_credential');
        // TODO: Compute
        $identifierType = '4';

        $configParams = [
            'Initiator'     => $initiator,
            'SecurityCredential'=> $securityCredential,
            'CommandID'         => $commandId,
            'PartyA'            => $shortCode,
            'IdentifierType'    => $identifierType,
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
                'endpoint' => $endpoint,
                'body' => $body
            ]);
        } catch (\Exception $exception) {
            return \json_decode($exception->getMessage());
        }
    }
}
