<?php
/**
 * Author: wanglong
 * Date: 2018-06-04
 * Desc: This is a global interface verification and database connection file
 */
ini_set('date.timezone', 'Asia/Shanghai');
error_reporting(0);

//(1)Define Token Constants
define('TOKEN', '19837e4d1739579b41f5a76de9b555c8');

//(2)Check wid and Token
$wid   = $_POST['wid'];
$token = $_POST['token'];
if (empty($wid) || empty($token)) {
    exit( json_encode(['code' => 1001, 'msg' => 'sorry you dont permision access me']) );
}

if ($token !== TOKEN) {
    exit( json_encode(['code' => 1002, 'msg' => 'sorry token is error']) );
}

//(3)Mysqli Database Connecting
$mysqli = new mysqli('localhost', 'wanglong', 'wl940207', 'ice');
if ($mysqli->connect_errno) {
    exit( json_encode(['code' => 1003, 'msg' => 'Mysqli Error']) );
}
$mysqli->set_charset("utf8");

// (4) initialize return json data
$return_json = [
	'code'=>1011,
	'msg'=>'Server Failure'
];