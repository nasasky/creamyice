<?php
/**
 * @author wanglong <[1285505793@qq.com]>
 * Date   2018-06-28
 * Description  this page is scan QRcode for PC login admin
 */
include './Api/signature.php';
$code                = $_GET["code"];
$status              = 0;
$msg                 = "OK";
@list($sn, $barcode) = split('[-]', $code);

$ice = substr($code, 0, 3);
if ($ice === "ICE") {
    $logincode = substr($code, 3);
    $sql       = "UPDATE logincode SET status=1, wid=" . $wid . " WHERE code='" . $logincode . "'";
    $result    = $mysqli->query($sql);
    if ($result === false) {
        $status = -1;
        $msg = '数据库异常';
    } else {
        $status = 1;
        $msg    = "登录成功";
    }
} else {
    $sql    = "SELECT id from machine WHERE sn='" . $sn . "' AND barcode='" . $barcode . "'";
    $result = $mysqli->query($sql);
    if ($result === false) {
        echo $mysqli->error;
        echo $mysqli->errno;
    }
    if ($row = $result->fetch_assoc()) {

        $sql    = "SELECT id from weixin WHERE wid='" . $wid . "' AND sn='" . $sn . "'";
        $result = $mysqli->query($sql);
        if ($result === false) {
            echo $mysqli->error;
            echo $mysqli->errno;
        }
        if ($row = $result->fetch_assoc()) {
            $status = 0;
            $msg    = "请勿重复绑定";
        } else {
            $sql    = "INSERT INTO weixin (wid, sn) VALUES(" . $wid . ",'" . $sn . "')";
            $result = $mysqli->query($sql);
            if ($result === false) {
                echo $mysqli->error;
                echo $mysqli->errno;
            } else {
                $status = 1;
                $msg    = "绑定成功";
            }
        }

    } else {
        $status = 0;
        $msg    = "非法条码";
    }
}

$arr = array("msg" => $msg, "status" => $status);
echo json_encode($arr);
$mysqli->close();
