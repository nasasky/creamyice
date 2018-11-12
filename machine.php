<?php
include './db.php';

////////////////Define
$strWarnings=array(0=>"无报警",1=>"无转速信号报警",2=>"皮带打滑报警",
				   3=>"高压开关报警(HH)",4=>"料缸温度传感器开路(AL)",5=>"料缸温度传感器短路(AH)",
				   6=>"料缸温度传感器开路(bL)",7=>"料缸温度传感器短路(bH)",
				   20=>"缺料",21=>"推杆电机疑似故障",22=>"热循环升温阶失败",
				   23=>"热循环降温阶失败",24=>"冻缸自动保护",25=>"无料",
				   255=>""
);
$strStatus=array(
	0=>"待机",1=>"制冷",2=>"清洗",
	3=>"保鲜",4=>"解冻",5=>"巴士杀菌",
	6=>"再生",7=>"重操作",8=>"自动出料",
	9=>"离线"
);
$myProp = array(0=>0, 1=>"",2=>0,
				10=>"",11=>"",12=>"",13=>"",14=>"",15=>"",16=>"",17=>"",18=>"",19=>"",
				20=>"",21=>"",22=>"",23=>"",24=>"",25=>"",26=>"",27=>"",28=>"",29=>"",
				30=>"",31=>"",32=>"",33=>"",34=>"",35=>"",36=>"",
				37=>255,38=>"",39=>"",40=>"",41=>"",42=>"",43=>"");
$myTime = array(0=>"", 1=>"",2=>"",
				10=>"",11=>"",12=>"",13=>"",14=>"",15=>"",16=>"",17=>"",18=>"",19=>"",
				20=>"",21=>"",22=>"",23=>"",24=>"",25=>"",26=>"",27=>"",28=>"",29=>"",
				30=>"",31=>"",32=>"",33=>"",34=>"",35=>"",36=>"",
				37=>"",38=>"",39=>"",40=>"",41=>"",42=>"",43=>"");
////////////

$sn = $_GET["sn"];

if(!empty($sn))
{
	$sql = "SELECT id,sn,cmd,addr, param0, param1, param2,timestamp as lt,
			CURRENT_TIMESTAMP() as ct FROM response WHERE sn='".$sn."' AND cmd=71";

	$result = $mysqli->query($sql);
	if($result === false){
		echo $mysqli->error;
		echo $mysqli->errno;
	}
	while($row = $result->fetch_assoc()){
		$val = 0;
		if($row['addr']==39 || $row['addr']==42 || $row['addr']==43) $val = $row['param2']*65536 + $row['param0']*256 + $row['param1'];
		else $val = $row['param0']*256 + $row['param1'];
		$myProp[$row['addr']] = $val;
		$myTime[$row['addr']] = $row['lt'];
	}

	if($myProp[40]>32767)$myProp[40] = $myProp[40]-65536;
	if($myProp[41]>32767)$myProp[41] = $myProp[41]-65536;
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
<title>系统状态-克瑞米艾</title>
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
span {margin-right:20px;}
span.red {color:red}
.button{ float:left;width:300px;border:1px solid #00F;height:60px;background-color:#cccccc;margin:6px;padding-left:15px;}
.time {font: normal 9px #cccccc}
</style>
<body>
  <div uk-sticky="media: 960" class="uk-navbar-container tm-navbar-container uk-sticky uk-sticky-fixed">
                      <div class="uk-container uk-container-expand">
                        <nav class="uk-navbar">
                          <div class="uk-navbar-left">
                            <a href="index.php" class="uk-navbar-item uk-logo" style="color: #ed5c38;">
                              <img width="50" src="images/ice_logo.png" />克瑞米艾
                              <span class="uk-label uk-label-warning" style="margin-left: 0.5em; margin-top: 0.5em;">体验版</span></a>
                          </div>
                          <div class="uk-navbar-right">
                            <ul class="uk-navbar-nav uk-visible@m">
                              <li>
                                <a href="index.php">
  <span uk-icon="icon: home" class="uk-margin-small-right uk-icon"></span><span class="uk-text-middle">商户首页</span></a></li>
                              <a  href="machine.php?sn=<?=$sn?>">
								  <li>
  <span uk-icon="icon: cog" class="uk-margin-small-right uk-icon"></span><span class="uk-text-middle">状态</span></a></li>
							  <li>
                                <a href="setting.php?sn=<?=$sn?>">
  <span uk-icon="icon: pencil" class="uk-margin-small-right uk-icon"></span><span class="uk-text-middle">配置</span></a></li>
                            </ul>
                            <div class="uk-navbar-item uk-visible@m"><a href="refresh.php?sn=<?=$sn?>" class="uk-button uk-button-default tm-button-default uk-icon"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">刷新系统 </font></font><canvas uk-icon="icon: refresh" width="20" height="20" class="uk-icon" hidden="true"></canvas></a></div>
                            <a uk-navbar-toggle-icon="" href="#offcanvas" uk-toggle="target: #offcanvas-push" class="uk-navbar-toggle uk-hidden@m uk-navbar-toggle-icon uk-icon">
                            </a>
                          </div>
                        </nav>
                      </div>
                    </div>
                    <section class="uk-container" style="margin-top: 1rem; margin-bottom: 1rem;">
                    <ul class="uk-breadcrumb">
                          <li><a href="index.php">商户首页</a></li>
                          <li><span href="#">系统状态</span></li>
                    </ul>
                    </section>
                    <section class="uk-container" style=" margin-top:1rem;  margin-bottom:5rem;">
                      <h1 class="uk-heading-primary" style="font-size: 2rem; padding: 1rem 2rem 2rem 2rem; color: #999; text-align: -webkit-center;">系统状态</h1>
                      <div class="uk-child-width-1-4@m uk-grid-small uk-grid-match" uk-grid>

  <div>
      <div class="uk-card uk-card-default uk-card-body">
          <h5 style="color:#999;">运行状态</h5>
          <h3><?=$strStatus[$myProp[0]]?>中</h3>
          <p style="color:#999; font-size: 1rem; padding-top:0.2rem;"><?=$myTime[0]?></p>
      </div>
  </div>

  <div>
  <div class="uk-card uk-card-default uk-card-body">
      <h5 style="color:#999;">成型比</h5>
      <h3><?=$myProp[1]?></h3>
      <p style="color:#999; font-size: 1rem; padding-top:0.2rem;"><?=$myTime[1]?></p>
  </div>
</div>
<div>
<div class="uk-card uk-card-default uk-card-body">
    <h5 style="color:#999;">报警故障</h5>
    <h3><?=$strWarnings[$myProp[37]]?></h3>
    <p style="color:#999; font-size: 1rem; padding-top:0.2rem;"><?=$myTime[37]?></p>
</div>
</div>
<div>
<div class="uk-card uk-card-default uk-card-body">
    <h5 style="color:#999;">料缸温度</h5>
    <h3><?=$myProp[41]?></h3>
    <p style="color:#999; font-size: 1rem; padding-top:0.2rem;"><?=$myTime[41]?></p>
</div>
</div>
</div>
<div class="uk-child-width-1-3@m uk-grid-small uk-grid-match" uk-grid>
<div>
<div class="uk-card uk-card-default uk-card-body">
<h5 style="color:#999;">出料杯数39</h5>
<h3><?=$myProp[39]?></h3>
<p style="color:#999; font-size: 1rem; padding-top:0.2rem;"><?=$myTime[39]?></p>
</div>
<div class="uk-card uk-card-default uk-card-body">
<h5 style="color:#999;">出料杯数42</h5>
<h3><?=$myProp[42]?></h3>
<p style="color:#999; font-size: 1rem; padding-top:0.2rem;"><?=$myTime[42]?></p>
</div>
<div class="uk-card uk-card-default uk-card-body">
<h5 style="color:#999;">出料杯数43</h5>
<h3><?=$myProp[43]?></h3>
<p style="color:#999; font-size: 1rem; padding-top:0.2rem;"><?=$myTime[43]?></p>
</div>
</div>
<div>
<div class="uk-card uk-card-default uk-card-body">
<h5 style="color:#999;">料槽温度</h5>
<h3><?=$myProp[40]?></h3>
<p style="color:#999; font-size: 1rem; padding-top:0.2rem;"><?=$myTime[40]?></p>
</div>
</div>
<div>
<div class="wh50 uk-card uk-card-default uk-card-body">
<h5 style="color:#999;">系统清洗倒计时</h5>
<h3>还剩:<code style="padding:0;  background: #FFF; font-size: 1.5rem;"><?=$myProp[38]?></code>天</h3>
<p style="color:#999; font-size: 1rem; padding-top:0.2rem;"><?=$myTime[38]?></p>
</div>
</div>
</div>
<div style="padding-top:1rem;">
<h1 class="uk-heading-primary" style="font-size: 2rem; padding: 1rem 2rem 2rem 2rem; color: #999; text-align: -webkit-center;">操控系统</h1>
<dl class="uk-child-width-1-3@m uk-child-width-1-3@m uk-grid-small uk-grid-match uk-grid">
<dd><a href="run.php?sn=<?=$sn?>&cmd=1" style="line-height: 6rem; font-size: 2rem;"  class="wd322 uk-button uk-button-primary uk-width-1-1 uk-margin-small-bottom"><span class="uk-icon uk-icon-image" style="background-image: url(../images/refrigeration.png);"></span>制冷</a></dd>
<dd><a href="run.php?sn=<?=$sn?>&cmd=3" style="line-height: 6rem; font-size: 2rem;"  class="wd322 uk-button uk-button-primary uk-width-1-1 uk-margin-small-bottom"><span class="uk-icon uk-icon-image" style="background-image: url(../images/fresh.png);"></span>保鲜</a></dd>
<dd><a href="run.php?sn=<?=$sn?>&cmd=2" style="line-height: 6rem; font-size: 2rem;"   class="wd322 uk-button uk-button-primary uk-width-1-1 uk-margin-small-bottom"><span class="uk-icon uk-icon-image" style="background-image: url(../images/clean.png);"></span>清洗</a></dd>
<dd><a href="run.php?sn=<?=$sn?>&cmd=0" style="line-height: 6rem; font-size: 2rem;" class="uk-button uk-button-secondary uk-width-1-1 uk-margin-small-bottom wd322"><span class="uk-icon uk-icon-image" style="background-image: url(../images/standby.png);"></span>待机</a></dd>
<dd><a href="run.php?sn=<?=$sn?>&cmd=4" style="line-height: 6rem; font-size: 2rem;"   class="wd322 uk-button uk-button-secondary uk-width-1-1 uk-margin-small-bottom"><span class="uk-icon uk-icon-image" style="background-image: url(../images/thaw.png);"></span>解冻</a></dd>
<dd><a href="run.php?sn=<?=$sn?>&cmd=5" style="line-height: 6rem; font-size: 2rem;"  class="wd322 uk-button uk-button-secondary uk-width-1-1 uk-margin-small-bottom"><span class="uk-icon uk-icon-image" style="background-image: url(../images/sterilization.png);"></span>巴士杀菌</a></dd>
<dd><a href="run.php?sn=<?=$sn?>&cmd=6 " style="line-height: 6rem; font-size: 2rem;"   class="wd322 uk-button uk-button-secondary uk-width-1-1 uk-margin-small-bottom"><span class="uk-icon uk-icon-image" style="background-image: url(../images/regeneration.png);"></span>再生</a></dd>
<dd><a href="run.php?sn=<?=$sn?>&cmd=7" style="line-height: 6rem; font-size: 2rem;"   class="wd322 uk-button uk-button-secondary uk-width-1-1 uk-margin-small-bottom"><span class="uk-icon uk-icon-image" style="background-image: url(../images/operation.png);"></span>重操作</a></dd>
<dd><a href="run.php?sn=<?=$sn?>&cmd=8" style="line-height: 6rem; font-size: 2rem;"  class="wd322 uk-button uk-button-secondary uk-width-1-1 uk-margin-small-bottom"><span class="uk-icon uk-icon-image" style="background-image: url(../images/discharge.png);"></span>自动出料</a></dd>

</dl>
</div>
</section>

</body>
</html>
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
      <li>
        <a href="machine.php?sn=<?=$sn?>">
          <font style="vertical-align: inherit;">
            <font style="vertical-align: inherit;">状态</font></font>
        </a>
      </li>
      <li>
        <a href="setting.php?sn=<?=$sn?>">
          <font style="vertical-align: inherit;">
            <font style="vertical-align: inherit;">配置</font></font>
        </a>
      </li>
      <li>
        <a href="refresh.php?sn=<?=$sn?>">
          <font style="vertical-align: inherit;">
            <font style="vertical-align: inherit;">刷新系统</font></font>
        </a>
      </li>
    </ul>
  </div>
</div>
<div class="actGotop"><a href="javascript:;" title="返回顶部"></a></div>
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
<?php

$mysqli->close();
?>
