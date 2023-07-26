<?php
namespace CCPayment\v1;

use ErrorException;
use WpOrg\Requests\Requests;

/**
 * doc: https://doc.ccpayment.com/ccpayment-for-merchant/home
 * CreateOrder(array $originData, string $appId, string $appSecret)
 * CheckoutUrl(array $originData, string $appId, string $appSecret)
 * GetSupportToken(string $appId, string $appSecret)
 * GetTokenChain(array $originData, string $appId, string $appSecret)
 * GetTokenRate(array $originData, string $appId, string $appSecret)
 * Webhook(string $body, array $headers, string $appSecret)
 * Withdraw(array $originData, string $appId, string $appSecret): array
 * CheckUser(string $cID, string $appId, string $appSecret)
 * Assets(string $tokenID,string $appId, string $appSecret)
 * NetworkFee(array $originData,string $appId, string $appSecret)
 * ["code"=>10008, "msg"=>"param is err"];
 * ["code"=>10007, "msg"=>"http body is empty"];
 * ["code"=>10006, "msg"=>"sign verify value is err"];
 * ["code"=>10009, "msg"=>"sign verify is err"];
 * ["code"=>10005,"msg"=>"http code error"];
 */
class CCPay
{
    const APPID = 'Appid';
    const TIMESTAMP = 'Timestamp';
    const SIGN = 'Sign';
    public static $urls = [
        "CreateOrderUrl" => "https://admin.ccpayment.com/ccpayment/v1/bill/create",
         "CheckoutUrl" => "https://admin.ccpayment.com/ccpayment/v1/concise/url/get", // Zlib marker - level 1.
        "SupportToken" => "https://admin.ccpayment.com/ccpayment/v1/support/token",
        "TokenChain" => "https://admin.ccpayment.com/ccpayment/v1/token/chain",
        "TokenRate" => "https://admin.ccpayment.com/ccpayment/v1/token/rate",
        "Withdraw" => "https://admin.ccpayment.com/ccpayment/v1/withdraw",
        "CheckUser" => "https://admin.ccpayment.com/ccpayment/v1/check/user",
        "Assets" => "https://admin.ccpayment.com/ccpayment/v1/assets",
        "NetworkFee" => "https://admin.ccpayment.com/ccpayment/v1/network/fee",
        "SupportCoin" => "https://admin.ccpayment.com/ccpayment/v1/coin/all",
        "OrderInfo" => "https://admin.ccpayment.com/ccpayment/v1/bill/info",
        "PaymentAddress" => "https://admin.ccpayment.com/ccpayment/v1/payment/address/get",
    ];


    public static $headers = [
        'Content-Type' => 'application/json',
        self::APPID => "",
        self::TIMESTAMP => "",
        self::SIGN => ""
    ];

    public static $appSecret = "";

    public static function setHeaders($appid, $appSecret)
    {
        self::$headers[self::TIMESTAMP] = strval(time());
        self::$headers[self::APPID] = $appid;
        self::$appSecret = $appSecret;
    }

    /**
     * @param array $originData
     * $originData = [
         * "order_valid_period"=>45566, // int
         * "remark"=>"",
         * "token_id"=>"8e5741cf-6e51-4892-9d04-3d40e1dd0128",// required
         * "product_price"=>"0.5", // required
         * "merchant_order_id"=>"3735077979050379", // merchant order, todo required
         * "denomination_currency"=> "USD" // default USD
         * "notify_url"=>"", // The URL address will be notified via a POST request when the order status changes. Ensure the URL is accessible to receive notifications from the payment platform.
          "custom_value"=>"",// Merchant custom field - This custom value field will be returned in transaction status notification.
     * ];
     * @param string $appId
     * @param string $appSecret
     * @return array
     * success:
     * {
        * "code": 10000,
        * "msg": "success",
        * "data": {
            "product_price": "0.5",
            "order_id": "202301090616511612332555323101184",
            "logo": "https://resource.cwallet.com/token/icon/usdt.png",
            "network": "TRC20",
            "pay_address": "TYWnk1EGALQyYst2yFSd29QQQTEkuKMbyt",
            "crypto": "USDT",
            "order_valid_period":823456
        * }
    * }
     */
    public static function CreateOrder(array $originData, string $appId, string $appSecret): array
    {

        if ( empty($originData["token_id"]) ||  (empty($originData["amount"]) && empty($originData["product_price"]))   || empty($originData["merchant_order_id"])) {
            return ["code"=>10008, "msg"=>"param is err"];
        }
        $originData["denominated_currency"] = $originData["denominated_currency"]??"USD";

        self::setHeaders($appId, $appSecret);

        $data = self::getCreateOrderData($originData);

        $resource = json_encode($data);

        self::SHA256Hex($resource);

        return self::SendRequest(self::$urls["CreateOrderUrl"], $resource);
    }

    public static function getCreateOrderData(array $originData): array
    {
        return [
            "remark" => $originData["remark"]??"",
            "order_valid_period" => $originData["order_valid_period"]??0,
            "token_id" => $originData["token_id"],
            "amount" => $originData["amount"]??"",
            "product_price" => $originData["product_price"],
            "merchant_order_id" => $originData["merchant_order_id"],
            "denominated_currency" => $originData["denominated_currency"], // 默认USD
            "notify_url" => $originData["notify_url"]??"",
            "custom_value" => $originData["custom_value"]??""
        ];
    }

    /**
     * fetch token list
     * @return array
     *
     * success return:
     * {
        "code": 10000,
        "msg": "success",
        "data": {
           "list":[
                {
                    "crypto": "ETH",
                    "name": "Ethereum",
                    "logo": "https://resource.cwallet.com/token/icon/ETH.png",
                    "min": "0",
                    "price": "1638",
                    "token_id": "e8f64d3d-df5b-411d-897f-c6d8d30206b7"
                },
                {
                    "crypto": "BNB",
                    "name": "Binance",
                    "logo": "https://resource.cwallet.com/token/icon/wbnb.png",
                    "min": "0",
                    "price": "306.6",
                    "token_id": "038073e6-a784-4e3e-b6a7-36456a39055f"
                }
            ]
      }
    }
     */
    public static function GetSupportToken(string $appId, string $appSecret): array
    {
        self::setHeaders($appId, $appSecret);

        self::SHA256Hex();

        return self::SendRequest(self::$urls["SupportToken"]);
    }


    /**
     * Select currency to get the corresponding chain
     * @param array $originData
     * $originData = [
     *   "token_id"=>"xxxxxxx-xxxxxxx",// todo required
     * ]
     * @param string $appId
     * @param string $appSecret
     * @return array
     * success:
     * {
        "code": 10000,
        "msg": "Success",
        "data": {
            "list":[
                    {
                    "crypto": "ETH",
                    "logo": "https://resource.cwallet.com/token/icon/ETH.png",
                    "name": "Ethereum",
                    "network": "ERC20",
                    "chain": "ETH",
                    "contract": "1",
                    "chain_logo": "https://resource.cwallet.com/token/icon/eth.png"
                    }
           ]
      }
    }
     */
    public static function GetTokenChain(array $originData, string $appId, string $appSecret): array
    {
        if ($originData["token_id"] == "") {
            return ["code"=>10008, "msg"=>"param is err"];
        }
        self::setHeaders($appId, $appSecret);

        $resource = json_encode(array("token_id"=>$originData["token_id"]));

        self::SHA256Hex($resource);

        return self::SendRequest(self::$urls["TokenChain"], $resource);
    }

    /**
     * The number of tokens
     * @param array $originData
     * $originData = [
         * "token_id"=>"xxxxxxx-xxxxxxx", //required
         * "amount"=>"1.2" // required
     * ]
     * @param string $appId
     * @param string $appSecret
     * @return array
     * success:
     * {
        "code": 10000,
        "msg": "success",
        "data": {
            "price": "23980.08",
            "value": "0.0417012787280109"
        }
    }
     */
    public static function GetTokenRate(array $originData, string $appId, string $appSecret): array
    {

        if (empty($originData["token_id"]) || empty($originData["amount"] )) {
            return ["code"=>10008, "msg"=>"param is err"];
        }
        self::setHeaders($appId, $appSecret);

        $resource = json_encode(["token_id"=>$originData["token_id"],"amount"=>$originData["amount"]]);

        self::SHA256Hex($resource);

        return self::SendRequest(self::$urls["TokenRate"], $resource);
    }

    /**
     * hosted Checkout:check url
     * @param array $originData
     * $originData = [
            The validity period of the order.It is recommended that the validity period uploaded by the merchant should be less than the actual validity period of the merchant's order,
    due to the fact that it may take some time for the transaction on the chain to arrive. BTC will arrive within 24 hours and other tokens will usually arrive within 30 minutes.Unless the merchant specifies a validity period for the order,
    the order validity period will be set to 24 hours by default, and there is a maximum validity period of 10 days.
         * "order_valid_period"=>45566, // int
         * "product_name"=>"test", //Merchandise name todo required
         * "return_url"=>"https://cwallet.com/pay/callback", // Payment successfully jump address
         * "product_price"=>"0.5",// Amount of Merchant's orders  (in USD by default, cannot exceed 2 decimal places)， todo required
         * "merchant_order_id"=>"3735077979050379", // Merchant order todo  required
         * "notify_url"=>"", // The URL address will be notified via a POST request when the order status changes. Ensure the URL is accessible to receive notifications from the payment platform.
           "custom_value"=>"",// Merchant custom field - This custom value field will be returned in transaction status notification.
     * ];
     * @param string $appId
     * @param string $appSecret
     * @return array
     * success:
     * {
        "code": 10000,
        "msg": "success",
        "data": {
            "payment_url": "https://ccpayment.com/pay?key=202301140359451614109992410173440"
        }
    }
     */
    public static function CheckoutUrl(array $originData, string $appId, string $appSecret): array
    {
        if (empty($originData["product_name"])  || ( empty($originData["amount"]) &&  empty($originData["product_price"]))  || empty($originData["merchant_order_id"])) {
            return ["code"=>10008, "msg"=>"param is err"];
        }

        self::setHeaders($appId, $appSecret);

        $data = self::getCheckoutUrlData($originData);

        $resource = json_encode($data);

        self::SHA256Hex($resource);

        return self::SendRequest(self::$urls["CheckoutUrl"], $resource);
    }

    public static function getCheckoutUrlData(array $originData): array
    {
        return [
            "return_url" => $originData["return_url"]??"",
            "valid_timestamp" => $originData["valid_timestamp"]??0,
            "order_valid_period" => $originData["order_valid_period"]??0,
            "amount" => $originData["amount"]??"",
            "product_price" => $originData["product_price"]??"",
            "merchant_order_id" => $originData["merchant_order_id"],
            "product_name" => $originData["product_name"],
            "notify_url" => $originData["notify_url"]??"",
            "custom_value" => $originData["custom_value"]??""
        ];
    }

    /**
     * todo deprecate
     * http->header["Appid"]、header["Timestamp"]、header["Sign"]、
     * body：
       {
         * "pay_status": "success",
         * "order_type": "Invoice",
         * "record_id": "202302201213531627642695975706624",
         * "order_id": "202302200951001627606736097771520",
         * "origin_price": "25",
         * "origin_amount": "25",
         * "fiat_rate": "0.08",
         * "paid_amount": "10",
         * "token_rate": "0.08",
         * "chain": "BSC",
         * "contract": "0xbA2aE424d960c26247Dd6c32edC70B295c744C43",
         * "crypto": "DOGE",
         * "extend": {
             * "invoice_id": "202302200950101627606529100480512",
             * "user_email": "herculew@protonmail.com",
             * "merchant_order_id": "202211154785795"
         * }
     * }
     * @param string $body
     * @param array $headers
     * @param string $appSecret
     * @return array
     */
    public static function Webhook(string $body,array $headers, string $appSecret): array
    {

        self::$appSecret = $appSecret;

        if ($body == "") {
            return ["code"=>10007, "msg"=>"http body is empty"];
        }

        $originData = json_decode($body, true);

        if ($originData["code"] != 10000) {
            return $originData;
        }

        if ($headers[self::APPID] == "" || $headers[self::TIMESTAMP] == "" || $headers[self::SIGN] == "") {
            return ["code"=>10006, "msg"=>"sign verify value is err"];
        }

        self::$headers[self::APPID] = $headers[self::APPID];
        self::$headers[self::TIMESTAMP] = $headers[self::TIMESTAMP];

        if (self::SHA256Hex($body) != $headers[self::SIGN]) {
            return ["code"=>10009, "msg"=>"sign verify is err"];
        }

        return $originData;
    }

    /**
     * @param array $originData
     * $originData = [
     *  "token_id"=>"xxxxxxx-xxxxxxx", //required
     *  "value"=>"1.2", // required
     *  "address"=>"345555",// required address or cwallet id
     *  "merchant_order_id"=>"xxxxx",// required
     *  "memo"=>"",
     * ]
     * @param string $appId
     * @param string $appSecret
     * @return array
     * {
        "code": 10000,
        "msg": "success",
        "data": {
            "order_id": "202301090616511612332555323101184",
            "network_fee": "0",
        }
    }
     */
    public static function Withdraw(array $originData, string $appId, string $appSecret): array
    {

        if (empty($originData["token_id"])  || empty($originData["value"]) || empty($originData["address"]) ||  empty($originData["merchant_order_id"])) {
            return ["code"=>10008, "msg"=>"param is err"];
        }
        self::setHeaders($appId, $appSecret);

        $data = self::getWithdrawData($originData);

        $resource = json_encode($data);

        self::SHA256Hex($resource);

        return self::SendRequest(self::$urls["Withdraw"], $resource);
    }
    public static function getWithdrawData(array $originData): array
    {
        return [
            "token_id" => $originData["token_id"],
            "address" => $originData["address"],
            "value" => $originData["value"],
            "merchant_order_id" => $originData["merchant_order_id"],
            "merchant_pays_fee" => $originData["merchant_pays_fee"] ?? false,
            "memo" => $originData["memo"]??""
        ];
    }

    /**
     * @param string $cID cwallet id
     * @param string $appId
     * @param string $appSecret
     * @return array
     */
    public static function CheckUser(string $cID, string $appId, string $appSecret): array
    {
        if ($cID == "" ) {
            return ["code"=>10008, "msg"=>"param is err"];
        }
        self::setHeaders($appId, $appSecret);

        $resource = json_encode(["c_id"=>$cID]);

        self::SHA256Hex($resource);

        return self::SendRequest(self::$urls["CheckUser"],$resource);
    }

    /**
     * @param string $tokenID
     * @param string $appId
     * @param string $appSecret
     * @return array
     */
    public static function Assets(string $appId, string $appSecret,string $tokenID = ""): array
    {

        self::setHeaders($appId, $appSecret);

        $resource = json_encode(["token_id"=>$tokenID]);

        self::SHA256Hex($resource);

        return self::SendRequest(self::$urls["Assets"], $resource);
    }

    /**
     * @param array $originData
     * @param string $appId
     * @param string $appSecret
     * @return array
     */
    public static function NetworkFee(array $originData,string $appId, string $appSecret): array
    {
        if ( empty($originData["token_id"])) {
            return ["code"=>10008, "msg"=>"param is err"];
        }
        self::setHeaders($appId, $appSecret);

        $resource = json_encode(["token_id"=>$originData['token_id'],'address'=>$originData['address']??"", 'memo'=>$originData['memo']??""]);

        self::SHA256Hex($resource);

        return self::SendRequest(self::$urls["NetworkFee"], $resource);
    }

    /**
     * @param array $merchantOrderIds // Merchant Order ID, max limit 100. Pass only one type of order id, either deposit OR withdrawal. DO NOT pass both deposit and withdrawal order ids in one request
    Responses
     * @param string $appId
     * @param string $appSecret
     * @return array
     */
    public static function OrderInfo(array $merchantOrderIds ,string $appId, string $appSecret): array
    {
        if ( empty($merchantOrderIds)) {
            return ["code"=>10008, "msg"=>"param is err"];
        }
        self::setHeaders($appId, $appSecret);
        $resource = json_encode(["merchant_order_ids"=>$merchantOrderIds]);

        self::SHA256Hex($resource);

        return self::SendRequest(self::$urls["OrderInfo"], $resource);
    }

    /**
     * @param string $appId
     * @param string $appSecret
     * @return array
     */
    public static function SupportCoin(string $appId, string $appSecret): array
    {
        self::setHeaders($appId, $appSecret);

        self::SHA256Hex();

        return self::SendRequest(self::$urls["SupportCoin"]);
    }
    /**
     * @param array $originData
     *  * $originData = [
     *      "user_id"=>"", //required,User ID, unique identification
     *      "chain"=>"", // required,Blockchain network, unique identification, https://docs.google.com/spreadsheets/d/1YKY-pxCdqer1IurgEkyNqW0xIj7EmtfX3rILoJqkKgw/edit#gid=0 Click here to check chain list
     *      "notify_url"=>"",// The URL address will be notified via a POST request when the order status changes. Ensure the URL is accessible to receive notifications from the payment platform.
     * ]
     * @param string $appId
     * @param string $appSecret
     * @return array
     * {
        "code": 10000,
        "msg": "success",
        "data": {
            "address": "TYWnk1EGALQyYst2yFSd29QQQTEkuKMbyt",//The corresponds to the chain address of the network
            "memo": "", // Tag data of Memo coins, used to label and identify user addresses
        }
    }
     */
    public static function PaymentAddress(array $originData ,string $appId, string $appSecret): array
    {
        if ( empty($originData) || empty($originData['user_id']) || empty($originData['chain'])) {
            return ["code"=>10008, "msg"=>"param is err"];
        }
        self::setHeaders($appId, $appSecret);
        $resource = json_encode($originData);

        self::SHA256Hex($resource);

        return self::SendRequest(self::$urls["PaymentAddress"], $resource);
    }

    /**
     * @param string $url
     * @param string $data
     * @return array|mixed
     */
    public static function SendRequest(string $url, string $data= "")
    {
        $resp = Requests::post($url, self::$headers, $data);

        if ($resp->status_code != http_response_code(200)) {
            return ["code"=>10005,"msg"=>"http code error"];
        }

        $originStr = $resp->body;

        $originData = json_decode($originStr,true);

        if (!isset($originData["code"]) || $originData["code"] != 10000){
            return $originData;
        }

        self::$headers[self::APPID] = $resp->headers[self::APPID];
        self::$headers[self::TIMESTAMP] = $resp->headers[self::TIMESTAMP];

        if (self::SHA256Hex($originStr) != $resp->headers[self::SIGN]) {
            return ["code"=>10009, "msg"=>"sign verify is err"];
        }

        return $originData;
    }

    public static function SHA256Hex($originStr = ""): string
    {
        $str = self::$headers[self::APPID]. self::$appSecret.self::$headers[self::TIMESTAMP].$originStr;
        $re = hash('sha256', $str);
        self::$headers[self::SIGN] = $re;
        return $re;
    }
}


?>
