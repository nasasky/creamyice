<?php
include './db.php';

$sn = $_GET["sn"];
@$addr_str = $_GET["addr"];

$addr = 11;
if(!empty($addr_str)) $addr = intval($addr_str);

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

$nexturl = "";
$delay="1";
if($addr == 36)
{
	$delay="2";
	$nexturl = "setting.php?sn=".$sn;
}
else
{
	$nexturl = "refresh_setting.php?sn=".$sn."&addr=".($addr+1);
}

//header("Location: /machine.php?sn=".$sn);
?>
<html>
<head>
<meta http-equiv="refresh" content="<?=$delay?>; url=<?=$nexturl?>">   
</head>
<link rel='icon' href='images/logo.ico' type='image/x-ico' />
<link rel="stylesheet" href="css/uikit.css" />
<script src="js/uikit.js"></script>
<script src="js/uikit-icons.js"></script>
<body>
  <section style="text-align: center; padding: 5rem;">
  <div uk-spinner></div>
  <div>Refresh <?=$addr?>/36.</div>
  </section>
</body>
</html>
