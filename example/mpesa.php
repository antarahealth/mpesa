<?php
require "../src/autoload.php";

use Kabangi\Mpesa\Native\Mpesa;

$mpesa = new Mpesa();

$response = $mpesa->B2C([
    'amount' => 10,
    'accountReference' => '12',
    'callBackURL' => 'https://example.com/v1/payments/C2B/confirmation',
    'queueTimeOutURL' => 'https://example.com/v1/payments/C2B/confirmation',
    'resultURL' => 'https://example.com/v1/payments/C2B/confirmation',
    'Remarks' => 'Test'
]);

header('Content-Type: application/json');
echo json_encode($response);

// $mpesa->STKStatus([]);

// $mpesa->C2BRegister([]);

// $mpesa->C2BSimulate([]);



// $mpesa->accountBalance([]);

// $mpesa->reversal([]);

// $mpesa->transactionStatus([]);

