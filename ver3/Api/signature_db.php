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
define('LOGINTIME', time() + 2 * 60 * 60); //Setting miniprogram effective time
define('SHIPTIME', time() - 2 * 60 * 60); //Setting order shipTime for display page
define('INTERVAL', 1000 * 12); //Setting timer long
define('DISTANCE', 5); //Setting location distance
define('SIZE', 10); //Set page size
define('RANGE', 2); //Setting up the merchant's available range

//(2)Mysqli Database Connecting
$mysqli = new mysqli('127.0.0.1', 'root', 'wl940207', 'ice');
if ($connect_errno = $mysqli->connect_errno) {
    exit(json_encode(['code' => $connect_errno, 'msg' => $mysqli->connect_error]));
}
$mysqli->set_charset("utf8");

//(3)Check wid and Token
$wid   = $_GET['wid'];
$token = $_GET['token'];
if (empty($wid) || empty($token)) {
	$mysqli->close();
    exit(json_encode(['code' => 250, 'msg' => 'sorry you dont permision access me']));
}

if ($token !== TOKEN) {
	$mysqli->close();
    exit(json_encode(['code' => 438, 'msg' => 'sorry token is error']));
}
