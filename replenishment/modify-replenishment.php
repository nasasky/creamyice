<?php
include 'db.php';
error_reporting(E_ALL);
mb_internal_encoding("UTF-8");
if(!$_GET['repid']) header('HTTP/1.1 404 Not Found');
//(3)query replenishment orders lists
$lists =[];
$sql = "SELECT o.id,o.number,o.name,o.address,o.telephone,o.wid,o.status,o.courier,o.message,o.store,o.apply_date,o.confirm_date,o.cour_date,o.timestamp,GROUP_CONCAT(m.id,'=',m.name,'/',m.unit,'|',i.count) detail FROM purchase_order o,po_item i,material m WHERE o.id=i.purchase_id AND i.material_id=m.id AND o.id=".$_GET['repid']." GROUP BY o.id";
$result = $mysqli->query($sql);
if($result === false){
	$lists = 'Mysql Server Error';
}else{
	if($row = $result->fetch_assoc()){
		$detail =explode(',',$row['detail']);
		sort($detail,SORT_NUMERIC);
		$row['details'] = $detail;
		arr_reset($detail);
		$row['detail'] = $detail;
		$lists =$row;
	}
}
//var_dump($lists);
function arr_reset(&$arr){
	foreach($arr as $key=>$val){
         $arr[$key] = mb_substr( $val,strpos($val,'=') + 1 );
	}
    return true;
}
//echo  ;exit();


include './modify-replenishment.html';

?>