<?php
require_once './Api/app_sign.php';

// build sql by type
$type = isset($_POST['type'])?$_POST['type']:'';

switch ($type) {
	case 'query':
		$order_id = isset($_POST['order_number'])?$_POST['order_number']:'';
		if($order_id) $order_id = param_decrypt($order_id);
		else break;
		$sql = "SELECT `status`,`price`,`proname` name,`amount`,`count`,CONCAT('Cr-App',`number`) order_number,`paytime`,`timestamp` FROM app_order WHERE id=".$order_id;
		break;
	
	default:
		$return_json = ['code'=>1006,'msg'=>'type param failure'];
		break;
}

// execute sql and deal with result
if($sql){
	$result = $mysqli->query($sql);
	if($result === false){
		$return_json = ['code'=>1202,'msg'=>'db server exception'];
	}else{
		if($row = $result->fetch_assoc()){
			$row['paytime'] = date('Y-m-d H:i:s',$row['paytime']);
			// set order status timeout
			if($row['status'] == 1 && ( $row['timestamp']+20 ) < time() ){
				$row['status'] = 0;
			}
			$return_json = ['code'=>200,'msg'=>$type.' order is successful','data'=>['order'=>$row]];
		}
	}
}
echo json_encode($return_json);
$mysqli->close();