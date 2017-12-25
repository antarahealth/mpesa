<?php

namespace Kabangi\Mpesa\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class C2BRegister
 *
 * @category PHP
 *
 * @author   Kabangi <kabangijulius@gmail.com>
 */
class C2BRegister extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mp_C2B_register';
    }
}
