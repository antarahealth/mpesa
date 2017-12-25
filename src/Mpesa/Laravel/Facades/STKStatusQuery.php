<?php

namespace Kabangi\Mpesa\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class STKStatusQuery
 *
 * @category PHP
 *
 * @author   Julius Kabangi <kabangijulius@gmail.com>
 */
class STKStatusQuery extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(){
        return 'mp_stk_status_query';
    }
}
