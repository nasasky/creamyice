<?php
/**
 * Author:   wanglong
 * Date:     2018-06-28
 * Description:    this page is the order details for the customer's version of the wechat miniprogram
 */
require_once './Api/signature_db.php';
error_reporting(0);
// If there is type a value, it's a query to get a single number
if (isset($_GET['type']) && !empty($_GET['type'])) {
    $orid   = $_GET['orid'] ?: 0;
    $sql    = "SELECT number,date FROM sales_order_number WHERE orid=" . $orid . " limit 1";
    $result = $mysqli->query($sql);
    if ($result === false) {
        echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
    }
    $or_number = array();
    if ($row = $result->fetch_assoc()) {
        $or_number[] = $row;
    }
    echo json_encode(['code' => 0, 'msg' => 'get getnumber successful', 'or_number' => $or_number]);

} else {
/**
 * @ order status:
 *   1:non-paid    2：have paid              3:already received         4：already comments
 */
    $status = $_GET['status'];
    if ($status == 1) {
        $sql = "SELECT s.id orid,s.sn,s.status,s.total_amount,s.total_count,s.or_number,s.paytime,s.shiptime,place.address,place.store,s.discount,GROUP_CONCAT(p.name,'=',i.count,'=',p.price) detail
    FROM sales_order s,so_item i,customer u,product p,place WHERE s.id=i.sales_id AND s.customer_id=u.id AND i.product_id=p.id AND place.sn=s.sn AND u.id=" . $wid . " AND s.status > 1 GROUP BY s.id ORDER BY s.id DESC";

    } else {
        $sql = "SELECT s.id orid,s.sn,s.status,s.total_amount,s.total_count,s.or_number,s.paytime,s.shiptime,place.address,place.store,s.discount,GROUP_CONCAT(p.name,'=',i.count,'=',p.price) detail
    FROM sales_order s,so_item i,customer u,product p,place WHERE s.id=i.sales_id AND s.customer_id=u.id AND i.product_id=p.id AND place.sn=s.sn AND u.id=" . $wid . " AND s.status=" . $status . " GROUP BY s.id ORDER BY s.id DESC";
    }

    $result = $mysqli->query($sql);
    if ($result === false) {
        echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
    }
    $orders = array();
    while ($row = $result->fetch_assoc()) {
        $detail               = explode(',', $row['detail']);
        $row['comments_name'] = '';
        foreach ($detail as $key => $val) {
            $row['details'][$key] = explode('=', $val);
            $row['comments_name'] .= $row['details'][$key][0] . '-';
        }
        $row['comments_name'] = rtrim($row['comments_name'], '-');
        $orders[]             = $row;
    }
    echo json_encode(['code' => 0, 'msg' => 'status=' . $status . ' successful', 'orders' => $orders]);
}

$mysqli->close();
