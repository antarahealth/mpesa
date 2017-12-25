<?php

namespace Kabangi\Mpesa\Reversal;

use GuzzleHttp\Exception\RequestException;
use Kabangi\Mpesa\Engine\Core;
use Kabangi\Mpesa\Repositories\EndpointsRepository;

class Reversal {
    protected $endpoint;

    protected $engine;

    /**
     * Reversal constructor.
     *
     * @param Core $engine
     */
    public function __construct(Core $engine)
    {
        $this->engine       = $engine;
        $this->endpoint = EndpointsRepository::build(MPESA_REVERSAL);
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
        $shortCode = $this->engine->config->get('mpesa.reversal.short_code');
        $successCallback  = $this->engine->config->get('mpesa.reversal.result_url');
        $timeoutCallback  = $this->engine->config->get('mpesa.reversal.timeout_url');
        $initiator  = $this->engine->config->get('mpesa.reversal.initiator_name');
        $commandId  = $this->engine->config->get('mpesa.reversal.default_command_id');
        
        // TODO: Compute
        $securityCredential  = $this->engine->config->get('mpesa.reversal.security_credential');
        // TODO: Compute
        $identifierType = '4';

        $body = [
            'Initiator'              => $initiator,
            'SecurityCredential'     => $securityCredential,
            'CommandID'              => $commandId,
            'PartyA'                 => $shortCode,
            'RecieverIdentifierType' => $identifierType,
            'Remarks'                => $description,
            'QueueTimeOutURL'        => $timeoutCallback,
            'ResultURL'              => $successCallback,
            'TransactionID'          => $number
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
