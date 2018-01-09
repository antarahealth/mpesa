<?php
require "../src/autoload.php";

use Kabangi\Mpesa\Native\Mpesa;

$mpesa = new Mpesa();

$response = $mpesa->STKPush([
    'amount' => 10,
    'phoneNumber' => '254723731241',
    'accountReference' => '12',
    'callBackURL' => 'https://sandbox.connecthealth.io/v1/payments/C2B/confirmation',
    'transactionDesc' => 'Test'
]);
header('Content-Type: application/json');
echo json_encode($response);

// $mpesa->STKStatus([]);

// $mpesa->C2BRegister([]);

// $mpesa->C2BSimulate([]);

// $mpesa->B2C([]);

// $mpesa->accountBalance([]);

// $mpesa->reversal([]);

// $mpesa->transactionStatus([]);

