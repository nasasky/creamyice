<?php
include './Api/signature_db.php';

$sql ="SELECT o.id,o.paytime,n.number FROM sales_order o,sales_order_number n WHERE o.id=n.orid AND o.customer_id=n.wid AND o.customer_id={$wid} AND status=2 ORDER BY id DESC LIMIT 1";

$result = $mysqli->query($sql);
$data =[];
if($result === false){
	$code = $mysqli->errno; $msg = $mysqli->error;
}else{
	if($row =$result->fetch_assoc()){
		if(strtotime($row['paytime']) + 60 > time()){
			$row['clock'] = 60 - ( time() - strtotime($row['paytime']) );
			$data = $row;
			$code =0; $msg ='successful';
		}else{
			$code = -2; $msg = 'paytime is expired';
		}
	}else{
		$code = -1;  $msg ='pay number is empty';
	}
}

echo json_encode(['code'=>$code,'msg'=>$msg,'numbers'=>$data]);
$mysqli->close();







?>