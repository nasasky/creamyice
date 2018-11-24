<?php
/**
 * Author: wanglong
 * Date: 2018-06-04
 * Desc: This is a global interface verification and database connection file
 */
ini_set('date.timezone', 'Asia/Shanghai');
error_reporting(E_ALL);

//(1)Define Token Constants
define('TOKEN', '19837e4d1739579b41f5a76de9b555c8');
//(2)Check wid and Token
$wid   = isset($_POST['wid'])?$_POST['wid']:'';
$token = isset($_POST['token'])?$_POST['token']:'';
$sn = isset($_POST['sn'])?$_POST['sn']:'';

if (empty($wid) || empty($token)) {
    exit( json_encode(['code' => 1001, 'msg' => 'sorry you dont permision access me']) );
}

if ($token !== TOKEN) {
    exit( json_encode(['code' => 1002, 'msg' => 'sorry token is error']) );
}

if(!$sn){
	exit(json_encode(['code'=>1003,'msg'=>'machine is no authorization']));
}

//(3)Mysqli Database Connecting
$mysqli = new mysqli('127.0.0.1', 'wanglong', 'wl940207', 'ice');
//$mysqli = new mysqli('47.96.106.128', 'ice', 'ice731', 'ice');
if ($mysqli->connect_errno) {
    exit( json_encode(['code' => 1200, 'msg' => 'Mysqli Connection Error']) );
}
$mysqli->set_charset("utf8");

function param_encrypt($param){
	return base64_encode(urlencode(base64_encode($param)));
}

function param_decrypt($encrypt){
	return base64_decode(urldecode(base64_decode($encrypt)));
}
