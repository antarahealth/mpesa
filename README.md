# M-PESA API Package
[![Build Status](https://travis-ci.org/Kabangi/mpesa.svg?branch=master)](https://travis-ci.org/Kabangi/mpesa)
[![Latest Stable Version](https://poser.pugx.org/kabangi/mpesa/v/stable.svg)](https://packagist.org/packages/kabangi/mpesa)
[![Latest Unstable Version](https://poser.pugx.org/kabangi/mpesa/v/unstable.svg)](https://packagist.org/packages/kabangi/mpesa)
[![License](https://poser.pugx.org/kabangi/mpesa/license.svg)](https://packagist.org/packages/kabangi/mpesa)

This is a PHP package for the Safaricom's M-Pesa REST API dubbed DARAJA API.  

If you are looking for a laravel implementation do check the following repo [Laravel Implementation](https://github.com/Kabangi/mpesalaravel)

## Installation

This project supports both composer dependency management tool and can also be used without composer

### Using Composer
1. Run the following command
```
composer require kabangi/mpesa
```
### Without composer

1. Download the source code as zipped 

2. Follow the following directions
``` 
<?php
require "{PATHTOTHISLIBFOLDER}/src/autoload.php";

use Kabangi\Mpesa\Init as Mpesa;
```
3. Check the following example for usage https://github.com/Kabangi/mpesa/blob/master/example/mpesa.php

## Configuration

The library comes with a structured config file based on the API that you intend to use.

To add the necessary configurations:-
1. Open the folder installation.

2. Look for a file named `config/mpesa.php`

3. Edit the necessary keys that reflects the product you are using.


## Usage
```
<?php
require "../src/autoload.php";

use Kabangi\Mpesa\Init as Mpesa;

// You can also pass your own config here.
// Check the folder ./config/mpesa.php for reference

$mpesa = new Mpesa();
try {

    $response = $mpesa->STKPush([
        'amount' => 10,
        'transactionDesc' => '',
        'phoneNumber' => '',
    ]);
    
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
```
## Support
Need support using this package:- 
[Send a quick message here](https://t.me/kabangi)

https://t.me/kabangi

## API's Supported
The library implements all the exposed endpoints by Safaricom as listed below:-

### 1. [Lipa na M-Pesa Online Payment](https://developer.safaricom.co.ke/docs#lipa-na-m-pesa-online-payment)
#### &nbsp; &nbsp; What it is?
The Lipa na M-Pesa Online Payment endpoint(STK push) allows you to request payment from your users/clients. With this endpoint all the user is required to do is input their M-PESA pin to a prompt to send a payment to you. 
#### &nbsp; &nbsp; How to implement it?
[Read docs here](docs/LipaNaMpesaOnline.md).

### 2. [Lipa na M-Pesa Online Query Request](https://developer.safaricom.co.ke/docs#lipa-na-m-pesa-online-query-request)
#### &nbsp; &nbsp; What it is?
When you request payment from your users/clients via Lipa na M-Pesa Online endpoint above you might want to know the status of that request. This endpoint facilitates that. It allows you to query the status of any STK push on demand. 
#### &nbsp; &nbsp; How to implement it?
[Read docs here](docs/LipaNaMpesaOnlineQuery.md).

### 3. [C2B](https://developer.safaricom.co.ke/docs#c2b-api)
#### &nbsp; &nbsp; What it is?
This endpoint enables developers to receive real time notifications when a client makes a payments to a merchant's Till number or Paybill number. It assumes the payment are made via the SIM card toolkit and as a developer you need to know when that payment hits the merchants till/paybill number for reconciliation and accounting purposes.
#### &nbsp; &nbsp; How to implement it?
[Read docs here](docs/C2B.md).

### 4. [B2C](https://developer.safaricom.co.ke/docs#b2c-api)
#### &nbsp; &nbsp; What it is?
This endpoints enables merchants to pay their customers from they paybill account. Some of the use cases are but not limited to paying salaries, paying promotions to customers etc.
#### &nbsp; &nbsp; How to implement it?
[Read docs here](docs/B2C.md).

### 5. [B2B](https://developer.safaricom.co.ke/docs#b2b-api)
#### &nbsp; &nbsp; What it is?
This endpoint allows merchants to transfer funds from business to business accounts. 
#### &nbsp; &nbsp; How to implement it?
[Read docs here](docs/B2B.md).

### 6. [Transaction Status](https://developer.safaricom.co.ke/docs#transaction-status)
#### &nbsp; &nbsp; What it is?
This endpoint enables developers to initiate status check of a B2B, B2C and C2B transactions. It really comes in handy where one party in a transactions fails/claims not to have received an acknowledgment for a transaction.
#### &nbsp; &nbsp; How to implement it?
[Read docs here](docs/TransactionStatus.md).

### 7. [Reverse API](https://developer.safaricom.co.ke/docs#reversal)
#### &nbsp; &nbsp; What it is?
This endpoint enables merchants to reverse a B2B, B2C or C2B transaction. It allows automation of reversal of erronous payment to a merchant's paybill/till number or payments to goods never delivered.
#### &nbsp; &nbsp; How to implement it?
[Read docs here](docs/Reversal.md).

### 8. [Account Balance](https://developer.safaricom.co.ke/docs#account-balance-api)
#### &nbsp; &nbsp; What it is?
This endpoint enables merchants to query their Till/Paybill numbers account balance on demand.
#### &nbsp; &nbsp; How to implement it?
[Read docs here](docs/AccountBalance.md).

## Inspiration

This package was inspired by the work from smodav work on the following project:-
https://github.com/SmoDav/mpesa

## Contributors
Gratitudes goes to the following Ninjas for their help during my integration process and the things I had to learn along the way fom them or their code:-

[<img src="https://avatars1.githubusercontent.com/u/8632600?s=400&u=24e47804b187e651c75c3defc65930ef4e719b79&v=4" width="100px;"/><br /><sub>Julius Kabangi</sub>](mailto::kabangijulius@gmail.com/)


## License

The M-Pesa Package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

