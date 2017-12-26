### Prerequisites to implementation
1. Make sure you have installed this package
2. You have read the configuration guidelines specific to Lipa na mpesa and taken the necessary steps.
3. You have successfully acquired production credentials from safaricom. If not you can go ahead and use the sandbox credentials that comes preconfigured on installation.

### How to consume LipaNaMpesaOnline(STK PUSH) endpoint with this package.

#### Payment flow involved with this endpoint.
1. Your system initiates a payment request to you on behalf of client and sends it to safaricom.
2. Safaricom sends the request to the client requesting them to authorize the transaction by entering their M-PESA PIN.
3. If client enters their correct M-PESA PIN, safaricom respond to your system with details regarding the transaction. This is made possible by sending an internet accessible URL via a CallBackUrl.

#### Usage
Note this package allows you to override preconfigured parameters for this endpoint. For all supported options check the docs here https://developer.safaricom.co.ke/lipa-na-m-pesa-online/apis/post/stkpush/v1/processrequest

##### Using vanilla php


##### Using Laravel.
```
use STKPush;

class CheckoutController{

   public function checkout(){
     STKPush::submit([
      'amount' => 20,
      'phoneNumber' => '254722000000',
      'accountReference' => 'INVOICEID', 
      'transactionDesc' => 'Payment for my service'
     ]); 
   }
}

```

### Known issues with this endpoint
1. There exists some SIM cards that are not yet supported by this. All your requests to such SIM Cards will fail with `[STK DS timeout]`.
2. Making multiple subsequent requests to the same phone number causes the initial request to timeout and the STK Prompt to user not to respond to safaricom even though the user entered the correct M-PESA pin.
