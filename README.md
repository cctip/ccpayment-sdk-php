# php-sdk

## Document Address: https://doc.ccpayment.com/ccpayment-for-merchant/home
## Install via composer
````
composer require ccpayment/php-sdk:dev-master
````
## Example usage
```
<?php

use CCPayment\v1\CCPay;

$resp =  CCPay::CheckUser("9454818","202301310325561620262074393440256","c4600b8125b7ed23b5b7b8ee4acb42f4");
var_dump($resp);

```
## Reference API
_________________
### Obtain the token list supported by merchants
#### request:
| Name       | Required | Type      | Description                                                                                                                                    |
|------------|----------|-----------|------------------------------------------------------------------------------------------------------------------------------------------------|
| $appid     | Y        | string    | Partner unique ID, once a merchant has been on-boarded with CCPayment Pay, the merchant will be provided with the credentials with appId       |
| $appSecret | Y        | string    | Partner unique ID, once a merchant has been on-boarded with CCPayment Pay, the merchant will be provided with the credentials with appSecret   |

#### Example:
```
use CCPayment\v1\CCPay;

$resp =  CCPay::GetSupportToken("202301310325561620262074393440256","c4600b8125b7ed23b5b7b8ee4acb42f4");
var_dump($resp);
```

#### response
| Name               | Required | Type    | Description                    |
|--------------------|----------|---------|--------------------------------|
| code               | Y        | Integer |                                |
| msg                | Y        | string  |                                |
| data               | Y        | object  |                                |
| data.list          | Y        | array   |                                |
| data.list.token_id | Y        | string  | The unique identifier of token |
| data.list.crypto   | Y        | string  | crypto symbol                  |
| data.list.name     | Y        | string  | Currency full name             |
| data.list.price    | Y        | string  | Current price (in USD)         |
| data.list.min      | Y        | string  | Minimum trading amount         |
| data.list.logo     | Y        | string  | crypto logo                    |

_________________
### Obtain the list of the available networks for a certain token
#### request: 
| Name       | Required | Type      | Description                                                   |
|------------|----------|-----------|---------------------------------------------------------------|
| $token_id  | Y        | string    | Returned token_id form token                                  |
| $appid     | Y        | string    | the merchant will be provided with the credentials with appId |
| $appSecret | Y        | string    | appSecret                                                     |

#### Example:
```
use CCPayment\v1\CCPay;

$resp =  CCPay::GetTokenChain(["token_id"=>"58f93c4d-ce0b-4c7c-af77-b4b299718715"], "202301310325561620262074393440256","c4600b8125b7ed23b5b7b8ee4acb42f4");
var_dump($resp);
```

#### response
| Name                 | Required | Type    | Description                                 |
|----------------------|----------|---------|---------------------------------------------|
| code                 | Y        | Integer |                                             |
| msg                  | Y        | string  |                                             |
| data                 | Y        | object  |                                             |
| data.list            | Y        | array   |                                             |
| data.list.token_id   | Y        | string  | The unique identifier of the network chain  |
| data.list.crypto     | Y        | string  | crypto symbol                               |
| data.list.name       | Y        | string  | Currency full name                          |
| data.list.network    | Y        | string  | crypto network                              |
| data.list.chain      | Y        | string  | crypto chain                                |
| data.list.contract   | Y        | string  | Contract                                    |
| data.list.logo       | Y        | string  | crypto logo                                 |
| data.list.chain_logo | Y        | string  | Network logo                                |

_________________
###  Create payment order
    Manage 100% of your front-end interactions and use our APIs to build your own checkout page.
#### request:
| Name               | Required | Type      | Description                                                                      |
|--------------------|----------|-----------|----------------------------------------------------------------------------------|
| $token_id          | Y        | string    | Returned token_id form token chain                                               |
| $amount            | Y        | string    | Amount of Merchant's orders  (in USD by default, cannot exceed 2 decimal places) |
| $merchant_order_id | Y        | string    | Merchant orders, cannot be repeated                                              |
| $fiat_currency     | Y        | string    | Fiat currency name (in USD by default),Other fiat currency,in development        |
| $remark            | N        | string    | remark                                                                           |
| $appid             | Y        | string    | the merchant will be provided with the credentials with appId                    |
| $appSecret         | Y        | string    | appSecret                                                                        |

#### Example:
```
use CCPayment\v1\CCPay;

$resp =  CCPay::CreateOrder([
      "remark"=>"",
      "token_id"=>"f36ad1cf-222a-4933-9ad0-86df8069f916",
      "amount"=>"0.5",
      "merchant_order_id"=>strval(time()).strval(rand(0,1000)),
      "fiat_currency"=> "USD"
     ], "202301310325561620262074393440256","c4600b8125b7ed23b5b7b8ee4acb42f4");
var_dump($resp);
```

#### response
| Name             | Required | Type    | Description                |
|------------------|----------|---------|----------------------------|
| code             | Y        | Integer |                            |
| msg              | Y        | string  |                            |
| data             | Y        | object  |                            |
| data.order_id    | Y        | string  | CCPayment order id         |
| data.crypto      | Y        | string  | crypto symbol              |
| data.amount      | Y        | string  | Amount (in USD by default) |
| data.logo        | Y        | string  | crypto logo                |
| data.network     | Y        | string  | network                    |
| data.pay_address | Y        | string  | payment address            |

_________________
### Generate a checkout URL
#### request:
| Name               | Required | Type          | Description                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               |
|--------------------|----------|---------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| $valid_timestamp   | N        | Integer       | The validity period of the order.It is recommended that the validity period uploaded by the merchant should be less than the actual validity period of the merchant's order, due to the fact that it may take some time for the transaction on the chain to arrive.BTC will arrive within 24 hours and other tokens will usually arrive within 30 minutes.Unless the merchant specifies a validity period for the order, the order validity period will be set to 24 hours by default, and there is a maximum validity period of 10 days. |
| $amount            | Y        | string        | Amount of Merchant's orders  (in USD by default, cannot exceed 2 decimal places)                                                                                                                                                                                                                                                                                                                                                                                                                                                          |
| $merchant_order_id | Y        | string        | Merchant orders, cannot be repeated                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       |
| $product_name      | Y        | string        | Merchandise name                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          |
| $return_url        | N        | string        | The URL used to return to the merchant after the user completes the payment                                                                                                                                                                                                                                                                                                                                                                                                                                                               |
| $appid             | Y        | string        | the merchant will be provided with the credentials with appId                                                                                                                                                                                                                                                                                                                                                                                                                                                                             |
| $appSecret         | Y        | string        | appSecret                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 |

#### Example:
```
use CCPayment\v1\CCPay;

$resp =  CCPay::CheckoutUrl([
    "return_url"=>"https://cwallet.com/pay/callback",
    "valid_timestamp"=>4566,
    "amount"=>"0.5",
    "merchant_order_id"=>strval(time()).strval(rand(0,1000)),
    "product_name"=> "knowledge is power"
   ], "202301310325561620262074393440256","c4600b8125b7ed23b5b7b8ee4acb42f4");
var_dump($resp);
```

#### response
| Name                  | Required | Type    | Description         |
|-----------------------|----------|---------|---------------------|
| code                  | Y        | Integer |                     |
| msg                   | Y        | string  |                     |
| data                  | Y        | object  |                     |
| data.payment_url      | Y        | string  | URL link of payment |

_________________
### Call the withdrawal API to initiate withdrawals
#### request:
| Name               | Required | Type      | Description                                                                       |
|--------------------|----------|-----------|-----------------------------------------------------------------------------------|
| $token_id          | Y        | string    | token_id returned by  passing token list when sending crypto to a Cwallet account |
| $address           | Y        | string    | Pass the Cwallet ID when sending to a Cwallet account                             |
| $merchant_order_id | Y        | string    | Merchant orders, cannot be repeated                                               |
| $value             | Y        | string    | Quantity of withdrawal                                                            |
| $appid             | Y        | string    | the merchant will be provided with the credentials with appId                     |
| $appSecret         | Y        | string    | appSecret                                                                         |

#### Example:
```
use CCPayment\v1\CCPay;

$resp =  CCPay::Withdraw([
      "token_id"=>"85db36af-3282-4501-9357-67da32691ab7",//matic POLYGON
      "address"=>"0x4aa884f207c2e4003c450eb14a5e93d16a60e730",
//    "token_id"=>"8e5741cf-6e51-4892-9d04-3d40e1dd0128",//POLYGON  f137d42c-f3a6-4f23-9402-76f0395d0cfe
//    "address"=>"9454818",
      "value"=>"0.045",
     "merchant_order_id"=>strval(time()).strval(rand(0,1000))
     ], "202301310325561620262074393440256","c4600b8125b7ed23b5b7b8ee4acb42f4");
var_dump($resp);
```

#### response
| Name             | Required | Type    | Description        |
|------------------|----------|---------|--------------------|
| code             | Y        | Integer |                    |
| msg              | Y        | string  |                    |
| data             | Y        | object  |                    |
| data.order_id    | Y        | string  | CCPayment order id |
| data.network_fee | Y        | string  | network fee        |

_________________
### The amount of USD converted into tokens
#### request:
| Name                | Required | Type        | Description                                                   |
|---------------------|----------|-------------|---------------------------------------------------------------|
| $token_id           | Y        | string      | Returned token_id from token chain or token list              |
| $amount             | Y        | string      | Amount (USD by default)                                       |
| $appid              | Y        | string      | the merchant will be provided with the credentials with appId |
| $appSecret          | Y        | string      | appSecret                                                     |

#### Example:
```
use CCPayment\v1\CCPay;

$resp =  CCPay::GetTokenRate(["token_id"=>"e8f64d3d-df5b-411d-897f-c6d8d30206b7","amount"=>"12"], "202301310325561620262074393440256","c4600b8125b7ed23b5b7b8ee4acb42f4");
var_dump($resp);
```

#### response
| Name           | Required | Type    | Description                      |
|----------------|----------|---------|----------------------------------|
| code           | Y        | Integer |                                  |
| msg            | Y        | string  |                                  |
| data           | Y        | object  |                                  |
| data.price     | Y        | string  | Crypto current price (in USD)    |
| data.value     | Y        | string  | Number of corresponding currency |

_________________
### Obtain the network fee of a certain network
#### request:
| Name                | Required | Type        | Description                                                                  |
|---------------------|----------|-------------|------------------------------------------------------------------------------|
| $token_id           | Y        | string      | Returned token_id from token chain                                           |
| $appid              | Y        | string      | the merchant will be provided with the credentials with appId                |
| $appSecret          | Y        | string      | appSecret                                                                    |

#### Example:
```
use CCPayment\v1\CCPay;

$resp =  CCPay::NetworkFee(["token_id"=>"0912e09a-d8e2-41d7-a0bc-a25530892988"], "202301310325561620262074393440256","c4600b8125b7ed23b5b7b8ee4acb42f4");
var_dump($resp);
```

#### response
| Name          | Required | Type    | Description   |
|---------------|----------|---------|---------------|
| code          | Y        | Integer |               |
| msg           | Y        | string  |               |
| data          | Y        | object  |               |
| data.token_id | Y        | string  |               |
| data.crypto   | Y        | string  | crypto symbol |
| data.fee      | Y        | string  | network fee   |

_________________
### Obtain details of merchant's assets
#### request:
| Name       | Required | Type     | Description                                                   |
|------------|----------|----------|---------------------------------------------------------------|
| $token_id  | N        | string   | Returned token_id form token list                             |
| $appid     | Y        | string   | the merchant will be provided with the credentials with appId |
| $appSecret | Y        | string   | appSecret                                                     |

#### Example:
```
use CCPayment\v1\CCPay;

$resp =  CCPay::Assets("202301310325561620262074393440256","c4600b8125b7ed23b5b7b8ee4acb42f4","");
var_dump($resp);
```

#### response
| Name                 | Required | Type    | Description                       |
|----------------------|----------|---------|-----------------------------------|
| code                 | Y        | Integer |                                   |
| msg                  | Y        | string  |                                   |
| data                 | Y        | object  |                                   |
| data.list            | Y        | array   |                                   |
| data.list.token_id   | Y        | string  | Returned token_id form token list |
| data.list.crypto     | Y        | string  | crypto symbol                     |
| data.list.name       | Y        | string  | Currency full name                |
| data.list.value      | Y        | string  | Quantity                          |
| data.list.price      | Y        | string  | Crypto current price (in USD)     |
| data.list.logo       | Y        | string  | crypto logo                       |

_________________
### Check the Validity of Cwallet ID
#### request:
| Name       | Required | Type      | Description                                                                                                                                  |
|------------|----------|-----------|----------------------------------------------------------------------------------------------------------------------------------------------|
| $cId       | Y        | string    | cwallet id                                                                                                                                   |
| $appid     | Y        | string    | Partner unique ID, once a merchant has been on-boarded with CCPayment Pay, the merchant will be provided with the credentials with appId     |
| $appSecret | Y        | string    | Partner unique ID, once a merchant has been on-boarded with CCPayment Pay, the merchant will be provided with the credentials with appSecret |

#### Example:
```
use CCPayment\v1\CCPay;

$resp =  CCPay::CheckUser("9454818","202301310325561620262074393440256","c4600b8125b7ed23b5b7b8ee4acb42f4");
var_dump($resp);
```
#### response
| Name          | Required | Type      | Description      |
|---------------|----------|-----------|------------------|
| code          | Y        | Integer   |                  |
| msg           | Y        | string    |                  |
| data          | Y        | object    |                  |
| data.c_id     | Y        | string    | cwallet id       |
| data.nickname | Y        | string    | cwallet nikename |

_________________
### Notification of order callbacks
