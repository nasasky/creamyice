<?php
/**
 * Author: WangLong
 * Date: 2018-06-21
 * Desc: This file is send coupon to customer
 */
require './Api/signature_db.php';

/**
 * Coupon Status Description
 * Status: 0 1 2 3
 * s(0): Available
 * s(1): Already used
 * s(2): Expired
 * s(3): Freeze
 */
$type        = $_GET['type'];
$code        = 0;
$msg         = 'Send coupon is Successful';
$new_coupons = [];
switch ($type) {
    case 'getRandCoupons':
        $identi = $_GET['identi']; //unique order identified
        $fromid = $_GET['fromid']; //source from user identified
        //(1)query customer_coupons check user is already receive coupons
        $sql    = "SELECT c.id,c.amount,c.condition,c.name,c.description,s.expire_time FROM coupon c,customer_coupon s WHERE c.id=s.coupon_id AND customer_id={$wid} AND share_id={$identi}";
        $result = $mysqli->query($sql);
        if ($result === false) {
            $code = $mysqli->errno;
            $msg  = $mysqli->error;
            break;
        }
        while ($row = $result->fetch_assoc()) {
            $row['is_receive'] = true;
            $new_coupons[]     = $row;
        }
        if ($new_coupons) {
            $code = 1;
            $msg  = 'coupon is receive';
            break;
        }
        //(2)the share page is first receive
        $str    = '45678945454545454545445544454445456666666444';
        $length = strlen($str) - 1;
        $counts = mt_rand(1, 2);
        $sql = '';
        for ($i = 0; $i < $counts; $i++) {
            $coupon_id = $str[mt_rand(0,$length)];
            if($i == 0) $sql = "SELECT c.id,c.amount,c.condition,c.name,c.description FROM coupon c WHERE c.id=".$coupon_id;
            else $sql .= " UNION ALL SELECT c.id,c.amount,c.condition,c.name,c.description FROM coupon c WHERE c.id =".$coupon_id;
        }
        //$sql = "SELECT c.id,c.amount,c.condition,c.name,c.description FROM coupon c WHERE c.id>3 ORDER BY c.amount ASC";
        $result = $mysqli->query($sql);
        if ($result === false) {
            $code = $mysqli->errno;
            $msg  = $mysqli->error;
        }
        $date = date('Y-m-d', time() + 7 * 24 * 3600);
        while ($row = $result->fetch_assoc()) {
            $row['expire_time'] = $date;
            $new_coupons[]          = $row;
        }
        /*$len = count($coupons) - 1;
        $max = mt_rand(1, 2);
        for ($i = 0; $i < $max; $i++) {
            $rand          = mt_rand(0, $len);
            $new_coupons[] = $coupons[$rand];
        }*/
        $code = 2;
        break;
    case 'insertCustomerCoupons':
        $new_coupons = json_decode($_GET['new_coupons'], true);
        $fromid      = $_GET['fromid'];
        $identi      = $_GET['identi'];
        foreach ($new_coupons as $key => $val) {
            $sql    = "INSERT INTO customer_coupon(customer_id,coupon_id,expire_time,share_id) VALUES({$wid},{$val['id']},'" . $val['expire_time'] . "',{$identi})";
            $result = $mysqli->query($sql);
            if ($result === false) {
                $code = $mysqli->errno;
                $msg  = $mysqli->error;
                break;
            }
        }
        break;
    default:
        $expire_time = date('Y-m-d', time() + 7 * 24 * 3600);
        //(1)send coupon to customer
        $sql    = "INSERT INTO customer_coupon(customer_id,coupon_id,expire_time) VALUES({$wid},2,$expire_time)";
        $result = $mysqli->query($sql);

        if ($result === false) {
            $code = $mysqli->errno;
            $msg  = $mysqli->error;
        }

        //(2)update customer for already ship coupon
        $sql    = "UPDATE customer SET couponid=1 WHERE id =" . $wid;
        $result = $mysqli->query($sql);
        if ($result === false) {
            $code = $mysqli->errno;
            $msg  = $mysqli->error;
        }
        break;
}

echo json_encode(['code' => $code, 'msg' => $msg, 'new_coupons' => $new_coupons]);
//(2) asynchronous exec insert customer_coupon
if ($code == 2 && $new_coupons) {
    foreach ($new_coupons as $key => $val) {
        $sql    = "INSERT INTO customer_coupon(customer_id,coupon_id,expire_time,share_id) VALUES({$wid},{$val['id']},'" . $val['expire_time'] . "',{$identi})";
        $result = $mysqli->query($sql);
        if ($result === false) {
            file_put_contents('./log.txt', 'sendCouponInfo-(' . date('Ymd H:i:s') . ') :' . $mysqli->errno . ":" . $mysqli->error . "\r\n");
            break;
        }
    }
}
$mysqli->close();
