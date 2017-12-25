<?php

namespace Kabangi\Mpesa\Contracts;

/**
 * Interface Transactable
 *
 * @category PHP
 *
 * @author   David Mjomba <Kabangiprivate@gmail.com>
 */
interface Transactable
{
    /**
     * Generate transaction number for the request
     *
     * @return mixed
     */
    public static function generateTransactionNumber();
}
