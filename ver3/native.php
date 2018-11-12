<?php
require_once './Api/app_sign.php';
require_once "./wlpay/lib/WxPay.Api.php";
require_once "./wlpay/example/WxPay.NativePay.php";
require_once './wlpay/example/log.php';

//wechat payment pattern

$order_number = 'CrApp'.'MachineNumber'.date("YmdHis");
$sn = $_POST['sn'];
$product = json_decode($_POST['product'],true);
$time = time();
$sql = "INSERT INTO `app_order`(`number`,`sn`,`user`,`prepay_id`,`proname`,`price`,`amount`,`count`,`discount`,`type`,`status`,`paytime`,`shiptime`,`timestamp`) 
VALUES('{$order_number}',{$sn},'{$user}','{$prepay_id}','".$product['name']."',".$product['price'].",".$product['amount'].",".$product['num'].",".$product['discount'].",1,1,{$time},{$time},{$time})";

$notify = new NativePay();
$input = new WxPayUnifiedOrder();
$input->SetBody("克瑞米艾App-冰淇淋购买");
$input->SetAttach(json_encode(['orderid'=>$orderid]));
$input->SetOut_trade_no($order_number);
$input->SetTotal_fee("$fee");
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag("订单优惠标记");
$input->SetNotify_url("https://partner.creamyice.com/ver3/native_notify.php");
$input->SetTrade_type("NATIVE");
$input->SetProduct_id($order_number);
$result = $notify->GetPayUrl($input);

// reAssemble result and return to app
if($result['return_code'] === 'SUCCESS' && $result['result_code'] === 'SUCCESS'){
	$return_json = [
		'code'=>200,
		'msg'=>'Get WechatQRcode Successful',
		'data'=>[
			'code_url' => $result["code_url"],
			'order_number' => $order_number
		]
	];
}

echo json_encode($return_json);
$mysqli->close();

?>