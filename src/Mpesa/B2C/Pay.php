<?php

namespace Kabangi\Mpesa\B2C;

use GuzzleHttp\Exception\RequestException;
use Kabangi\Mpesa\Engine\Core;
use Kabangi\Mpesa\Repositories\EndpointsRepository;

class Pay {
    protected $pushEndpoint;
    protected $engine;
    protected $number;
    protected $amount;
    protected $reference;
    protected $description;

    /**
     * STK constructor.
     *
     * @param Core $engine
     */
    public function __construct(Core $engine)
    {
        $this->engine       = $engine;
        $this->pushEndpoint = EndpointsRepository::build(MPESA_B2C);
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
        if (! starts_with($number, '2547')) {
            throw new \InvalidArgumentException('The subscriber number must start with 2547');
        }

        if (!\is_numeric($amount)) {
            throw new \InvalidArgumentException('The amount must be numeric');
        }

        $isSandbox = $this->engine->config->get('mpesa.is_sandbox');
        if($isSandbox === true){
            // Simulate using the test phone number otherwise it won't work.
            $number = $this->engine->config->get('mpesa.b2c.test_phone_number');
        }

        $shortCode = $this->engine->config->get('mpesa.b2c.short_code');
        $successCallback  = $this->engine->config->get('mpesa.b2c.result_url');
        $timeoutCallback  = $this->engine->config->get('mpesa.b2c.timeout_url');
        $initiator  = $this->engine->config->get('mpesa.b2c.initiator_name');
        // TODO: Compute
        $securityCredential  = $this->engine->config->get('mpesa.b2c.security_credential');
        $commandId  = $this->engine->config->get('mpesa.b2c.default_command_id');

        $body = [
            'InitiatorName'     => $initiator,
            'SecurityCredential'=> $securityCredential,
            'CommandID'         => $commandId,
            'Amount'            => $amount,
            'PartyA'            => $shortCode,
            'PartyB'            => $number,
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
