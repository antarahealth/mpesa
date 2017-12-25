<?php

namespace Kabangi\Mpesa\C2B;

use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;
use Kabangi\Mpesa\Engine\Core;
use Kabangi\Mpesa\Repositories\EndpointsRepository;

/**
 * Class Simulate.
 *
 * @category PHP
 *
 * @author   Kabangi Julius <kabangijulius@gmail.com>
 */
class Simulate
{
    /**
     * @var string
     */
    protected $endpoint;

    /**
     * The short code to register callbacks for.
     *
     * @var string
     */
    protected $shortCode;

    /**
     * @var Core
     */
    private $engine;

    /**
     * Registrar constructor.
     *
     * @param Core $engine
     */
    public function __construct(Core $engine)
    {
        $this->engine   = $engine;
        $this->endpoint = EndpointsRepository::build(MPESA_C2B_SIMULATE);
    }

    /**
     * Initiate the simulation process.
     *
     * @param null $shortCode
     * @param null $confirmationURL
     * @param null $validationURL
     * @param null $onTimeout
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function submit($params = []){
        $shortCode = $this->engine->config->get('mpesa.c2b.short_code');
        $isSandbox = $this->engine->config->get('mpesa.is_sandbox');
        if($isSandbox === true){
            // Simulate using the test phone number otherwise it won't work.
            $number = $this->engine->config->get('mpesa.c2b.test_phone_number');
        }

        $body = [
            'CommandID'         => 'CustomerPayBillOnline',
            'Amount'            => intval($amount),
            'Msisdn'            => $number,
            'BillRefNumber'     => (string) $reference,
            'ShortCode'         => intval($shortCode),
        ];

        try {
            return $this->engine->makePostRequest([
                'endpoint' => $this->endpoint,
                'body' => $body
            ]);
        } catch (RequestException $exception) {
            $message = $exception->getResponse() ?
               $exception->getResponse()->getReasonPhrase() :
               $exception->getMessage();

            throw $this->generateException($message);
        }
    }

    /**
     * @param $getReasonPhrase
     *
     * @return \Exception
     */
    private function generateException($getReasonPhrase){
        return new \Exception($getReasonPhrase);
    }
}
