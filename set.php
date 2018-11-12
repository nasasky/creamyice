<?php
include './db.php';

$sn = $_GET["sn"];
$addr = $_GET["addr"];
$val = $_GET["val"];

$param0 = 0;
$param1 = 0;
$param2 = 0;
if($addr == 46)
{
	$param0 = $_GET["l43"];
	$param1 = $_GET["l39"];
	$param2 = $_GET["l42"];
}
else
{
	$param0 = floor($val / 256);
	$param1 = $val % 256;
}

if(!empty($sn))
{
	$sql = "INSERT INTO command (sn, cmd, addr, param0, param1, param2) 
			VALUES('".$sn."', 83, ".$addr.", ".$param0.", ".$param1.", ".$param2.")";
			
	echo $sql;
	$result = $mysqli->query($sql);
	if($result === false){
		echo $mysqli->error;
		echo $mysqli->errno;
	}
	
	$sql = "INSERT INTO command_history (sn, cmd, addr, param0, param1, param2, source) 
		VALUES('".$sn."', 83, ".$addr.", ".$param0.", ".$param1.", ".$param2.", 'web')";
			
	echo $sql;
	$result = $mysqli->query($sql);
	if($result === false){
		echo $mysqli->error;
		echo $mysqli->errno;
	}
}
$mysqli->close();

header("Location: /setting.php?sn=".$sn);
?>
