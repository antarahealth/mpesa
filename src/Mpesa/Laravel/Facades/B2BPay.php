<?php

namespace Kabangi\Mpesa\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class B2BPay
 *
 * @category PHP
 *
 * @author   Kabangi <kabangijulius@gmail.com>
 */
class B2BPay extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mp_B2B_pay';
    }
}
