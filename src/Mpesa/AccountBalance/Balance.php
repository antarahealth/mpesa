<?php

namespace Kabangi\Mpesa\AccountBalance;

use GuzzleHttp\Exception\RequestException;
use Kabangi\Mpesa\Engine\Core;
use Kabangi\Mpesa\Repositories\EndpointsRepository;

class Balance {

    protected $pushEndpoint;
    
    protected $engine;

    /**
     * STK constructor.
     *
     * @param Core $engine
     */
    public function __construct(Core $engine)
    {
        $this->engine       = $engine;
        $this->pushEndpoint = EndpointsRepository::build(MPESA_ACCOUNT_BALANCE);
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
    public function submit($description = null){
        $shortCode = $this->engine->config->get('mpesa.account_balance.short_code');
        $successCallback  = $this->engine->config->get('mpesa.account_balance.result_url');
        $timeoutCallback  = $this->engine->config->get('mpesa.account_balance.timeout_url');
        $initiator  = $this->engine->config->get('mpesa.account_balance.initiator_name');
        $commandId  = $this->engine->config->get('mpesa.account_balance.default_command_id');
        
        // TODO: Compute
        $securityCredential  = $this->engine->config->get('mpesa.account_balance.security_credential');
        // TODO: Compute
        $identifierType = '4';

        $body = [
            'Initiator'     => $initiator,
            'SecurityCredential'=> $securityCredential,
            'CommandID'         => $commandId,
            'PartyA'            => $shortCode,
            'IdentifierType'    => $identifierType,
            'Remarks'           => $description,
            'QueueTimeOutURL'   => $timeoutCallback,
            'ResultURL'         => $successCallback,
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
