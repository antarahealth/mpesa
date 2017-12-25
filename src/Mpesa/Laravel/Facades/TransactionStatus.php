<?php

namespace Kabangi\Mpesa\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class TransactionStatus
 *
 * @category PHP
 *
 * @author   Julius Kabangi <kabangijulius@gmail.com>
 */
class TransactionStatus extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(){
        return 'mp_transaction_status';
    }
}
