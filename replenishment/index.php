<?php
include 'db.php';
error_reporting(E_ALL);
rebuild_GET();
$total = 0;
//(1) query replenishment orders total
$sql = 'SELECT count(id) total FROM purchase_order';
$result = $mysqli->query($sql);
if($result === false){
	$total = '读取失败';
}else{
	if($row = $result->fetch_assoc()){
		$total =$row['total'];
	}
}

//(2) query storer number
$store_total =0;
$sql ="SELECT count(id) total FROM weixinusr";
$result = $mysqli->query($sql);
if($result === false){
	$store_total = '查询失败';
}else{
	if($row = $result->fetch_assoc()){
		$store_total = $row['total'] - 2;
	}
}

//page 
$curPage =1;
$size = 10;
$start =0;
$totalPage =ceil($total / $size);
if(isset($_GET['page'])){
	$curPage =$_GET['page'];
	$start =($curPage - 1 > 0)?($curPage - 1) * $size:0;
}

//(3)query replenishment orders lists
$lists =[];
$sql = "SELECT o.id,o.number,o.name,o.address,o.telephone,o.wid,o.status,o.courier,o.store,o.apply_date,o.confirm_date,o.cour_date,o.message,o.timestamp,GROUP_CONCAT(m.id,'=',m.name,'(',i.count,'/',m.unit,')') detail FROM purchase_order o,po_item i,material m WHERE o.id=i.purchase_id AND i.material_id=m.id GROUP BY o.id ORDER BY o.id DESC LIMIT $start,$size";
$result = $mysqli->query($sql);
if($result === false){
	$lists = '服务器正忙';
}else{
	while($row = $result->fetch_assoc()){
		$detail =explode(',',$row['detail']);
		sort($detail,SORT_NUMERIC);
		$row['details'] = $detail;
		arr_reset($detail);
		$row['detail'] = implode(',',$detail);
		$lists[] =$row;
	}
}

function arr_reset(&$arr){
	foreach($arr as $key=>$val){
         $arr[$key] = mb_substr( $val,strpos($val,'=') + 1 );
	}
    return true;
}
include './index.html';
?>


