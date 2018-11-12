<?php
include './db.php';

@$sn = $_GET["sn"];
@$addr_str = $_GET["addr"];

$addr = 0;
if(!empty($addr_str)) $addr = intval($addr_str);

$addrs=array("0","1","37","38","39","40","41","42","43");
$addrlen=count($addrs);

if(!empty($sn))
{
	$sql = "INSERT INTO command (sn, cmd, addr)
			VALUES('".$sn."', 71, ".$addrs[$addr].")";
	$result = $mysqli->query($sql);

	if($result === false){
		echo $mysqli->error;
		echo $mysqli->errno;
	}
}
$mysqli->close();

$nexturl = "";
$delay="1";
if($addr == $addrlen-1)
{
	$delay="2";
	$nexturl = "machine.php?sn=".$sn;
}
else
{
	$nexturl = "refresh.php?sn=".$sn."&addr=".($addr+1);
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
<div>Refresh <?=$addr?>/<?=$addrlen?>.</div>
</section>
</body>
</html>
