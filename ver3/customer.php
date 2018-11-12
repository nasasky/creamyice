<?php
/**
 * Author: wanglong
 * Date: 2018-06-27
 * Desc: This is user login and register script
 */
require_once './Api/signature_db.php';

//user login by mobile
if (isset($_POST['mobile']) && !empty($_POST['mobile'])) {
    $mo_user = [];
    $mobile  = $_POST['mobile'];
    $sql     = "SELECT id,name,mobile FROM users WHERE mobile=" . $mobile;
    $result  = $mysqli->query($sql);
    if ($result === false) {
        echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
    }
    //if there is a record, it's landing
    if ($row = $result->fetch_assoc()) {
        $mo_user = $row;
        $sql     = "UPDATE users SET last_login_time=" . time() . " WHERE mobile=" . $mobile;
        $result  = $mysqli->query($sql);
        if ($result === false) {
            echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
        }
    }
    //if no record,it's register
    else {
        $str   = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $nonce = '';
        for ($i = 0; $i < 8; $i++) {
            $nonce .= $str[mt_rand(0, 61)];
        }
        $name   = 'Ne' . date('m-d') . $nonce;
        $sql    = "INSERT INTO users(name,mobile,registe_time,login_time) VALUES('{$name}',$mobile," . time() . "," . time() . ")";
        $result = $mysqli->query($sql);
        if ($result === false) {
            echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
        }
        $sql    = "SELECT id,name,mobile FROM users WHERE mobile=" . $mobile;
        $result = $mysqli->query($sql);
        if ($result === false) {
            echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
        }

        if ($row = $result->fetch_assoc()) {
            $mo_user = $row;
        }
    }
    $mysqli->close();
    http_response_code(201);
    echo json_encode(['code' => 0, 'msg' => 'success', 'mo_user' => $mo_user]);

}
//user login and register by weixin authentication
else {
    $user      = $_GET["user"] ?: '神秘的我';
    $code      = $_GET["code"];
    $wid       = -1;
    $appid     = 'wx16cfcfded8eb72d2';
    $appsecret = '975e906d69447e41ab50cfb78c4db36b';
    if (empty($code)) {
        echo json_encode(['code' => -5, 'msg' => 'query code is error']);
    }

    $session_url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret=' . $appsecret . '&js_code=' . $code . '&grant_type=authorization_code';
    $session     = json_decode(file_get_contents($session_url));
    if (isset($session->errcode)) {
        echo json_encode(['code' => $session->errcode, 'msg' => $session->errmsg]);
    } else {

        $sql    = "SELECT id,couponid FROM customer WHERE openid='" . $session->openid . "'";
        $result = $mysqli->query($sql);
        if ($result === false) {
            echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
        }
        $couponid = 1;
        if ($row = $result->fetch_assoc()) {
            $wid = $row["id"];
            //$couponid =$row['couponid'];
            $sql    = "UPDATE customer SET lastlogin=CURRENT_TIMESTAMP(), nickname='" . $user . "' WHERE openid='" . $session->openid . "'";
            $result = $mysqli->query($sql);
            if ($result === false) {
                echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
            }
        } else {
            $sql    = "INSERT INTO customer (openid, nickname) VALUES('" . $session->openid . "', '" . $user . "')";
            $result = $mysqli->query($sql);
            if ($result === false) {
                echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
            }
            $wid = $mysqli->insert_id;
            /*$sql = "SELECT id FROM customer WHERE openid='".$session->openid."'";
        $result = $mysqli->query($sql);
        if($result === false){
        echo json_encode(['code'=>$mysqli->errno,'msg'=>$mysqli->error]);
        }

        if($row = $result->fetch_assoc()){
        $wid = $row["id"];
        }*/
        }
    }
    $mysqli->close();
    http_response_code(201);
    echo json_encode(['code' => 0, 'msg' => 'success', 'wid' => $wid, 'couponid' => $couponid, 'desc' => '克瑞米艾送红包啦：恭喜您获得满20元减10元红包，快快去使用吧！']);
}
