<?php
require_once './Api/app_sign.php';

//build query app_product sql
$sql = "SELECT `id`,`name`,`price`,`description`,`img` FROM app_product LIMIT 3";
$result = $mysqli->query($sql);
if($result === false){
	exit(json_encode(['code'=>1201,'msg'=>'query app_product is failure!']));
}

// get product result
$product = [];
while($row = $result->fetch_assoc()){
	$product[] = $row;
}

//return json result
$return_json = [
	'code'=>1202,
	'msg'=>'the product is empty'
];
if($product && is_array($product)){
	$return_json = [
		'code'=>200,
		'msg'=>'query product is successful',
		'data'=>[
			'product'=>$product
		]
	];
}

echo json_encode($return_json);
$mysqli->close();