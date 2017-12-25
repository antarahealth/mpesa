<?php

namespace Kabangi\Mpesa\B2B;

use GuzzleHttp\Exception\RequestException;
use Kabangi\Mpesa\Engine\Core;
use Kabangi\Mpesa\Repositories\EndpointsRepository;

class Pay {

    protected $pushEndpoint;

    protected $engine;

    /**
     * Pay constructor.
     *
     * @param Core $engine
     */
    public function __construct(Core $engine)
    {
        $this->engine       = $engine;
        $this->pushEndpoint = EndpointsRepository::build(MPESA_B2B);
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
    public function submit(){
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

        $body = [
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
