<?php
namespace CCPayment\v1;

use CCPayment\v1;
use WpOrg\Requests\Autoload;

require './vendor/autoload.php';

Autoload::register();

header('Content-type: application/json');

//$postStr = $GLOBALS["HTTP_RAW_POST_DATA"] ?? "";
//
//print_r($postStr);
//$data = json_decode(file_get_contents('php://input'), true);
//
//var_dump($data);
//echo 111;

$resp =  CCPay::NetworkFee(["token_id"=>"a0634a9a-fb43-49eb-a918-18405f192217"],"JsreuwK7DrWASbQN","1e2f9c22a4c8dfe3360f2d730d88c4d5");
var_dump($resp);

return;
// $resp =  CCPay::PaymentAddress( ["user_id"=>"xxxxxx", "chain"=>"BSC"],"202308040255341687296190582538240","dcd99b1308384c2f071b58436d2fb88d");
// echo json_encode($resp);


// $resp =  CCPay::SupportCoin( "202310160225331713742948502618112","1f6efd4691f89ed40324f4b5f75f0092");
// echo json_encode($resp);
// return;
//

// $resp =  CCPay::GetChainHeightInfo( "202308040255341687296190582538240","dcd99b1308384c2f071b58436d2fb88d");
// echo json_encode($resp);
// return;
//

// $resp =  CCPay::Withdraw( [
//  // "token_id"=>"c6b7c878-8a01-4f7f-8d2e-99a72787b1ac",//Trc20
//    "address"=>"CP10119",
//    "merchant_pays_fee"=>false,
// //    "token_id"=>"f137d42c-f3a6-4f23-9402-76f0395d0cfe",//POLYGON
//    // "address"=>"0x4aa884f207c2e4003c450eb14a5e93d16a60e730",
//        "token_id"=>"5c48ec51-c8d0-45cc-ace9-1a3860bd0e25",//POLYGON USDC
//    // "address"=>"0x4aa884f207c2e4003c450eb14a5e93d16a60e730",
// //    "token_id"=>"bc78e27d-540b-4756-a7fa-69930eca6322",//Solana
// //   "address"=>"5ArZBKFVozK9xH3MsNx8A6hYcL2jsCgVHvYPwQnr43pa",
//     // 85db36af-3282-4501-9357-67da32691ab7
// //    "token_id"=>"e660b0fd-a1b7-430d-a558-8cbfaf7302c9",//STATS
// //    "address"=>"LNURL1DP68GURN8GHJ7MTE9E3HWCTVD3JHGTNRDAKJ7CMRW35HQTMKXYHKCM30WPSHJTEKXGMRGVFKXVCRSVP48Q6NWC34XY6NSWP3VFNXXCTRXASKZDTYVCCRVWRPVD3RGDFEV5ERGWPHXDNXZDF4V33NQVF5XSCNSWRXVD3RJ3EYQUA1",
// //    "token_id"=>"f36ad1cf-222a-4933-9ad0-86df8069f9162",//POLYGON 0912e09a-d8e2-41d7-a0bc-a25530892988,f36ad1cf-222a-4933-9ad0-86df8069f916
// //    "address"=>"herculew@protonmail.com",
//     "value"=>"0.1",
//     "memo"=>"",
//     "merchant_order_id"=>strval(time()).strval(rand(0,1000)),
// ],"202308040255341687296190582538240","dcd99b1308384c2f071b58436d2fb88d");
// var_dump($resp);


// $resp =  CCPay::OrderInfo( ["37343721111113213111112221111"],"202310160225331713742948502618112","1f6efd4691f89ed40324f4b5f75f0092");// "202307201100461681982474689273856","fc59b3e22801c4f8eed69a87e4f402ac"
// echo json_encode($resp);


// return;
// php -S localhost:8080
// 创建订单
//for ($i =0;$i<2;$i++){
   $resp =  CCPay::CreateOrder( [
       "notify_url"=>"https://www.xxxx.com/callback",
       "remark"=>"备注",
       "token_id"=>"a0634a9a-fb43-49eb-a918-18405f192217",
       "product_price"=>"1",
       "merchant_order_id"=>"evenPRDTestMerchantOrddddd00",
       "denominated_currency"=> "USD",
   ],"202308080629551688799681213833216","3b5c6a816023ac7fc740c94eb074d38c");
   var_dump($resp);
//}

return;
//
//// url

// 获取token 列表
//$resp =  CCPay::GetSupportToken("202303220557251638419590313394176","bb3b8f03e90cb6955ea415f30e9d000c");
//echo json_encode($resp);

// 获取链列表
//$resp =  CCPay::GetTokenChain(["token_id"=>"bbcaccc6-8cd1-4cc0-8d62-5e8f053e3837"],"202303220557251638419590313394176","bb3b8f03e90cb6955ea415f30e9d000c");
//echo json_encode($resp);
////
//$resp =  CCPay::GetTokenRate(["token_id"=>"0f47494d-a139-40a4-a2db-8cd720b4faba","amount"=>"12"],"202302010636261620672405236006912","62fbff1f796c42c50bb44d4d3d065390");
//var_dump($resp);



// 检测url
//$resp =  CCPay::CheckUser("9454818","202303220557251638419590313394176","bb3b8f03e90cb6955ea415f30e9d000c");
//var_dump($resp);
//
//// 拉取资产
//$resp =  CCPay::Assets("202303220557251638419590313394176","bb3b8f03e90cb6955ea415f30e9d000c","");
//var_dump($resp);

// 获取网络费用


// 查询一下订单 todo

