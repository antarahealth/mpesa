<?php
// MPESA OAUTH API
const MPESA_AUTH     = 'oauth/v1/generate?grant_type=client_credentials';

// C2B API
const MPESA_C2B_REGISTER = 'mpesa/c2b/v1/registerurl';
const MPESA_C2B_SIMULATE = 'mpesa/c2b/v1/simulate';

// LipaNaMpesaOnline API
const MPESA_STK_PUSH = 'mpesa/stkpush/v1/processrequest';
const MPESA_STK_PUSH_STATUS_QUERY = 'mpesa/stkpushquery/v1/query';

// B2C API
const MPESA_B2C = 'mpesa/b2c/v1/paymentrequest';

// Account Balance API
const MPESA_ACCOUNT_BALANCE = 'mpesa/accountbalance/v1/query';

// Reversal API
const MPESA_REVERSAL = 'mpesa/reversal/v1/request';

// B2B API
const MPESA_B2B = 'mpesa/b2b/v1/paymentrequest';

// Transaction Status API for B2B, B2C and C2B transactions
const MPESA_TRANSACTION_STATUS = 'mpesa/transactionstatus/v1/query';
