<?php
require_once './Api/app_sign.php';

$account = isset($_POST['account'])?addslashes(trim($_POST['account'])):'';
$password = isset($_POST['password'])?trim($_POST['password']):'';

// define default response result return_json
$return_json = [
	'code'=>1011,
	'msg'=>'request parameter failure'
];


// compare account and password is match?
if($account && $password){
	$password = md5(md5($password.'wanglong'));
	$sql = "SELECT*FROM `app_user` WHERE account='{$account}' AND password='{$password}' LIMIT 1";
	$result = $mysqli->query($sql);
	if($result === false){
		$return_json = ['code'=>1202,'msg'=>'db server failure,please try later agin'];
	}else{
		/**
		 * (1) query result return success
		 *     |-1.1  query app_login_history table for last login time
		 *     |-1.2  insert into last login time to app_login_history table 
		 * (2) query result is empty and return failure
		 */
		$lastlogin = time();
		if($row = $result->fetch_assoc()){
			$user_id = $row['id'];
			// 1.1
			$sql = "SELECT `timestamp` FROM `app_login_history` WHERE `app_user_id`=".$user_id." ORDER BY `id` DESC LIMIT 1";
			$result = $mysqli->query($sql);
			
			if($result){
				if($row2 = $result->fetch_assoc()){
					$lastlogin = $row['timestamp'];
				}
			}
			$data = ['status'=>'SUCCESS','msg'=>'Hello ! Login Successful...','account'=>$row['account'],'username'=>$row['username'],'mode'=>$row['mode'],'lastlogin'=>$lastlogin];
		}else{
			$data = ['status'=>'FAILURE','msg'=>'Sorry ! Account OR Password Error...','account'=>$row['account'],'username'=>$row['username'],'mode'=>0,'lastlogin'=>$lastlogin];
		}
		$return_json = ['code'=>200,'msg'=>'quer user successful','data'=>$data];
	}
}

echo json_encode($return_json);
if($data && $data['status'] === 'SUCCESS' && $user_id){
	$sql = "INSERT INTO `app_login_history`(`app_user_id`,`ip`,`timestamp`) VALUES({$user_id},'".$_SERVER['REMOTE_ADDR']."',".time().")";
	$result = $mysqli->query($sql);
}
$mysqli->close();