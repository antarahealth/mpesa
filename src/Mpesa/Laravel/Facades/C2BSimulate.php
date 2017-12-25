<?php

namespace Kabangi\Mpesa\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class C2BSimulate
 *
 * @category PHP
 *
 * @author   Julius  <kabangijulius@gmail.com>
 */
class C2BSimulate extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mp_C2B_simulate';
    }
}
