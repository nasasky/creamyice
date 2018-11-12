<?php
/**
 * @author wanglong <[1285505793@qq.com]>
 * Date  2018-06-28
 * Description   this page is customer's store details
 */
require_once './Api/signature_db.php';
require_once './Api/function.php';

$type       = $_GET['type'];
$size       = 25;
$page       = $_GET['page'];
$page_start = (($page >= 1) ? $page - 1 : $page) * $size;
//begin current date 30 days and currenttime
$date = date('Y-m-d', time() - 30 * 24 * 60 * 60);
$time = date('H:i');
$code = 0;
$msg  = 'store list is successfully';

//(1)location get user longitude and latitude by type
if ($type == 'location') {
    $longitude = $_GET['longitude'];
    $latitude  = $_GET['latitude'];
    $sql       = "SELECT p.store,p.address,p.sn,p.src,p.starttime,p.business_hours,p.longitude,p.latitude,p.type,m.timestamp lt FROM place p,machine m WHERE p.sn=m.sn AND p.enable=1 LIMIT {$page_start},{$size}";
} else {
    $sql = "SELECT p.store,p.address,p.sn,p.src,p.starttime,p.business_hours,p.longitude,p.latitude,p.type,m.timestamp lt FROM place p,machine m WHERE p.sn=m.sn AND p.enable=1 LIMIT {$page_start},{$size}";
}
$result = $mysqli->query($sql);
if ($result === false) {
    $code = $mysqli->errno;
    $msg  = $mysqli->error;
}

//(2)query success and fetch
while ($row = $result->fetch_assoc()) {
    if($row['type'] == 1){
         $row['full']   = 20;
         $row['reduce'] = 10;
    }
    if($longitude && $latitude) $row['distance'] =getDistance($longitude,$latitude,$row['longitude'],$row['latitude']);
    //compare place business_hours,current time
    $arr = explode("-", trim(substr($row['business_hours'], 0, 11)));
    if ($time > $arr[0] && $time < $arr[1]) {
        $row['is_business'] = true;
    } else {
        $row['is_business'] = false;
    }

    //$row['is_dis'] =true;

    //$row['is_status'] =true; //machine stauts true:正常 false:离线
    //if( (strtotime($row['lt']) + 125) < time()) $row['is_status'] =false;

    /*if ($row['pro_list']) {
        $proList = explode(',', $row['pro_list']);
    }*/
    //explode pro_list check store's product is enough
    //if($row['is_status']);
    $list[] = $row;
}

//(3) sort arr asc by distance
if ($longitude && $latitude && is_array($list)) {
    sortArray($list, 'distance');
}
if (!$list) {
    $code = -1;
    $msg  = 'store list is empty';
}
echo json_encode(['code' => $code, 'msg' => $msg, 'list' => $list, 'date' => $date]);
$mysqli->close();

// I ) location get user longitude and latitude by type
/*if($type == 'location'){
$longitude =$_GET['longitude'];
$latitude =$_GET['latitude'];
//get current location assign distace's squarepoint
$squarePoint =getSquarePoint($longitude , $latitude , 100000);
$page_start =(($page >= 1)?$page - 1 : $page)*$size;
$sql ="SELECT p.id,p.store,p.address,p.zone,p.sn,m.ver,p.src,p.img,p.starttime,p.business_hours,p.longitude,p.latitude,p.active,p.enable FROM place p,machine m
WHERE p.sn=m.sn AND m.ver = 1 AND ( p.latitude BETWEEN ".$squarePoint['right-bottom']['lat']." AND ".$squarePoint['left-top']['lat']." )
AND ( p.longitude BETWEEN ".$squarePoint['left-top']['lng']." AND ".$squarePoint['right-bottom']['lng'].") LIMIT {$page_start},{$size}";
}*/
