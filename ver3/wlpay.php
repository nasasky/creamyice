<?php
/**
 * @author [Wanglong] <[1285505793@qq.com]>
 * Date:   2018-06-28
 * Desc:   This page is wechat user unified payment
 */
require_once './Api/signature_db.php';
require_once './Api/function.php';

//(1)payment success and update order status
if (isset($_GET['type']) && !empty($_GET['type'])) {
    $orid   = (int) $_GET['orid'] ?: 0;
    $status = (int) $_GET['status'] ?: 1;
    //如果状态为2，说明是支付成功去更新订单状态
    if ($status == 2) {
        $que_sql    = "SELECT  number FROM sales_order_number WHERE orid=" . $orid;
        $que_result = $mysqli->query($que_sql);
        if ($que_result === false) {
            echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
        }
        if ($row = $que_result->fetch_assoc()) {
            $last_number = $row['number'];
        }
        echo json_encode(['code' => 0, 'msg' => 'update 订单 status=' . $status . ' successful', 'number' => $last_number]);
    }
}
//(2)use wecaht unifiedorder and return miniprogram payment paramters
else {
    require_once "./wlpay/lib/WxPay.Api.php";
    //1: accept miniprogram request
    $sn           = (int) $_POST['sn'] ?: 0;
    $store        = $_POST['store'] ?: '克瑞米艾智能机';
    $zone         = $_POST['zone'] ?: '安徽';
    $cuscouponid  = $_POST['cuscouponid'] ?: 0;
    $num          = (int) $_POST['totalNum'] ?: 1;
    $address      = $_POST['address'] ?: '中国上海';
    $total_amount = floatval($_POST['totalMoney']);
    $shopCar      = json_decode($_POST['shopCar'], true);
    $discount     = $_POST['discount'] ?: 0;
    //this is wechat payment used fee
    $fee = $total_amount * 100;
    if ( $sn == 2002090001) {
        $fee = 1;
    }
    $or_number = 'Cr-' . substr($_POST['sn'], -5) . date("YmdHis");

    $open_sql    = "SELECT openid FROM customer WHERE id=" . $wid;
    $open_result = $mysqli->query($open_sql);
    if ($open_result === false) {
        echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
    }
    if ($row = $open_result->fetch_assoc()) {
        $openId = $row['openid'];
    } else {
        echo json_encode(['code' => -8, 'msg' => 'openid is empty']);
    }

    //2: call wechat unifiedorder interface befor generater user order
    $paytime = date('Y-m-d H:i:s');
    $sql     = "INSERT INTO sales_order(or_number,sn,customer_id,total_amount,agency_policy,rate,total_count,status,
            paytime,shiptime,prepay_id,discount) VALUES('" . $or_number . "'," . $sn . "," . $wid . "," . $total_amount . ",1,1," . $num . ",1,
            '{$paytime}','{$paytime}','wanglong',{$discount})";
    $result = $mysqli->query($sql);
    if ($result === false) {
        echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error, 'res' => -2]);
    } else {
        //I: get insert last id and insert so_item table records
        $last_id = $mysqli->insert_id;
        if (!empty($shopCar) && is_array($shopCar)) {
            foreach ($shopCar as $key => $val) {
                $proAmount   = $val['price'] * $val['num'];
                $item_sql    = "INSERT INTO so_item(sales_id,product_id,count,amount,promotion_policy_id) VALUES(" . $last_id . "," . $val['addr'] . "," . $val['num'] . ",{$proAmount},1)";
                $item_result = $mysqli->query($item_sql);
                if ($item_result === false) {
                    echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
                    $error = 1;
                    break;
                }
            }
        }
        if ($error) {
            echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error, 'res' => -1]);
        } else {
            //3: generate user order success after call wechat unifiedorder
            $input = new WxPayUnifiedOrder();
            $input->SetBody("克瑞米艾-" . $name);
            $attach = json_encode(['sn' => $sn, 'wid' => $wid, 'orid' => $last_id, 'cuscouponid' => $cuscouponid]);
            $input->SetAttach($attach);
            $input->SetOut_trade_no($or_number);
            $input->SetTotal_fee($fee);
            //$input->SetTotal_fee("1");
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            //$input->SetGoods_tag("test");
            $input->SetNotify_url("https://partner.creamyice.com/ver3/notify.php");
            $input->SetTrade_type("JSAPI");
            $input->SetOpenid($openId);
            $order = WxPayApi::unifiedOrder($input);
            if ($order['return_code'] == 'SUCCESS' && $order['result_code'] == "SUCCESS") {
                //return miniprogram parametrs
                $payData    = getJsApiParameters($order);
                $pre_sql    = "UPDATE sales_order SET prepay_id ='" . $order['prepay_id'] . "' WHERE id=" . $last_id;
                $pre_result = $mysqli->query($pre_sql);
                if ($pre_result === false) {
                    echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
                }
                echo json_encode(['code' => 0, 'msg' => 'unifiedorder payment successful', 'payData' => $payData, 'sales_id' => $last_id]);
            }
        }
    }
}

$mysqli->close();
