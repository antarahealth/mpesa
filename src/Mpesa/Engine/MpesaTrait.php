<?php

namespace Kabangi\Mpesa\Engine;

use Kabangi\Mpesa\AccountBalance\Balance;
use Kabangi\Mpesa\B2B\Pay;
use Kabangi\Mpesa\B2C\Pay as B2CPay;
use Kabangi\Mpesa\C2B\Register;
use Kabangi\Mpesa\C2B\Simulate;
use Kabangi\Mpesa\Reversal\Reversal;
use Kabangi\Mpesa\TransactionStatus\TransactionStatus;
use Kabangi\Mpesa\LipaNaMpesaOnline\STKPush;
use Kabangi\Mpesa\LipaNaMpesaOnline\STKStatusQuery;

trait MpesaTrait{
    public function STKPush($params = []){
        $stk = new STKPush($this->engine);
        return $stk->submit($params);
    }

    public function STKStatus($params = []){
        $stk = new STKStatusQuery($this->engine);
        return $stk->submit($params);
    }

    public function C2BRegister($params = []){
        $c2b = new Register($this->engine);
        return $c2b->submit($params);
    }

    public function C2BSimulate($params = []){
        $simulate = new Simulate($this->engine);
        return $simulate->submit();
    }

    public function B2C($params = []){
        $b2c = new B2CPay($this->engine);
        return $b2c->submit($params);
    }

    public function B2B($params = []){
        $b2c = new Pay($this->engine);
        return $b2c->submit($params);
    }

    public function accountBalance($params = []){
       $bl = new Balance($this->engine);
       return $bl->submit($params);
    }

    public function reversal($params = []){
        $rv = new Reversal($this->engine);
        return $rv->submit($params);
    }

    public function transactionStatus($params = []){
        $tn = new TransactionStatus($this->engine);
        return $tn->submit($params);
    }
}