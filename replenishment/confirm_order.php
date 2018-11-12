<?php
if($_POST){
	include 'db.php';
	$id =$_POST['id'];
	$sql = "UPDATE purchase_order SET status=2 WHERE id=".$id;
	$result = $mysqli->query($sql);
	if($result === false){
		echo json_encode(['code'=>-1,'msg'=>'系统异常']);
	}else{
		echo json_encode(['code'=>0,'msg'=>'Success']);
	}

	$mysqli->close();
}else{
	header('HTTP/1.1 404 Not Found');
}










?>