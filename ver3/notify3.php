
<?php
/*
 * @Author    wanglong    email@qq.com
 * Date      2018-06-28
 * Description  this page is notify3
 */
error_reporting(0);
include './wlpay/Base.php';
include './wlpay/Logs.php';
//Init logs
$logHandler = new CLogFileHandler("./wlpay/wllogs/" . date('Y-m-d') . '.log');
$log        = Log::Init($logHandler, 15);
class Notify extends Base
{
    private static $mysqli;
    public function __construct()
    {
        parent::__construct();
        //获取微信服务器提交过来的通知数据
        $xml = $this->getPost();
        //将XML格式的数据转换为数组
        $result = $this->XmlToArr($xml);
        Log::DEBUG('Notify:' . json_encode($result));
        //验证签名
        if ($this->checkSign($result)) {
            if (array_key_exists("return_code", $result) && array_key_exists("result_code", $result) && $result["return_code"] == "SUCCESS" && $result["result_code"] == "SUCCESS") {
                $attach = json_decode($result['attach'], true);
                //!-- Check order status
                if ($this->queryOrderStatus($attach) > 1) {
                    $notifyWechat = [
                        'return_code' => 'SUCCESS',
                        'return_msg'  => 'OK',
                    ];
                    echo $this->ArrToXml($notifyWechat);
                } else {
                    //(1)UPDATE SALES_ORDER STATUS AND NOTIFY WECHAT RESPONSE SUCCESSFUL
                    if (!$this->upOrderStatus($attach)) {
                        Log::DEBUG('UPDATE ORDER:' . $this->mysqli()->error);
                    }

                    //(2)QUERY ORDER_NUMBER FOR LAST
                    $lastNumber = $this->queryLastNumber($attach);

                    //(3)PUSH WECHATMINIPROGRAM USER TEMPLATE MESSAGE
                    $this->pushTmpMsg($attach, $result['openid'], $lastNumber);

                    //(4)UPDATE COUPON STATUS
                    $this->updateCoupon($attach);

                    $notifyWechat = [
                        'return_code' => 'SUCCESS',
                        'return_msg'  => 'OK',
                    ];
                    echo $this->ArrToXml($notifyWechat);
                }
            }
        }
    }
    protected function queryOrderStatus($attach)
    {
        $sql    = "SELECT status FROM sales_order2 WHERE id=" . $attach['orid'];
        $result = $this->mysqli()->query($sql);
        if ($result === false) {
            return false;
        }
        if ($row = $result->fetch_assoc()) {
            $status = $row['status'];
        }
        return $status;
    }
    protected function pushTmpMsg($attach, $openId, $lastNumber)
    {
        //(1)Query Order Detail BY $attach['orid']
        $detail_sql = "SELECT group_concat(p.name,'(',i.count,'个)') AS pro_name,s.total_count AS num,s.prepay_id as prepay_id,s.sn AS sn,s.total_amount AS fee,l.store AS store,p.addr AS addr,
                      p.id as pro_id FROM sales_order2 s,so_item2 i,product p,place l WHERE s.id=i.sales_id AND i.product_id=p.id AND s.sn=l.sn AND s.id={$attach['orid']} GROUP BY s.sn";

        $detail_result = $this->mysqli()->query($detail_sql);
        if ($detail_result === false) {
            Log::DEBUG('mysql:' . $this->mysqli()->errno . '||' . $this->mysqli()->error);
        }
        if ($row = $detail_result->fetch_assoc()) {
            $detail = $row;
        }
        //(2)GET WeChatMiniProgram user's access_token
        $accessToken = $this->getAccessToken(self::APPID, self::SECRET);
        //(3)Create Template Data
        $templateId = '29U-_WO-ZKWLSto-P5z-jCNZh_1REzO8lxGxcTfanTU';
        $page       = 'page/detail/detail?sn=' . $detail['sn'];
        $postData   = $this->createTemplateData($templateId, $detail['prepay_id'], $openId, $page, $lastNumber, $detail['pro_name'], $detail['num'], $detail['store'], $detail['fee']);
        //(4)Push template request
        $url    = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=" . $accessToken;
        $outPut = $this->postStr($url, $postData, 'json');
        //(5)Insert so_diary table details
        $que_sql    = "SELECT id,count,amount FROM so_diary WHERE sn=" . $detail['sn'] . " AND concat(year,'-',month,'-',day)='" . date('Y-n-j') . "' AND pro_id=" . $detail['pro_id'];
        $que_result = $this->mysqli()->query($que_sql);
        if ($row = $que_result->fetch_assoc()) {
            $newCount  = $row['count'] + $detail['num'];
            $newAmount = $row['amount'] + $detail['fee'];
            $sod_sql   = "UPDATE so_diary SET count=" . $newCount . ",amount='" . $newAmount . "',timestamp='" . date('Y-m-d H:i:s') . "' WHERE id=" . $row['id'];
        } else {
            $sod_sql = "INSERT INTO so_diary(sn,count,year,month,day,amount,pro_id)
            VALUES(" . $detail['sn'] . "," . $detail['num'] . "," . date('Y') . "," . date('n') . "," . date('j') . ",'" . $detail['fee'] . "'," . $detail['pro_id'] . ")";
        }
        $sod_result = $this->mysqli()->query($sod_sql);
        if ($sod_result === false) {
            $this->mysqli()->rollback();
            Log::DEBUG('mysql:' . $mysqli->error);
        }
        return true;
    }
    protected function updateCoupon($attach)
    {
        //(4)Update coupon status
        $cuscouponid = $attach['cuscouponid'];
        if ($cuscouponid > 0) {
            $cou_sql    = "UPDATE customer_coupon SET status=1 WHERE id=" . $cuscouponid;
            $cou_result = $this->mysqli()->query($cou_sql);
            if ($cou_result === false) {
                $this->mysqli()->rollback();
                Log::DEBUG('mysql:' . $mysqli->errno . '||' . $mysqli->error);
                return false;
            }
        }
        return true;
    }
    protected function queryLastNumber($attach)
    {
        //(2)查询最近的取单号
        $curdate    = date('Y-m-d');
        $que_sql    = "SELECT MAX(number) AS number FROM sales_order_number WHERE sn=" . $attach['sn'] . " and date='{$curdate}' limit 1";
        $que_result = $this->mysqli()->query($que_sql);
        if ($que_result === false) {
            $this->mysqli()->rollback();
            Log::DEBUG('mysql:' . $mysqli->errno . '||' . $mysqli->error);
            return false;
        }
        $number = 1000;
        if ($row = $que_result->fetch_assoc()) {
            if ($row['number'] && $row['number'] > 1000) {
                $number = $row['number'];
            }

        }
        $last_number = $number + 1;
        //(3)生成新的取单号
        $sn_sql    = "INSERT INTO sales_order_number(orid,wid,date,number,sn) VALUES({$attach['orid']},{$attach['wid']},'{$curdate}',{$last_number},{$attach['sn']})";
        $sn_result = $this->mysqli()->query($sn_sql);
        if ($sn_result === false) {
            $this->mysqli()->rollback();
            Log::DEBUG('mysql:' . $mysqli->errno . '||' . $mysqli->error);
            return false;
        }
        return $last_number;
    }
    protected function upOrderStatus($attach)
    {
        //(1)支付成功更新订单状态为status=2
        $this->mysqli()->autocommit(false);
        $paytime   = date('Y-m-d H:i:s');
        $shiptime  = date('Y-m-d H:i:s', time() + 2 * 60 * 60);
        $sql       = "UPDATE sales_order2 SET status=2,paytime='{$paytime}',shiptime='{$shiptime}' WHERE id=" . $attach['orid'];
        $up_result = $this->mysqli()->query($sql);
        if ($up_result === false) {
            $this->mysqli()->rollback();
            Log::DEBUG('mysql:' . $this->mysqli()->errno . '||' . $this->mysqli()->error);
            return false;
        }
        return true;
    }
    protected function getAccessToken($appid, $appsecret, $type = 1)
    {
        $sql    = "select*from access_token where id =" . $type;
        $result = $this->mysqli()->query($sql);
        if ($result === false) {
            $data = json_decode(file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appsecret), true);
            $time = time() + 7000;
            $sql2 = "insert into access_token(access_token,time) values('" . $data['access_token'] . "', {$time})";
        } else {
            $row  = $result->fetch_assoc();
            $time = time() + 7000;
            if (empty($row)) {
                $data = json_decode(file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appsecret), true);
                $sql2 = "insert into access_token(access_token,time) values('" . $data['access_token'] . "',{$time})";
            } else {
                if ($row['time'] > time()) {
                    $data = $row;
                } else {
                    $data = json_decode(file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $appsecret), true);
                    $sql2 = "update access_token set access_token='" . $data['access_token'] . "',time={$time} where id=" . $type;
                }
            }
        }
        if (isset($sql2) && !empty($sql2)) {
            $result2 = $this->mysqli()->query($sql2);
        }

        return $data['access_token'];
    }

    protected function createTemplateData($tempId, $preId, $openId, $page, $lastNumber, $pro_name, $num, $address, $fee)
    {
        $postData = array();
        $values   = [
            "keyword1" => [
                "value" => $lastNumber,
                "color" => "#ed5c38",
            ],
            "keyword2" => [
                "value" => $pro_name,
                "color" => "#4a4a4a",
            ],
            "keyword3" => [
                "value" => date('Y-m-d H:i:s'),
                "color" => "#4a4a4a",
            ],
            "keyword4" => [
                "value" => $fee,
                "color" => "#4a4a4a",
            ],
            "keyword5" => [
                "value" => $address,
                "color" => "#4a4a4a",
            ],
            "keyword6" => [
                "value" => $num . '个',
                "color" => "#4a4a4a",
            ],
            "keyword7" => [
                "value" => '您购买的冰淇淋已经下单成功，请您于今天（' . date('Y-m-d') . '）商户下班时间前取货',
                "color" => "#4a4a4a",
            ],
        ];
        $postData['touser']           = $openId;
        $postData['template_id']      = $tempId;
        $postData['page']             = $page;
        $postData['form_id']          = $preId;
        $postData['data']             = $values;
        $postData['emphasis_keyword'] = "keyword1.DATA";
        return $postData;
    }

    public function mysqli()
    {
        if (!self::$mysqli) {
            self::$mysqli = new mysqli('localhost', 'wanglong', 'wl940207', 'ice');
            if (self::$mysqli->connect_errno) {
                return false;
            }
            self::$mysqli->set_charset('utf8');
            if (self::$mysqli->errno) {
                return false;
            }
            return self::$mysqli;
        }
        return self::$mysqli;
    }

    public function __destruct()
    {
        if (self::$mysqli) {
            self::$mysqli->close();
        }
    }
}
Log::DEBUG('begin notify3 from ver2');
new Notify();

?>