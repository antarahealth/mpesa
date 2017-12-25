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
     * @var Core
     */
    private $engine;

    protected $validationRules = [
        'ShortCode:ShortCode' => 'required()({label} is required)',
        'ResponseType:ResponseType' => 'required()({label} is required)',
        'ConfirmationURL:ConfirmationURL' => 'required()({label} is required)',
        'ValidationURL:ValidationURL' => 'required()({label} is required)'
    ];

    /**
     * Registrar constructor.
     *
     * @param Core $engine
     */
    public function __construct(Core $engine)
    {
        $this->engine   = $engine;
        $this->endpoint = EndpointsRepository::build(MPESA_C2B_REGISTER);
        $this->engine->addValidationRules($this->validationRules);
    }

    /**
     * Initiate the registration process.
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

        $shortCode = $this->engine->config->get('mpesa.short_code');
        $confirmationURL   = $this->engine->config->get('mpesa.c2b.confirmation_url');
        $onTimeout   = $this->engine->config->get('mpesa.c2b.on_timeout');
        $validationURL   = $this->engine->config->get('mpesa.c2b.validation_url');

        $configParams = [
            'ShortCode'       => $shortCode,
            'ResponseType'    => $onTimeout,
            'ConfirmationURL' => $confirmationURL,
            'ValidationURL'   => $validationURL
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
                'endpoint' => $this->endpoint,
                'body' => $body
            ]);
        } catch (RequestException $exception) {
            $message = $exception->getResponse() ?
               $exception->getResponse()->getReasonPhrase() :
               $exception->getMessage();

            throw new \Exception($message);
        }
    }
}
