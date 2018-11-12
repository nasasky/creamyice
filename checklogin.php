<?php
//include './db.php';
$mysqli = new mysqli('localhost', 'wanglong', 'wl940207', 'ice');

if($mysqli->connect_errno){
	exit('Mysql Connect Error'.$mysqli->connect_error);
}

if(!$mysqli->set_charset('utf8')){
	exit('Mysql Set Charset Error'.$mysqli->error);
}

$logincode = $_GET['code'];
$sql = "SELECT wid FROM logincode WHERE code='".$logincode."' AND status=1";
$result = $mysqli->query($sql);
if($result === false){
  echo $mysqli->error;
  echo $mysqli->errno;
}

$status = 0;
if($row = $result->fetch_assoc()){
	$wid = $row['wid'];
	$_SESSION['wid'] = $wid;
    $status = 1;
    
}

$mysqli->close();

$arr = Array("status"=>$status);
echo json_encode($arr);
?>
