<?php
/**
 * Auth: wanglong
 * Date: 2018-06-27
 * Desc: weixin user unifiedOrder page
 */
date_default_timezone_set('RPC');
require_once "./wlpay/lib/WxPay.Api.php";
require_once './wlpay/lib/WxPay.Notify.php';
require_once './wlpay/example/log.php';
require_once './Api/function.php';
//Init log
$logHandler = new CLogFileHandler("./wlpay/logs/" . date('Y-m-d') . '.log');
$log        = Log::Init($logHandler, 15);
class PayNotifyCallBack extends WxPayNotify
{
    //query order
    public function Queryorder($transaction_id)
    {
        $input = new WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = WxPayApi::orderQuery($input);
        Log::DEBUG("query:" . json_encode($result));
        if (array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS") {
            //connect mysqldb
            $mysqli = new mysqli('localhost', 'wanglong', 'wl940207', 'ice');
            if ($mysqli->connect_errno) {
                Log::DEBUG('mysql:' . $mysqli->errno . '||' . $mysqli->error);
            } else {
                //Set charset
                if (!$mysqli->set_charset("utf8")) {
                    Log::DEBUG('mysql:' . $mysqli->errno . '||' . $mysqli->error);
                } else {
                    $attach = json_decode($result['attach'], true);
                    if ($this->queryIsNotify($attach, $mysqli) == 1) {
                        //(1)update order status=2 for pay success
                        $mysqli->autocommit(false);
                        $paytime   = date('Y-m-d H:i:s');
                        $shiptime  = date('Y-m-d H:i:s', time() + 24 * 60 * 60);
                        $sql       = "UPDATE sales_order SET status=2,paytime='{$paytime}',shiptime='{$shiptime}' WHERE id=" . $attach['orid'];
                        $up_result = $mysqli->query($sql);
                        if ($up_result === false) {
                            $mysqli->rollback();
                            $mysqli->autocommit(true);
                            Log::DEBUG('mysql:' . $mysqli->errno . '||' . $mysqli->error);
                        }
                        //（2）Before generating an ordered receipt number, check out the nearest receipt number.
                        $curdate    = date('Y-m-d');
                        $que_sql    = "SELECT MAX(number) AS number FROM sales_order_number WHERE sn=" . $attach['sn'] . " and date='{$curdate}' limit 1";
                        $que_result = $mysqli->query($que_sql);
                        if ($que_result === false) {
                            $mysqli->rollback();
                            $mysqli->autocommit(true);
                            Log::DEBUG('mysql:' . $mysqli->errno . '||' . $mysqli->error);
                        }
                        $number = 1000;
                        if ($row = $que_result->fetch_assoc()) {
                            if ($row['number'] && $row['number'] > 1000) {
                                $number = $row['number'];
                            }

                        }
                        $last_number = $number + 1;

                        $sn_sql    = "INSERT INTO sales_order_number(orid,wid,date,number,sn) VALUES({$attach['orid']},{$attach['wid']},'{$curdate}',{$last_number},{$attach['sn']})";
                        $sn_result = $mysqli->query($sn_sql);
                        if ($sn_result === false) {
                            $mysqli->rollback();
                            $mysqli->autocommit(true);
                            Log::DEBUG('mysql:' . $mysqli->errno . '||' . $mysqli->error);
                        }

                        //（3）update the customer coupon state to be used
                        $cuscouponid = $attach['cuscouponid'];
                        if ($cuscouponid > 0) {
                            $cou_sql    = "UPDATE customer_coupon SET status=1 WHERE id=" . $cuscouponid . " AND coupon_id<>2";
                            $cou_result = $mysqli->query($cou_sql);
                            if ($cou_result === false) {
                                $mysqli->rollback();
                                $mysqli->autocommit(true);
                                Log::DEBUG('mysql:' . $mysqli->errno . '||' . $mysqli->error);
                            }
                        }
                        //（4）Payment successfully invoked WeChat template message file to push messages to users to facilitate user viewing
                        $detail_sql = "SELECT p.name,s.total_count AS num,i.count,i.amount,s.prepay_id as prepay_id,s.sn AS sn,s.total_amount AS fee,l.store AS store,p.addr AS addr,
                        p.id as pro_id,s.discount FROM sales_order s,so_item i,product p,place l WHERE s.id=i.sales_id AND i.product_id=p.id AND s.sn=l.sn AND s.id=" . $attach['orid'];
                        $detail_result = $mysqli->query($detail_sql);
                        if ($detail_result === false) {
                            Log::DEBUG('mysql:' . $mysqli->errno . '||' . $mysqli->error);
                        }

                        while ($row = $detail_result->fetch_assoc()) {
                            $detail[] = $row;
                            $sn       = $row['sn'];
                            $num      = $row['num'];
                            $pro_name .= $row['name'] . '(' . $row['count'] . '),';
                            $prepay_id = $row['prepay_id'];
                            $fee       = $row['fee'];
                            $store     = $row['store'];
                            $discount  = $row['discount'];
                        }
                        $appid       = 'wx16cfcfded8eb72d2';
                        $appsecret   = '975e906d69447e41ab50cfb78c4db36b';
                        $accessToken = getAccessToken($appid, $appsecret, $mysqli);
                        $mysqli->commit();
                        $mysqli->autocommit(true);
                        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=" . $accessToken;
                        //The package post parameters are transmitted to the WeChat template API
                        $templateId = '29U-_WO-ZKWLSto-P5z-jCNZh_1REzO8lxGxcTfanTU';
                        $openId     = $result['openid'];
                        $page       = 'page/detail/detail?sn=' . $sn;

                        $postData = createTemplateData($templateId, $prepay_id, $openId, $page, $last_number, $pro_name, $num, $store, $fee);
                        //send template message by curl request
                        $outPut = https_curl_json($url, $postData, 'json');

                        //(5)Insert so_diary table details
                        foreach ($detail as $key => $val) {
                            if ($key == 0) {
                                $val['amount'] = $val['amount'] - $val['discount'];
                            }

                            $que_sql    = "SELECT id,count,amount FROM so_diary WHERE sn=" . $val['sn'] . " AND concat(year,'-',month,'-',day)='" . date('Y-n-j') . "' AND pro_id=" . $val['pro_id'];
                            $que_result = $mysqli->query($que_sql);
                            if ($row = $que_result->fetch_assoc()) {
                                $newCount  = $row['count'] + $val['count'];
                                $newAmount = $row['amount'] + $val['amount'];
                                $sod_sql   = "UPDATE so_diary SET count=" . $newCount . ",amount='" . $newAmount . "',timestamp='" . date('Y-m-d H:i:s') . "' WHERE id=" . $row['id'];
                            } else {
                                $sod_sql = "INSERT INTO so_diary(sn,count,year,month,day,amount,pro_id)
                              VALUES(" . $val['sn'] . "," . $val['count'] . "," . date('Y') . "," . date('n') . "," . date('j') . ",'" . $val['amount'] . "'," . $val['pro_id'] . ")";
                            }
                            $sod_result = $mysqli->query($sod_sql);
                            if ($sod_result === false) {
                                Log::DEBUG('mysql:' . $mysqli->error);
                            }
                        }
                        $mysqli->close();
                    }
                }
            }

            return true;
        }
        return false;
    }

    //rewrite callback function
    public function NotifyProcess($data, &$msg)
    {
        Log::DEBUG("call back:" . json_encode($data));
        $notfiyOutput = array();

        if (!array_key_exists("transaction_id", $data)) {
            $msg = "enter paramter is error";
            return false;
        }
        //Query order judgment authenticity
        if (!$this->Queryorder($data["transaction_id"])) {
            $msg = "order query failure";
            return false;
        }
        return true;
    }
    //check WeChat is notice. distinct
    public function queryIsNotify($attach, $mysqli)
    {
        $sql    = "SELECT status FROM sales_order WHERE id=" . $attach['orid'];
        $result = $mysqli->query($sql);
        if ($result === false) {
            return false;
        }
        if ($row = $result->fetch_assoc()) {
            $status = $row['status'];
        }
        return $status;
    }
}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
