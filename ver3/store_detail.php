<?php
/**
 * @author wanglong  1285505793@qq.com
 * @license https://wanglong.com  wanglong
 * @copyright  wanglong private
 * @datetime  2018-07-18 16:30:30
 * @description This is store details
 */
require_once './Api/signature_db.php';
require_once './Api/function.php';

$sn = $_GET['sn'];
if ($_GET['type'] == 'location') {
    $longitude = $_GET['longitude'];
    $latitude  = $_GET['latitude'];
}
//begin current date 30 days and currenttime
$date = date('Y-m-d', time() - 30 * 24 * 60 * 60);
$time = date('H:i');
$code = 0;
$msg  = 'successfully';

//(1)query store machine detail by sn
$sql = "SELECT p.id,p.store,p.address,p.starttime,p.zone,p.sn,p.src,p.img,p.type,p.business_hours,p.longitude,p.latitude,p.enable,p.pro_list,m.timestamp lt
FROM place p,machine m WHERE p.sn=m.sn AND p.sn=" . $sn;
$result = $mysqli->query($sql);
if ($result === false) {
    $code = $mysqli->errno;
    $msg  = $mysqli->error;
}
if ($row = $result->fetch_assoc()) {
    if ($row['type'] == 1) {
        $row['full']   = 20;
        $row['reduce'] = 10;
    }
    //compare place business_hours,current time
    $arr = explode("-", trim(substr($row['business_hours'], 0, 11)));
    if ($time > $arr[0] && $time < $arr[1]) {
        $row['is_business'] = true;
    } else {
        $row['is_business'] = false;
    }
    $row['is_share'] = false;
    $row['is_dis']   = true;
    if ($longitude) {
        $row['distance'] = getDistance($longitude, $latitude, $row['longitude'], $row['latitude']);
        if ($row['distance'] && $row['distance'] < 8) {
            $row['is_dis'] = true;
        } else {
            $row['is_dis'] = false;
        }
    }

    $row['is_status'] = true; //machine stauts true:正常 false:离线
    //if( (strtotime($row['lt']) + 125) < time()) $row['is_status'] =false;
    if ($row['pro_list']) {
        $proList = explode(',', $row['pro_list']);
    }
    //explode pro_list check store's product is enough
    $list = $row;
}

//(2) query product detail and check product is have
$product_sql = "SELECT id pro_id,name,price,addr,description,img,img_xiao FROM product";
$res         = $mysqli->query($product_sql);
if ($res === false) {
    $code = $mysqli->errno;
    $msg  = $mysqli->error;
}
while ($row = $res->fetch_assoc()) {
    $product[] = $row;
}
if ($product) {
    foreach ($product as $key => $val) {
        $product[$key]['selected'] = false;
        $product[$key]['num']      = 0;
        $product[$key]['id']       = $key;
        if (in_array($val['pro_id'], $proList)) {
            $product[$key]['is_have'] = true;
        } else {
            $product[$key]['is_have'] = false;
        }
    }
}

echo json_encode(['code' => $code, 'msg' => $msg, 'list' => $list, 'date' => $date, 'product' => $product]);
$mysqli->close();
