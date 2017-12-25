<?php

namespace Kabangi\Mpesa\TransactionStatus;

use GuzzleHttp\Exception\RequestException;
use Kabangi\Mpesa\Engine\Core;
use Kabangi\Mpesa\Repositories\EndpointsRepository;

class TransactionStatus {

    protected $pushEndpoint;
    
    protected $engine;

    /**
     * TransactionStatus constructor.
     *
     * @param Core $engine
     */
    public function __construct(Core $engine){
        $this->engine       = $engine;
        $this->pushEndpoint = EndpointsRepository::build(MPESA_TRANSACTION_STATUS);
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
        $shortCode = $this->engine->config->get('mpesa.transaction_status.short_code');
        $successCallback  = $this->engine->config->get('mpesa.transaction_status.result_url');
        $timeoutCallback  = $this->engine->config->get('mpesa.transaction_status.timeout_url');
        $initiator  = $this->engine->config->get('mpesa.transaction_status.initiator_name');
        $commandId  = $this->engine->config->get('mpesa.transaction_status.default_command_id');
        
        // TODO: Compute
        $securityCredential  = $this->engine->config->get('mpesa.transaction_status.security_credential');
        // TODO: Compute
        $identifierType = 4;

        $body = [
            'Initiator'         => $initiator,
            'SecurityCredential'=> $securityCredential,
            'CommandID'         => $commandId,
            'PartyA'            => $shortCode,
            'TransactionID'     => $transactionID,
            'IdentifierType'    => $identifierType,
            'Remarks'           => $description,
            'QueueTimeOutURL'   => $timeoutCallback,
            'ResultURL'         => $successCallback,
            'Occasion'          => ''
        ];
        try {
            return $this->engine->makePostRequest([
                'endpoint' => $this->endpoint,
                'body' => $body
            ]);
        } catch (RequestException $exception) {
            return \json_decode($exception->getResponse()->getBody());
        }
    }
}
