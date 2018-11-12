<?php
$mysqli =new mysqli('localhost','wanglong','wl940207','ice');

if($mysqli->connect_errno){
  exit('Mysql Connect Error'.$mysqli->connect_error);
}

if(!$mysqli->set_charset('utf8')){
  exit('Mysql Set Charset Error'.$mysqli->error);
}

$code = $_GET["code"];
$wid = $_GET["wid"];

$status = 1;
$msg = "OK";
@list($sn,$barcode)=split('[-]',$code);

$ice = substr($code, 0, 3);
if($ice === "ICE")
{
  $logincode = substr($code, 3);
  $sql = "UPDATE logincode SET status=1,wid={$wid} WHERE code='".$logincode."'";
  $result = $mysqli->query($sql);
  if($result === false){
  	$code = $mysqli->errno;
    $msg = $mysqli->error;
  }
  else
  {
    $status = 1;
    $msg = "登录成功";
  }
}
else
{
  $sql = "SELECT id from machine WHERE sn='".$sn."' AND barcode='".$barcode."'";
  $result = $mysqli->query($sql);
  if($result === false){
  	echo $mysqli->error;
  	echo $mysqli->errno;
  }
  if($row = $result->fetch_assoc()) {

  	$sql = "SELECT id from weixin WHERE wid='".$wid."' AND sn='".$sn."'";
  	$result = $mysqli->query($sql);
  	if($result === false){
  		$code = $mysqli->errno;
      $msg = $mysqli->error;
  	}
  	if($row = $result->fetch_assoc()) {
  		$status = 0;
  		$msg = "请勿重复绑定";
  	}
  	else
  	{
  		$sql = "INSERT INTO weixin (wid, sn) VALUES(".$wid.",'".$sn."')";
  		$result = $mysqli->query($sql);
  		if($result === false){
  			$code = $mysqli->errno;
        $msg = $mysqli->error;
  		}
  		else
  		{
  			$status = 1;
  			$msg = "绑定成功";
  		}
  	}

  }
  else
  {
  	$status = 0;
  	$msg = "非法条码";
  }
}


$arr = Array("msg"=>$msg, "status"=>$status, 'code'=>$code);
echo json_encode($arr);
$mysqli->close();
?>
