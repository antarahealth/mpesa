<?php

namespace Kabangi\Mpesa\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class ABBalance
 *
 * @category PHP
 *
 * @author   Kabangi <kabangijulius@gmail.com>
 */
class ABBalance extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mp_AB_balance';
    }
}
