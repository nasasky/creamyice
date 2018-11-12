<?php
include './db.php';

$sn = $_GET["sn"];
$cmd = $_GET["cmd"];

if(!empty($sn))
{
	$sql = "INSERT INTO command (sn, cmd, addr, param0, param1)
			VALUES('".$sn."', 83, 2, 0, ".$cmd.")";

	$result = $mysqli->query($sql);
	if($result === false){
		echo $mysqli->error;
		echo $mysqli->errno;
	}

	$sql = "INSERT INTO command_history (sn, cmd, addr, param0, param1, source)
		VALUES('".$sn."', 83, 2, 0, ".$cmd.", 'web')";

	$result = $mysqli->query($sql);
	if($result === false){
		echo $mysqli->error;
		echo $mysqli->errno;
	}
}
$mysqli->close();

$nexturl = "machine.php?sn=".$sn;
?>
<html>
<head>
<meta http-equiv="refresh" content="2; url=<?=$nexturl?>">   
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
