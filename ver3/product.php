<?php
/**
 * @author   Wanglong
 * Date   2018-06-28
 * Descr   This page is product's details
 */
require_once './Api/signature_db.php';

$type = $_GET['type'] ?: 'getPro';
if ($type == 'getPro') {
    //(1)query place pro_list
    $sql    = "SELECT p.pro_list,p.sn FROM place p,weixin w WHERE p.sn=w.sn AND w.wid=" . $wid . " LIMIT 1";
    $result = $mysqli->query($sql);
    if ($result === false) {
        echo $mysqli->error;
    }
    while ($row = $result->fetch_assoc()) {
        $proId = explode(',', $row['pro_list']);
        $sn    = $row['sn'];
    }

    //(2)query product sql and Reorganized the product array and return the result
    $sql    = "SELECT p.id,p.name,p.price,p.description,p.img,p.img_xiao FROM product p";
    $result = $mysqli->query($sql);
    if ($result === false) {
        echo $mysqli->error;
    }
    while ($row = $result->fetch_assoc()) {
        if ($proId && in_array($row['id'], $proId)) {
            $row['is_up'] = true;
        } else {
            $row['is_up'] = false;
        }
        $row['sn'] = $sn;
        $product[] = $row;
    }
    echo json_encode(['code' => 0, 'msg' => 'get product is successful', 'product' => $product]);
} else {
    //get product id and place sn
    $pid = $_GET['pid'];
    $sn  = $_GET['sn'];

    //(1)get the store products on the shelves
    $sql    = "SELECT pro_list FROM place WHERE sn=" . $sn;
    $result = $mysqli->query($sql);
    if ($result === false) {
        echo $mysqli->error;
    }
    $proId = [];
    if ($row = $result->fetch_assoc()) {
        $proId = explode(',', $row['pro_list']);
    }

    //(2)reorgonized proId array and update place table
    if ($type == 'upPro') {
        array_push($proId, $pid);
        $newProId = $proId;
        // $msg ="ShelfSuccess";
        $msg = "上架成功";
    } else {
        $newProId = array_values(array_diff($proId, [$pid]));
        //$msg ="onShelfSuccess";
        $msg = "下架成功";
    }
    $proStr    = trim(implode(',', $newProId), ',');
    $up_sql    = "UPDATE place SET pro_list='" . $proStr . "' WHERE sn=" . $sn;
    $up_result = $mysqli->query($up_sql);
    if ($up_result === false) {
        echo $mysqli->error;
    }
    echo json_encode(['code' => 0, 'msg' => $msg]);
}
$mysqli->close();
