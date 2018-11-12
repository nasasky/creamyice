<?php
include './db.php';

@$sn = $_GET["sn"];
@$limit = $_GET["limit"];

if(!empty($sn))
{
if(empty($limit)) $limit = 25;

$sql = "SELECT sn,param1,timestamp FROM log WHERE sn='".$sn."' AND cmd=71 AND addr=0 ORDER BY timestamp DESC LIMIT ".$limit;
$result = $mysqli->query($sql);
if($result === false){//执行失败
    echo $mysqli->error;
    echo $mysqli->errno;
}

$strStatus=array(
	0=>"<span>待机</span>",1=>"制冷",2=>"清洗",
	3=>"保鲜",4=>"解冻",5=>"<i>巴士杀菌</i>",
	6=>"再生",7=>"重操作",8=>"自动出料",
	9=>"离线"
);

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
<title>更新日志-克瑞米艾</title>
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
.uk-card-color span{color: #fe3f0c}
.uk-card-color i{font-style:normal;color: #32d296;}
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
                            <a uk-navbar-toggle-icon="" href="#offcanvas" uk-toggle="target: #offcanvas-push" class="uk-navbar-toggle uk-hidden@m uk-navbar-toggle-icon uk-icon">
                            </a>
                          </div>
                        </nav>
                      </div>
                    </div>
  <section class="uk-container" style=" max-width: 960px; margin-top: 1rem; margin-bottom: 1rem;">
  <ul class="uk-breadcrumb">
        <li><a href="index.php">商户首页</a></li>
        <li><span href="#">更新日志</span></li>
  </ul>
  </section>
                    <section class="uk-container" style=" max-width: 960px; margin-top: 1rem; margin-bottom: 1rem;">
                      <h3 style="text-align: center; padding-top: 1rem; padding-bottom: 1rem;">系统型号：<?=$sn?></h3>
                      <div>
                      </div>
                    </section>
	 <div class="uk-container" style="font-size: 1.5rem; max-width: 960px;">显示条数： <a href="log.php?sn=<?=$sn?>&limit=25">25</a> <a href="log.php?sn=<?=$sn?>&limit=50">50</a>  <a href="log.php?sn=<?=$sn?>&limit=100">100</a> <a href="log.php?sn=<?=$sn?>&limit=200">200</a>  <a href="log.php?sn=<?=$sn?>&limit=500">500</a></div>
                    <section class="uk-container" style=" max-width: 960px; margin-bottom:3rem;">
                      <ul class="uk-child-width-1-2@m" style="padding-top: 1rem;" uk-grid>

							  <?php
$i=1;
while($row = $result->fetch_assoc()){
?>
                        <li class="uk-grid-margin">
                          <div class="uk-card-default uk-card-color" style="padding: 1rem; overflow: hidden;">

                            <h4 style="float: left;">
                              <i style="color: #999; font-size: 1rem; padding-right: 0.5rem;"><?=$i++?></i><?=@$strStatus[$row['param1']]?></h4>
                            <p style="color: #CCC; margin: 0; float:right; padding-top: 4px;"><!--<span style="padding-right:0.2rem;"><?=$row['sn']?></span>--><?=$row['timestamp']?></p>
							</div>
                        </li>
								<?php
}
?>

                      </ul>
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
}
$mysqli->close();
?>
