<?php
/**
 * Auth: wanglong
 * Date: 2018-06-27
 * Desc: coupons description
 * @status 0 未使用  1 已使用   2 已过期
 */
require_once './Api/signature_db.php';

$type = $_GET['type'];
$date = date('Y-m-d');
if ($type == 'queryCount') {
    //$sql ="SELECT count(c.id) count FROM customer_coupon c,coupon p WHERE c.coupon_id=p.id AND c.customer_id={$wid} AND status=0 AND p.end_date>='".date('Y-m-d')."'";
    $sql    = "SELECT count(c.id) count FROM customer_coupon c WHERE c.customer_id={$wid} AND status=0 AND c.expire_time>='{$date}'";
    $result = $mysqli->query($sql);
    if ($result === false) {
        echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
    }
    $row = $result->fetch_assoc();
    echo json_encode(['code' => 0, 'msg' => 'query coupon status=0 count successful', 'count' => $row['count']]);
}
//query customer all coupons
elseif ($type == 'queryCoupons') {
    /*$count_sql ="SELECT count(c.id) count FROM customer_coupon c,coupon p WHERE c.coupon_id=p.id AND c.customer_id={$wid} AND p.end_date>='".date('Y-m-d')."' AND c.status=0
    UNION ALL SELECT count(c.id) count FROM customer_coupon c WHERE c.customer_id={$wid} AND c.status=1 UNION ALL SELECT count(c.id) count FROM customer_coupon c,coupon p
    WHERE c.coupon_id=p.id AND c.customer_id={$wid} AND p.end_date<'".date('Y-m-d')."' AND c.status=0";*/
    $count_sql = "SELECT count(c.id) count FROM customer_coupon c WHERE c.customer_id={$wid} AND c.expire_time>='" . date('Y-m-d') . "' AND c.status=0
            UNION ALL SELECT count(c.id) count FROM customer_coupon c WHERE c.customer_id={$wid} AND c.status=1 UNION ALL SELECT count(c.id) count FROM customer_coupon c
            WHERE c.customer_id={$wid} AND c.expire_time<'" . date('Y-m-d') . "' AND c.status=0";

    $count_result = $mysqli->query($count_sql);
    if ($count_result === false) {
        $counts = 'error';
    } else {
        while ($row = $count_result->fetch_assoc()) {
            $count[] = $row['count'];
        }
        $counts[0] = '已使用(' . $count[1] . ')';
        $counts[1] = '未使用(' . $count[0] . ')';
        $counts[2] = '已过期(' . $count[2] . ')';
    }
    $status = (int) $_GET['status'];
    switch ($status) {
        //available coupons
        case 1:
            /*$sql = "SELECT coupon.name,coupon.start_date,coupon.end_date,coupon.description,coupon.amount,customer_coupon.status,customer_coupon.id cuscouponid,coupon.condition  FROM coupon,customer,customer_coupon WHERE coupon.id=customer_coupon.coupon_id AND customer.id=customer_coupon.customer_id AND customer.id ={$wid} AND customer_coupon.status=0 AND coupon.end_date>='".date('Y-m-d')."'";*/
            $sql = "SELECT coupon.name,customer_coupon.expire_time,coupon.description,coupon.amount,customer_coupon.status,customer_coupon.id cuscouponid,coupon.condition FROM coupon,customer_coupon
    		WHERE coupon.id=customer_coupon.coupon_id AND customer_coupon.customer_id={$wid} AND customer_coupon.status=0 AND customer_coupon.expire_time>='{$date}'";
            break;
        //expired coupons
        case 2:
            $sql = "SELECT coupon.name,customer_coupon.expire_time,coupon.description,coupon.amount,customer_coupon.status,customer_coupon.id cuscouponid,coupon.condition FROM coupon,customer_coupon
    	    WHERE coupon.id=customer_coupon.coupon_id AND customer_coupon.customer_id={$wid} AND customer_coupon.status=0 AND customer_coupon.expire_time<'{$date}'";
            break;
        //already used coupons
        default:
            $sql = "SELECT coupon.name,customer_coupon.expire_time,coupon.description,coupon.amount,customer_coupon.status,customer_coupon.id cuscouponid,coupon.condition  FROM coupon,customer_coupon
    		WHERE coupon.id=customer_coupon.coupon_id AND customer_coupon.customer_id={$wid} AND customer_coupon.status=1";
            break;
    }
    $result = $mysqli->query($sql);
    if ($result === false) {
//执行失败
        echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
    } else {
        $coupons = array();
        while ($row = $result->fetch_assoc()) {
            $coupons[] = $row;
        }
        if (isset($coupons) || isset($counts)) {
            echo json_encode(['code' => 0, 'msg' => 'query coupon successful', 'coupons' => $coupons, 'counts' => $counts]);
        } else {
            echo json_encode(['code' => -1, 'msg' => 'query coupon is empty']);
        }
    }
}
//query Red envelopes that can be used
elseif ($type == 'queryUse') {
    $totalMoney = $_GET['totalMoney'];
    $sql        = "SELECT coupon.name,customer_coupon.expire_time,coupon.description,coupon.amount,customer_coupon.status,customer_coupon.id cuscouponid,coupon.condition  FROM coupon,customer_coupon
	WHERE coupon.id=customer_coupon.coupon_id AND customer_coupon.customer_id={$wid} AND customer_coupon.status=0 AND coupon.condition<={$totalMoney} AND customer_coupon.expire_time>='{$date}'
	ORDER BY coupon.amount DESC";
    $result = $mysqli->query($sql);
    if ($result === false) {
//执行失败
        echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
    } else {
        $coupons = array();
        while ($row = $result->fetch_assoc()) {
            $coupons[] = $row;
        }
        if (isset($coupons) && !empty($coupons)) {
            echo json_encode(['code' => 0, 'msg' => 'query coupon successful', 'coupons' => $coupons]);
        } else {
            echo json_encode(['code' => -1, 'msg' => 'query coupon is empty']);
        }
    }
}
//query Red envelopes that can be used Maximum money
elseif ($type == 'queryMax') {
    $totalMoney = $_GET['totalMoney'];
    $sql        = "SELECT coupon.amount discount,customer_coupon.id cuscouponid FROM coupon,customer_coupon WHERE coupon.id=customer_coupon.coupon_id  AND customer_coupon.customer_id={$wid}
	AND customer_coupon.status=0 AND coupon.condition<=" . $totalMoney . " AND customer_coupon.expire_time>='{$date}' ORDER BY coupon.amount DESC LIMIT 1";
    $result = $mysqli->query($sql);
    if ($result === false) {
//执行失败
        echo json_encode(['code' => 0, 'msg' => 'query coupon is successful', 'redPack' => 0, 'couponId' => 0]);
    } else {
        $redPack  = 0;
        $couponId = 0;
        while ($row = $result->fetch_assoc()) {
            $redPack  = $row['discount'];
            $couponId = $row['cuscouponid'];
        }
        echo json_encode(['code' => 0, 'msg' => 'query coupon is successful', 'redPack' => $redPack, 'couponId' => $couponId]);
    }
}
$mysqli->close();
