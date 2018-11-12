<?php
require_once './Api/app_sign.php';

$account = $_POST['account'];
$password = $_POST['password'];
$username = $_POST['username'];
$time = time();

$password = md5(md5($password.'wanglong'));

$sql = "INSERT INTO `app_user`(`account`,`password`,`username`,`lastlogin`,`timestamp`) VALUES('{$account}','{$password}','{$username}',{$time},{$time})";
$result = $mysqli->query($sql);
if($result === false){
	$return_json = ['code'=>1202,'msg'=>'db server failure'];
}else{
	$return_json = ['code'=>200,'msg'=>'register app_user successful'];
}

echo json_encode($return_json);
$mysqli->close();