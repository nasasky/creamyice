<?php
require_once './Api/app_sign.php';
require_once "./wlpay/lib/WxPay.Api.php";
require_once "./wlpay/example/WxPay.NativePay.php";
require_once './wlpay/example/log.php';
require_once './wlpay/example/phpqrcode/phpqrcode.php';
//wechat payment pattern
$product = isset($_POST['product'])?$_POST['product']:'';
if(!$product){
	//    {"name":"\u90c1\u91d1\u9999","price":"12.00","num":"5","amount":"60.00","discount":"0.0"}
	exit(json_encode(['code'=>1101,'msg'=>'product parameter is wrongful']));
}
$product = json_decode($product,true);
$order_number = get_order_number();
$time = time();

// create order to database
$sql = "INSERT INTO `app_order`(`number`,`sn`,`user`,`prepay_id`,`proname`,`price`,`amount`,`count`,`discount`,`type`,`status`,`paytime`,`shiptime`,`timestamp`) 
VALUES('{$order_number}','{$sn}','shenmidewo','prepayid','".$product['name']."',".$product['price'].",".$product['amount'].",".$product['num'].",".$product['discount'].",1,1,1924876800,1924876800,{$time})";
$result = $mysqli->query($sql);
if($result === false){
	exit(json_encode(['code'=>1201,'msg'=>'create order failure']));
}
$order_id = $mysqli->insert_id;
if(!$order_id){
	exit(json_encode(['code'=>1202,'msg'=>'get order_id failure']));
}
//echo $sql;die;
$notify = new NativePay();
$input = new WxPayUnifiedOrder();
$input->SetBody("克瑞米艾App-冰淇淋购买");
$input->SetAttach(param_encrypt($order_id));
$input->SetOut_trade_no($order_number);
$input->SetTotal_fee(intval($product['amount']*100));
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag("订单优惠标记");
$input->SetNotify_url("https://partner.creamyice.com/app1/native_notify.php");
$input->SetTrade_type("NATIVE");
$input->SetProduct_id($order_number);
$result = $notify->GetPayUrl($input);

// reAssemble result and return to app 
$return_json = [
	'code'=>1102,
	'msg'=>'Get WechatQRcode Failure'
];
if($result['return_code'] === 'SUCCESS' && $result['result_code'] === 'SUCCESS'){
	$return_json = [
		'code'=>200,
		'msg'=>'Get WechatQRcode Successful',
		'data'=>[
			'code_url' => $result["code_url"],
			'order_number' => param_encrypt($order_id)
		]
	];
}

echo json_encode($return_json);
$mysqli->close();

function get_order_number(){
	list($usec,$sec) = explode(' ',microtime());
	return sprintf('%.0f',($sec + $usec) * 100000000);
}
ob_end_clean();
QRcode::png($result['code_url']);


?>