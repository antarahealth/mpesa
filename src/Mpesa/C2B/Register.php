<?php

namespace Kabangi\Mpesa\C2B;

use InvalidArgumentException;
use Kabangi\Mpesa\Engine\Core;

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
    protected $endpoint = 'mpesa/c2b/v1/registerurl';

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
        $this->engine->setValidationRules($this->validationRules);
    }

    /**
     * Initiate the registration process.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function submit($params = [],$appName='default'){
        // Make sure all the indexes are in Uppercases as shown in docs
        $userParams = [];
        foreach ($params as $key => $value) {
            $userParams[ucwords($key)] = $value;
        }

        $shortCode = $this->engine->config->get('mpesa.c2b.short_code');
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

        return $this->engine->makePostRequest([
            'endpoint' => $this->endpoint,
            'body' => $body
        ],$appName);
    }
}
