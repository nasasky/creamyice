<?php
include './db.php';

$sn = $_GET["sn"];
$cmd_str = $_GET["cmd"];

$cmd = 2;
if(!empty($cmd_str)) $cmd = intval($cmd_str);

if(!empty($sn))
{
	$sql = "INSERT INTO command (sn, cmd, addr, param0, param1)
			VALUES('".$sn."', ".$cmd.", 0, 0, 0)";

	$result = $mysqli->query($sql);
	if($result === false){
		echo $mysqli->error;
		echo $mysqli->errno;
	}

	$sql = "INSERT INTO command_history (sn, cmd, addr, param0, param1, source)
		VALUES('".$sn."', ".$cmd.", 0, 0, 0, 'web')";

	$result = $mysqli->query($sql);
	if($result === false){
		echo $mysqli->error;
		echo $mysqli->errno;
	}
}
$mysqli->close();

if($cmd==2)
{
	$nexturl = "setserverip.php?cmd=3&sn=".$sn;
}
else
{
	$nexturl = "program.php";
}
?>
<html>
<head>
<meta http-equiv="refresh" content="5; url=<?=$nexturl?>">   
</head>
<link rel='icon' href='images/logo.ico' type='image/x-ico' />
<link rel="stylesheet" href="css/uikit.css" />
<script src="js/uikit.js"></script>
<script src="js/uikit-icons.js"></script>
<body>
  <section style="text-align: center; padding: 5rem;">
  <div uk-spinner></div>
  <div>Executing...</div>
  </section>
</body>
</html>
