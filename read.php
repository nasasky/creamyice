<?php
include './db.php';

@$sn = $_GET["sn"];
@$addr = $_GET["addr"];

if(!empty($sn))
{
	$sql = "INSERT INTO command (sn, cmd, addr)
			VALUES('".$sn."', 71, ".$addr.")";

	$result = $mysqli->query($sql);
	if($result === false){
		echo $mysqli->error;
		echo $mysqli->errno;
	}
}
$mysqli->close();

$nexturl = "setting.php?sn=".$sn;
?>
<html>
<head>
<meta http-equiv="refresh" content="3; url=<?=$nexturl?>">   
<link rel='icon' href='images/logo.ico' type='image/x-ico' />
<link rel="stylesheet" href="css/uikit.css" />
<script src="js/uikit.js"></script>
<script src="js/uikit-icons.js"></script>
</head>
<body>
  <section style="text-align: center; padding: 5rem;">
  <div uk-spinner></div>
  <div>Reading data...</div>
  </section>
</body>
</html>
