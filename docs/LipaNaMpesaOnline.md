### Prerequisites to implementation
1. Make sure you have installed this package
2. You have read the configuration guidelines specific to Lipa na mpesa and taken the necessary steps.
3. You have successfully acquired production credentials from safaricom. If not you can go ahead and use the sandbox credentials that comes preconfigured on installation.

### How to consume LipaNaMpesaOnline(STK PUSH) endpoint with this package.
#### Payment flow involved with this endpoint.
1. Your system initiates the payment request on behalf of client and send it to safaricom.
2. Safaricom sends the request to the client requesting them to authorize the transaction by entering their M-PESA PIN.
3. If client enters their correct M-PESA PIN, safaricom respond to your sytem with details regarding the transaction. This is made possible by sending an internet accessible URL via a CallBackUrl.

#### Using vanilla php


#### Using Laravel.


### Known issues with this endpoint
