<?php
include './db.php';
////////////////Define
$myProp = array(0=>0, 1=>"",2=>0,
				10=>"",11=>"",12=>"",13=>"",14=>"",15=>"",16=>"",17=>"",18=>"",19=>"",
				20=>"",21=>"",22=>"",23=>"",24=>"",25=>"",26=>"",27=>"",28=>"",29=>"",
				30=>"",31=>"",32=>"",33=>"",34=>"",35=>"",36=>"",
				37=>255,38=>"",39=>"",40=>"",41=>"",42=>"",43=>"",44=>"",45=>"0",46=>"0");
$myTime = array(0=>"", 1=>"",2=>"",
				10=>"",11=>"",12=>"",13=>"",14=>"",15=>"",16=>"",17=>"",18=>"",19=>"",
				20=>"",21=>"",22=>"",23=>"",24=>"",25=>"",26=>"",27=>"",28=>"",29=>"",
				30=>"",31=>"",32=>"",33=>"",34=>"",35=>"",36=>"",
				37=>"",38=>"",39=>"",40=>"",41=>"",42=>"",43=>"",44=>"",45=>"",46=>"");
$limit39 = 0;
$limit42 = 0;
$limit43 = 0;
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
    /***/
		if($row['addr']==46)
		{
			$limit39 = $row['param1'];
			$limit42 = $row['param2'];
			$limit43 = $row['param0'];
		}
			
		$val = $row['param0']*256 + $row['param1'];
		$myProp[$row['addr']] = $val;
		$myTime[$row['addr']] = $row['lt'];
	}
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
<title>系统配置-克瑞米艾</title>
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
                            <div class="uk-navbar-item uk-visible@m"><a href="refresh_setting.php?sn=<?=$sn?>" class="uk-button uk-button-default tm-button-default uk-icon"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">刷新配置 </font></font><canvas uk-icon="icon: refresh" width="20" height="20" class="uk-icon" hidden="true"></canvas></a></div>
                            <a uk-navbar-toggle-icon="" href="#offcanvas" uk-toggle="target: #offcanvas-push" class="uk-navbar-toggle uk-hidden@m uk-navbar-toggle-icon uk-icon">
                            </a>
                          </div>
                        </nav>
                      </div>
                    </div>
                    <section class="uk-container" style="margin-top: 1rem; margin-bottom: 1rem;">
                    <ul class="uk-breadcrumb">
                          <li><a href="index.php">商户首页</a></li>
                          <li><span href="#">系统配置</span></li>
                    </ul>
                    </section>
                    <section class="uk-container" style="padding-bottom:5rem;">
                      <div class="uk-child-width-1-3@s uk-grid-match" uk-grid>
                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">11:需要制作硬度</div>
                  <div class="ice-s3-1">
                          <input id="input11" type="text" value="<?=$myProp[11]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=11&val='+input11.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=11'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[11]?></div>
                  </div>
                  </div>

                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">12:机器周期重启时间设定</div>
                  <div class="ice-s3-1">
                          <input id="input12" type="text" value="<?=$myProp[12]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=12&val='+input12.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=12'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[12]?></div>
                  </div>
                  </div>

                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">13:搅拌电机延时停机时间设定</div>
                  <div class="ice-s3-1">
                          <input id="input13" type="text" value="<?=$myProp[13]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=13&val='+input13.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=13'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[13]?></div>
                  </div>
                  </div>
                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">14:压机延时启动时间设定</div>
                  <div class="ice-s3-1">
                          <input id="input14" type="text" value="<?=$myProp[14]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=14&val='+input14.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=14'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[14]?></div>
                  </div>
                  </div>
                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">15:抽料泵延时停机时间设定</div>
                  <div class="ice-s3-1">
                          <input id="input15" type="text" value="<?=$myProp[15]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=15&val='+input15.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=15'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[15]?></div>
                  </div>
                  </div>
                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">16:料槽预冷温度设定</div>
                  <div class="ice-s3-1">
                          <input id="input16" type="text" value="<?=$myProp[16]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=16&val='+input16.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=16'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[16]?></div>
                  </div>
                  </div>
                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">17:冷冻缸预冷温度设定</div>
                  <div class="ice-s3-1">
                          <input id="input17" type="text" value="<?=$myProp[17]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=17&val='+input17.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=17'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[17]?></div>
                  </div>
                  </div>
                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">18:清洗间隔天数</div>
                  <div class="ice-s3-1">
                          <input id="input18" type="text" value="<?=$myProp[18]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=18&val='+input18.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=18'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[18]?></div>
                  </div>
                  </div>
                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">19:缺料还允许出料杯数</div>
                  <div class="ice-s3-1">
                          <input id="input19" type="text" value="<?=$myProp[19]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=19&val='+input19.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=19'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[19]?></div>
                  </div>
                  </div>
                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">29:出料（小杯）时间设定</div>
                  <div class="ice-s3-1">
                          <input id="input29" type="text" value="<?=$myProp[29]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=29&val='+input29.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=29'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[29]?></div>
                  </div>
                  </div>
                  <div>
                    <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                    <div class="ice-s1">
                    <div class="ice-s2">20:出料（中杯）时间设定</div>
                    <div class="ice-s3-1">
                            <input id="input20" type="text" value="<?=$myProp[20]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                        </div>
                    </div>
                    <div>
                    <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=20&val='+input20.value">修改</button>
                    <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=20'">读取</button>
                    </div>
                    <div class="ice-s4">上次修改时间：<?=$myTime[20]?></div>
                    </div>
                    </div>
                    <div>
                      <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                      <div class="ice-s1">
                      <div class="ice-s2">30:出料（大杯）时间设定</div>
                      <div class="ice-s3-1">
                              <input id="input30" type="text" value="<?=$myProp[30]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                          </div>
                      </div>
                      <div>
                      <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=30&val='+input30.value">修改</button>
                      <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=30'">读取</button>
                      </div>
                      <div class="ice-s4">上次修改时间：<?=$myTime[30]?></div>
                      </div>
                      </div>
                      <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">21:允许出料成型比例</div>
                  <div class="ice-s3-1">
                          <input id="input21" type="text" value="<?=$myProp[21]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=21&val='+input21.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=21'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[21]?></div>
                  </div>
                  </div>
                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">22:加热温度设定值</div>
                  <div class="ice-s3-1">
                          <input id="input22" type="text" value="<?=$myProp[22]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=22&val='+input22.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=22'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[22]?></div>
                  </div>
                  </div>
                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">23:解冻温度</div>
                  <div class="ice-s3-1">
                          <input id="input23" type="text" value="<?=$myProp[23]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=23&val='+input23.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=23'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[23]?></div>
                  </div>
                  </div>
                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">24:冻缸温度判定</div>
                  <div class="ice-s3-1">
                          <input id="input24" type="text" value="<?=$myProp[24]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=24&val='+input24.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=24'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[24]?></div>
                  </div>
                  </div>
                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">25:料槽预冷间断开时间</div>
                  <div class="ice-s3-1">
                          <input id="input25" type="text" value="<?=$myProp[25]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=25&val='+input25.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=25'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[25]?></div>
                  </div>
                  </div>
                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">26:料槽预冷间断关时间</div>
                  <div class="ice-s3-1">
                          <input id="input26" type="text" value="<?=$myProp[26]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=26&val='+input26.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=26'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[26]?></div>
                  </div>
                  </div>
                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">27:料槽搅拌制冷间断开时间</div>
                  <div class="ice-s3-1">
                          <input id="input27" type="text" value="<?=$myProp[27]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=27&val='+input27.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=27'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[27]?></div>
                  </div>
                  </div>
                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">28:料槽搅拌制冷间断关时间</div>
                  <div class="ice-s3-1">
                          <input id="input28" type="text" value="<?=$myProp[28]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=28&val='+input28.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=28'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[28]?></div>
                  </div>
                  </div>
                  <div>
                    <!--
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">29:料槽搅拌制热间断开时间</div>
                  <div class="ice-s3-1">
                          <input id="input29" type="text" value="<?=$myProp[29]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=29&val='+input29.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=29'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[29]?></div>
                  </div>
                  </div>
                  <div>
                  -->
                  <!--
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">30:料槽搅拌制热间断关时间</div>
                  <div class="ice-s3-1">
                          <input id="input30" type="text" value="<?=$myProp[30]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=30&val='+input30.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=30'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[30]?></div>
                  </div>
                  </div>
                  <div>
                  -->
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">31:热循环进入时间设定（分钟）</div>
                  <div class="ice-s3-1">
                          <input id="input31" type="text" value="<?=$myProp[31]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=31&val='+input31.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=31'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[31]?></div>
                  </div>
                  </div>
                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">32:热循环进入时间设定（小时）</div>
                  <div class="ice-s3-1">
                          <input id="input32" type="text" value="<?=$myProp[32]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=32&val='+input32.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=32'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[32]?></div>
                  </div>
                  </div>
                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">33:自动制冷进入时间设定（分钟）</div>
                  <div class="ice-s3-1">
                          <input id="input33" type="text" value="<?=$myProp[33]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=33&val='+input33.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=33'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[33]?></div>
                  </div>
                  </div>
                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">34:自动制冷进入时间设定（小时）</div>
                  <div class="ice-s3-1">
                          <input id="input34" type="text" value="<?=$myProp[34]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=34&val='+input34.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=34'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[34]?></div>
                  </div>
                  </div>
                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">35:设备系统时间设定（分钟）</div>
                  <div class="ice-s3-1">
                          <input id="input35" type="text" value="<?=$myProp[35]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=35&val='+input35.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=35'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[35]?></div>
                  </div>
                  </div>
                  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">36:设备系统时间设定（小时）</div>
                  <div class="ice-s3-1">
                          <input id="input36" type="text" value="<?=$myProp[36]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=36&val='+input36.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=36'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[36]?></div>
                  </div>
                  </div>
				  
				  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">45:切换数量模式</div>
                  <div class="ice-s3-1">
                          <input id="input45" type="text" value="<?=$myProp[45]?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default">
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=45&val='+input45.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=45'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[45]?></div>
                  </div>
                  </div>
				  
				  <div>
                  <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                  <div class="ice-s1">
                  <div class="ice-s2">46:订单数量</div>
                  <div class="ice-s3-1">
                          <div>L39<input id="input_l39" type="text" value="<?=$limit39?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default"></div>
						  <div>L42<input id="input_l42" type="text" value="<?=$limit42?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default"></div>
						  <div>L43<input id="input_l43" type="text" value="<?=$limit43?>" size="4" class="uk-input uk-form-width-medium ice-fs" placeholder="Default"></div>
                      </div>
                  </div>
                  <div>
                  <button class="uk-button uk-button-primary ice-s3" onClick="location.href='set.php?sn=<?=$sn?>&addr=46&val=0&l39='+input_l39.value+'&l42='+input_l42.value+'&l43='+input_l43.value">修改</button>
                  <button class="uk-button uk-button-default" onClick="location.href='read.php?sn=<?=$sn?>&addr=46'">读取</button>
                  </div>
                  <div class="ice-s4">上次修改时间：<?=$myTime[46]?></div>
                  </div>
                  </div>
</div>
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
        <a href="refresh_setting.php?sn=<?=$sn?>">
          <font style="vertical-align: inherit;">
            <font style="vertical-align: inherit;">刷新配置</font></font>
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
