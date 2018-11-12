<?php
include './db.php';

$sql = "SELECT machine.sn,ip,name,barcode,machine.timestamp as lt, CURRENT_TIMESTAMP() as ct
        FROM machine
        ORDER BY machine.sn ASC";
$result = $mysqli->query($sql);
if($result === false){//执行失败
    echo $mysqli->error;
    echo $mysqli->errno;
}
?>
<!--[if IE 6]>
<html id="ie6" lang="en-US">
<![endif]-->
<!--[if IE 7]>
<html id="ie7" lang="en-US">
<![endif]-->
<!--[if IE 8]>
<html id="ie8" lang="en-US">
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<title>商户系统列表-克瑞米艾</title>
<link rel='icon' href='images/logo.ico' type='image/x-ico' />
<link rel="stylesheet" href="css/uikit.css" />
<script src="js/uikit.js"></script>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script src="js/uikit-icons.js"></script>
<!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
  <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
<head>
<style type="text/css">
span {margin-right:10px;}
span.red {color:red}
</style>
<body>
<!--<div>Creamy Ice Control Center <?=date("Y-m-d")?></div><div> Device List</div>-->
<div uk-sticky="media: 960" class="uk-navbar-container tm-navbar-container uk-sticky uk-sticky-fixed">
                    <div class="uk-container uk-container-expand">
                      <nav class="uk-navbar">
                        <div class="uk-navbar-left">
                          <a href="index.php" class="uk-navbar-item uk-logo" style="color: #ed5c38;">
                            <img width="50" src="images/ice_logo.png" />克瑞米艾
                            <span class="uk-label uk-label-warning" style="margin-left: 0.5em; margin-top: 0.5em;">体验版</span></a>
                        </div>
                        <div class="uk-navbar-right">
                          <a uk-navbar-toggle-icon="" href="#offcanvas" uk-toggle="target: #offcanvas-push" class="uk-navbar-toggle uk-hidden@m uk-navbar-toggle-icon uk-icon">
                          </a>
                        </div>
                      </nav>
                    </div>
                  </div>
<section class="uk-container" style="padding-bottom: 5em;">
<h2 class="ice-h2 ice-h2-1">商户系统列表</h2>
 <?php
while($row = $result->fetch_assoc()){
	$t1 = strtotime($row['ct']);
	$t2 = strtotime($row['lt']);
	$status = '';
	if($t1-$t2>125)
	{
?>

<section class="uk-background-default ice-ti2">
<div class="ice-ti3">
	<article class="uk-article ice-ti4">
    <h2 class="ice-ti5"><?=$row['name']?><span class="uk-label ice-ti6">离线</span></h2>
    <p class="ice-ti7">编号：<?=$row['sn']?><span class="ice-ti8"><?=$row['lt']?></span></p>
</div>
<div class="ice-ti9">
	<button class="uk-button uk-button-default uk-button-small ice-ti12" disabled>离线</button>
    <button onclick="window.location='log.php?sn=<?=$row['sn']?>'" class="uk-button uk-button-default uk-button-small ice-ti10">查看更新日志</button>
</div>
</article>
</section>
<?php
	}
	else
	{
?>
	<section class="uk-background-default ice-ti2">
<div class="ice-ti3">
	<article class="uk-article ice-ti4">
    <h2 class="ice-ti5"><?=$row['name']?><span class="ice-ti640-1"><span class="uk-label uk-label-danger ice-ti6" >已授权</span><span class="uk-label uk-label-success ice-ti6b">正常</span></span></h2>
    <p class="ice-ti7">编号：<?=$row['sn']?> <span class="ice-ti8"><?=$row['lt']?></span></p>
	<p class="ice-ti7">无限模式：<?=$row['sn']?> <span class="ice-ti8"><?=$row['lt']?></span> 计数模式：<?=$row['sn']?> <span class="ice-ti8"><?=$row['lt']?></span></p>
	<p class="ice-ti7">计数测试：<?=$row['sn']?> <span class="ice-ti8">1/2/3</span></p>
</div>
<div class="ice-ti9">
	<button onclick="window.location='machine.php?sn=<?=$row['sn']?>'" class="uk-button uk-button-primary uk-button-small ice-ti11">进入商户管理</button>
    <button onclick="window.location='log.php?sn=<?=$row['sn']?>'" class="uk-button uk-button-default uk-button-small ice-ti10">查看更新日志</button>
	<button onclick="window.location='setserverip.php?sn=<?=$row['sn']?>'" class="uk-button uk-button-default uk-button-small ice-ti10">更新模块服务器地址</button>
</div>
</article>
</section>

<?php
	}
}
?>
</section>
<!--
<ul>

<li><?=$row['sn']?> <?=$row['name']?><span> Offline</span>  <span><?=$row['lt']?></span> <span><a href="log.php?sn=<?=$row['sn']?>">Log</a></span></li>
<li><a href= "machine.php?sn=<?=$row['sn']?>"><?=$row['sn']?></a> <?=$row['name']?> <span class="red">Active</span> <span><a href="log.php?sn=<?=$row['sn']?>">Log</a></span></li>
-->

</section>
<div id="offcanvas-push" uk-offcanvas="mode: push; overlay: true">
  <div class="uk-offcanvas-bar">
    <button class="uk-offcanvas-close" type="button" uk-close></button>
    <ul class="uk-nav uk-nav-default tm-nav">
      <li class="uk-nav-header">
<a href="index.php">
        <font style="vertical-align: inherit;">
          <font style="vertical-align: inherit;">商户首页</font></font>
</a>
      </li>
    </ul>
  </div>
</div>
<div class="actGotop"><a href="javascript:;" title="返回顶部"></a></div>
</body>
<script type="text/javascript">
$(function(){
	$(window).scroll(function() {
		if($(window).scrollTop() >= 100){
			$('.actGotop').fadeIn(300);
		}else{
			$('.actGotop').fadeOut(300);
		}
	});
	$('.actGotop').click(function(){
	$('html,body').animate({scrollTop: '0px'}, 800);});
});
</script>

</html>

<?php

$mysqli->close();
?>
