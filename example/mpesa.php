<?php
require "../src/autoload.php";

use Kabangi\Mpesa\Init as Mpesa;

// You can also pass your own config here.
// Check the folder ./config/mpesa.php for reference

$mpesa = new Mpesa();
try {
    $response = $mpesa->B2C([
        'amount' => 10,
        'accountReference' => '12',
        'callBackURL' => 'https://example.com/v1/payments/C2B/confirmation',
        'queueTimeOutURL' => 'https://example.com/v1/payments/C2B/confirmation',
        'resultURL' => 'https://example.com/v1/payments/C2B/confirmation',
        'Remarks' => 'Test'
    ]);


    // $mpesa->STKStatus([]);
    
    // $mpesa->C2BRegister([]);
    
    // $mpesa->STKPush([]);
    
    // $mpesa->C2BSimulate([]);
    
    // $mpesa->B2C([])
    
    // $mpesa->B2B([]);
    
    // $mpesa->accountBalance([])
    
    // $mpesa->reversal([]);
    
    // $mpesa->transactionStatus([]);
    
    // $mpesa->reversal([]);
}catch(\Exception $e){
    $response = json_decode($e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($response);

