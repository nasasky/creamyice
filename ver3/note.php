<?php
/**
 * push template message
 * @author  wanglong
 * @date 2018-06-27
 ***/
define('TOKEN', '19837e4d1739579b41f5a76de9b555c8');

if ($_POST['token'] !== TOKEN) {
    exit(json_encode(['code' => 250, 'msg' => 'Sorry you dont permision follow me']));
}

if (empty($_POST['mobile'])) {
    exit(json_encode(['code' => 240, 'msg' => 'Sorry mobile is not empty']));
}

$mobile = $_POST['mobile'];
//use demo
require './Api/ServerAPI.php';
//sae appkey
$AppKey = '9884adf1cf2e621273ec7ff38e551029';
//sae appsecret
$AppSecret = '2f16e7af80db';
$p         = new ServerAPI($AppKey, $AppSecret, 'fsockopen'); //fsockopen伪造请求

//send note code  note:4072689  voice:3882682
$result = $p->sendSmsCode('4072689', $mobile, '', '5');
exit(json_encode(['code' => 0, 'msg' => 'success', 'result' => $result]));

//send template note message
print_r($p->sendSMSTemplate('6272', array('13888888888', '13666666666'), array('xxxx', 'xxxx')));
