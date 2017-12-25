<?php

namespace Kabangi\Mpesa\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Reversal
 *
 * @category PHP
 *
 * @author   Kabangi <kabangijulius@gmail.com>
 */
class Reversal extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mp_reversal';
    }
}
