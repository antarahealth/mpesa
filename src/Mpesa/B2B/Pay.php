<?php

namespace Kabangi\Mpesa\B2B;

use GuzzleHttp\Exception\RequestException;
use Kabangi\Mpesa\Engine\Core;
use Kabangi\Mpesa\Repositories\EndpointsRepository;

class Pay {

    protected $pushEndpoint;

    protected $engine;

    protected $validationRules = [
        'Initiator:Initiator' => 'required()({label} is required)',
        'SecurityCredential:SecurityCredential' => 'required()({label} is required)',
        'CommandID:CommandID' => 'required()({label} is required)',
        'PartyA:PartyA' => 'required()({label} is required)',
        'RecieverIdentifierType:RecieverIdentifierType' => 'required()({label} is required)',
        'PartyB:PartyB' => 'required()({label} is required)',
        'QueueTimeOutURL:QueueTimeOutURL' => 'required()({label} is required)',
        'ResultURL:ResultURL' => 'required()({label} is required)',
        'SenderIdentifierType:SenderIdentifierType' => 'required()({label} is required)',
        'Remarks:Remarks' => 'required()({label} is required)',
        'AccountReference:AccountReference' => 'required()({label} is required)',
        'Amount:Amount' => 'required()({label} is required)'
    ];

    /**
     * Pay constructor.
     *
     * @param Core $engine
     */
    public function __construct(Core $engine)
    {
        $this->engine       = $engine;
        $this->pushEndpoint = EndpointsRepository::build(MPESA_B2B);
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

        $shortCode = $this->engine->config->get('mpesa.b2b.short_code');
        $successCallback  = $this->engine->config->get('mpesa.b2b.result_url');
        $timeoutCallback  = $this->engine->config->get('mpesa.b2b.timeout_url');
        $initiator  = $this->engine->config->get('mpesa.b2b.initiator_name');
        // TODO: Compute
        $securityCredential  = $this->engine->config->get('mpesa.b2b.security_credential');
        $commandId  = $this->engine->config->get('mpesa.b2b.default_command_id');
        // TODO: Compute
        $identifierType = 4;
        $senderIdentifierType = 4;

        $configParams = [
            'Initiator'             => $initiator,
            'SecurityCredential'        => $securityCredential,
            'CommandID'                 => $commandId,
            'Amount'                    => $amount,
            'PartyA'                    => $shortCode,
            'PartyB'                    => $number,
            'RecieverIdentifierType'    => $identifierType,
            'SenderIdentifierType'      => $senderIdentifierType,
            'Remarks'                   => $description,
            'QueueTimeOutURL'           => $timeoutCallback,
            'ResultURL'                 => $successCallback,
            'AccountReference'          => $reference
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
