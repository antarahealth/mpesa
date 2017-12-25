<?php

namespace Kabangi\Mpesa\C2B;

use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;
use Kabangi\Mpesa\Engine\Core;
use Kabangi\Mpesa\Repositories\EndpointsRepository;

/**
 * Class Register.
 *
 * @category PHP
 *
 * @author   David Mjomba <Kabangiprivate@gmail.com>
 *           Julius Kabangi <kabangijulius@gmail.com>
 */
class Register
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
     * The validation callback.
     *
     * @var
     */
    protected $validationURL;

    /**
     * The confirmation callback.
     *
     * @var
     */
    protected $confirmationURL;

    /**
     * The status of the request in case a timeout occurs.
     *
     * @var string
     */
    protected $onTimeout = 'Completed';

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
        $this->endpoint = EndpointsRepository::build(MPESA_C2B_REGISTER);
    }

    /**
     * Initiate the registration process.
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
        $shortCode = $this->engine->config->get('mpesa.short_code');
        $confirmationURL   = $this->engine->config->get('mpesa.c2b.confirmation_url');
        $onTimeout   = $this->engine->config->get('mpesa.c2b.on_timeout');
        $validationURL   = $this->engine->config->get('mpesa.c2b.validation_url');

        if ($onTimeout != 'Completed' && $onTimeout != 'Cancelled') {
            throw new InvalidArgumentException('Invalid timeout argument. Use Completed or Cancelled');
        }

        $body = [
            'ShortCode'       => $shortCode,
            'ResponseType'    => $onTimeout,
            'ConfirmationURL' => $confirmationURL,
            'ValidationURL'   => $validationURL
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
