<?php

namespace Kabangi\Mpesa\C2B;

use InvalidArgumentException;
use Kabangi\Mpesa\Engine\Core;

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
    protected $endpoint = 'mpesa/c2b/v1/simulate';

    /**
     * @var Core
     */
    private $engine;

    protected $validationRules = [
        'ShortCode:ShortCode' => 'required()({label} is required)',
        'CommandID:CommandID' => 'required()({label} is required)',
        'Msisdn:Msisdn' => 'required()({label} is required)',
        'Amount:Amount' => 'required()({label} is required)',
        'BillRefNumber:BillRefNumber' => 'required()({label} is required)'
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
     * Initiate the simulation process.
     *
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
        $isSandbox = $this->engine->config->get('mpesa.is_sandbox');
        if($isSandbox === true){
            // Simulate using the test phone number otherwise it won't work.
            $userParams['Msisdn'] = $this->engine->config->get('mpesa.c2b.test_phone_number');
        }

        $configParams = [
            'CommandID'         => 'CustomerPayBillOnline',
            'ShortCode'         => intval($shortCode),
        ];

        // This gives precedence to params coming from user allowing them to override config params
        $body = array_merge($configParams,$userParams);

        return $this->engine->makePostRequest([
            'endpoint' => $this->endpoint,
            'body' => $body
        ],$appName);
    }
}
