<?php
if($_POST){
	
	include './db.php';
	$repid = $_POST['repid'];
	$name = $_POST['name'];
	$cour_date = $_POST['cour_date'];
	$telephone = $_POST['telephone'];
	$address = $_POST['address'];
	$message = $_POST['message'];
    $status = $_POST['status'] + 1;

	$material_ids = $_POST['material_ids'];
    $datetime =date("Y-m-d H:i:s");
	$sql ="UPDATE purchase_order SET name='{$name}',cour_date='{$cour_date}',telephone={$telephone},address='{$address}',message='{$message}',
	status={$status},timestamp='{$datetime}' WHERE id=".$repid.";";
	foreach ($material_ids as $key => $value) {
		$sql .="UPDATE po_item SET count={$value} WHERE purchase_id={$repid} AND material_id={$key};";
	}
	$result = $mysqli->multi_query($sql);
	if($result === false){
		exit("<script>alert('服务器错误！请稍后再试！');window.history.back(-1);</script>");
	}

     exit("<script>alert('恭喜你操作成功！');window.location.href='./index.php';</script>");
}