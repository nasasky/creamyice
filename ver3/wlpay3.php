<?php
/**
 * Author: wanglong
 * Date: 2018-06-06
 * Desc: This Is Payment file
 */
require_once './Api/signature_db.php';
//根据type类型判断是支付请求生成订单还是支付成功更新订单,更新订单的时候同时更新优惠券状态为1已使用状态
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
//这是调用统一下单API并向小程序返回支付api必须参数
else {
    require './wlpay/Base.php';
    require './wlpay/Logs.php';
    //初始化日志
    $logHandler = new CLogFileHandler("./wlpay/logs/" . date('Y-m-d') . '.log');
    $log        = Log::Init($logHandler, 15);
    class miniPay extends Base
    {
        public $unifiedOrder = [];
        public $lastId       = 0;
        /**
         * [__construct description]
         * @param get unifiedOrder and set class attribute
         */
        public function __construct($mysqli, $params, $wid, $sn, $total_amount, $num, $addr, $cuscouponid)
        {
            //(1)make order
            $last_id                    = $this->makeOrder($mysqli, $params['out_trade_no'], $sn, $wid, $total_amount, $num, $addr);
            $params['appid']            = self::APPID;
            $params['mch_id']           = self::MCHID;
            $params['notify_url']       = self::NOTIFY;
            $params['nonce_str']        = md5(microtime());
            $params['spbill_create_ip'] = $_SERVER['SERVER_ADDR'];
            $params['trade_type']       = 'JSAPI';
            $params['openid']           = $this->getOpenId($mysqli, $wid);
            $params['attach']           = json_encode(['sn' => $sn, 'wid' => $wid, 'orid' => $last_id, 'cuscouponid' => $cuscouponid]);
            //(2)call wechat unifiedorder
            $arr                = $this->unifiedorder($params);
            $this->unifiedOrder = $arr;
            $this->lastId       = $last_id;
            //(3)update order's prepay_id fields
            $this->updateOrder($mysqli, $last_id, $arr['prepay_id']);
        }
        /**
         * [getJsApiParameters description]
         * @return building miniProgram wxPayment parameters
         */
        public function getJsApiParameters()
        {
            $unifiedOrder        = $this->unifiedOrder;
            $params              = [];
            $params['appId']     = $unifiedOrder['appid'];
            $params['timeStamp'] = (string) time();
            $params['nonceStr']  = md5(microtime());
            $params['package']   = 'prepay_id=' . $unifiedOrder['prepay_id'];
            $params['signType']  = 'MD5';
            $params['paySign']   = $this->getSign($params);
            return $params;
        }
        /**
         * [makeOrder description]
         * @return Make an Order and call Wechat UnifiedOrder and return orderId
         */
        public function makeOrder($mysqli, $or_number, $sn, $wid, $total_amount, $num, $addr)
        {
            //(1)Insert sales_order
            $paytime = date('Y-m-d H:i:s');
            $sql     = "INSERT INTO sales_order(or_number,sn,customer_id,total_amount,agency_policy,rate,total_count,status,paytime,shiptime,prepay_id)
        VALUES('" . $or_number . "'," . $sn . "," . $wid . "," . $total_amount . ",1,1," . $num . ",1,'{$paytime}','{$paytime}','wanglong')";
            $result = $mysqli->query($sql);
            if ($result === false) {
                return false;
            }
            //(2)Insert so_item
            $last_id     = $mysqli->insert_id;
            $item_sql    = "INSERT INTO so_item(sales_id,product_id,count,amount,promotion_policy_id) VALUES(" . $last_id . ",{$addr},{$num},{$total_amount},1)";
            $item_result = $mysqli->query($item_sql);
            if ($item_result === false) {
                return false;
            }
            return $last_id;
        }
        /**
         * [getOpenId description]
         * @return openid
         */
        public function getOpenId($mysqli, $wid)
        {
            $open_sql    = "SELECT openid FROM customer WHERE id=" . $wid;
            $open_result = $mysqli->query($open_sql);
            if ($open_result === false) {
                return false;
            }
            if ($row = $open_result->fetch_assoc()) {
                $openId = $row['openid'];
            } else {
                $openId = '';
            }
            return $openId;
        }
        /**
         * [updateOrder description]
         */
        private function updateOrder($mysqli, $last_id, $prepay_id)
        {
            $pre_sql    = "UPDATE sales_order SET prepay_id ='" . $prepay_id . "' WHERE id=" . $last_id;
            $pre_result = $mysqli->query($pre_sql);
            if ($pre_result === false) {
                return false;
            }
            return true;
        }
    }
    //(1)获取小程序post过来的订单相关数据
    $sn           = (int) $_POST['sn'] ?: 0;
    $store        = $_POST['store'] ?: '克瑞米艾智能机';
    $zone         = $_POST['zone'] ?: '安徽';
    $name         = $_POST['name'] ?: '王龙甜筒';
    $cuscouponid  = $_POST['cuscouponid'] ?: 0;
    $num          = (int) $_POST['num'] ?: 1;
    $addr         = (int) $_POST['addr'] ?: 39;
    $address      = $_POST['address'] ?: '中国上海';
    $price        = floatval($_POST['price']);
    $total_amount = floatval($_POST['total_price']);
    $fee          = $total_amount * 100; //weixin fee
    $or_number    = 'Cr-' . substr($_POST['sn'], -5) . date("YmdHis");

    //(2)build params and call unifiedorder
    $params = [
        'body'         => '克瑞米艾-' . $name,
        'out_trade_no' => $or_number,
        'total_fee'    => $fee,
    ];
    $obj = new miniPay($mysqli, $params, $wid, $sn, $total_amount, $num, $addr, $cuscouponid);
    //(3)return miniprogram payment params
    $payData = $obj->getJsApiParameters();
    echo json_encode(['code' => 0, 'msg' => 'unifiedorder payment successful', 'payData' => $payData, 'sales_id' => $obj->lastId]);
}

$mysqli->close();
