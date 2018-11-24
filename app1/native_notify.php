<?php
/**
 * Auth: wanglong
 * Date: 2018-06-27
 * Desc: weixin user unifiedOrder page
 */
ini_set('date.timezone', 'Asia/Shanghai');
error_reporting(0);
require_once "./wlpay/lib/WxPay.Api.php";
require_once './wlpay/lib/WxPay.Notify.php';
require_once './wlpay/example/log.php';

//Init log
$logHandler = new CLogFileHandler("./wlpay/logs/native-logs/" . date('Y-m-d') . '.log');
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
            
            if( $mysqli = $this->mysqli() ){
            	$order_id = $this->paramDecrypt($result['attach']);
                if ($this->queryIsNotify($order_id, $mysqli) == 1) {
					$time = time();
                    $sql       = "UPDATE app_order SET status=2,paytime={$time} WHERE id=".$order_id;
                    $result = $mysqli->query($sql);
                    if ($result === false) {
                        Log::DEBUG('mysql:' . $mysqli->errno . '||' . $mysqli->error);
                        $mysqli->close();
                        return false;
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
    public function queryIsNotify($order_id, $mysqli)
    {
        $sql    = "SELECT status FROM app_order WHERE id=" . $order_id;
        $result = $mysqli->query($sql);
		$status = 0;
        if ($result !== false) {
			if ($row = $result->fetch_assoc()) {
			    $status = $row['status'];
			}
        }
        return $status;
    }

    //connect mysqldb
    private function mysqli(){
    	$mysqli = new Mysqli('127.0.0.1','wanglong','wl940207','ice');
    	if($mysqli->connect_errno) return false;
    	if( !$mysqli->set_charset('UTF8') ) return false;
    	return $mysqli;
    }

    //decrypt order_id
    protected function paramDecrypt($encrypt){
        return base64_decode(urldecode(base64_decode($encrypt)));
    }
}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);

