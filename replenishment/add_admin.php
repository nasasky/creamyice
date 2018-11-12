<?php
include 'db.php';
if($_POST){
	//connect db
	$account =$_POST['account'];
	$password =$_POST['password'];
	$sn =$_POST['sn'];
    $newPassword =md5(md5($password));
    $openid =createNonceStr(32);
    $datetime =date('Y-m-d H:i:s');
	$sql ="INSERT INTO weixinusr(openid,timestamp,lastlogin,remark,account,password) VALUES('{$openid}','{$datetime}','{$datetime}',0,'{$account}','{$newPassword}')";
	$result =$mysqli->query($sql);
	if($result === false){
		exit("<script>alert('".$mysqli->error."');location.reload;</script>");
	}
	$lastId =$mysqli->insert_id;
	$sql ="INSERT INTO weixin(wid,sn,enable,timestamp) VALUES({$lastId},{$sn},1,'{$datetime}')";
	$result =$mysqli->query($sql);
	if($result === false){
		exit("<script>alert('".$mysqli->error."');location.reload;</script>");
	}
	exit("<script>alert('Successful account:".$account."');location.href='./store.php';</script>");
}

$totalAccount =0;
$sql ="SELECT count(id) total FROM weixinusr";
$result =$mysqli->query($sql);
if($result === false){
	$totalAccount = 'ServerError';
}
if($row = $result->fetch_assoc()){
	$totalAccount =$row['total'];
}



function createNonceStr($length){
	$str ="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_=,|[]();:?/";
	$strLen =strlen($str)-1;
	$nonceStr ='';
	for($i=1;$i<$length;$i++){
		$nonceStr .=$str[mt_rand(0,$strLen)];
	}
	return $nonceStr;
}







?>
</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Mosaddek">
    <meta name="keyword" content="FlatLab, Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
    <link rel="shortcut icon" href="img/favicon.html">
    <title>克瑞米艾商户版</title>
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-reset.css" rel="stylesheet">
    <!--external css-->
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="assets/bootstrap-datepicker/css/datepicker.css" />
    <link rel="stylesheet" type="text/css" href="assets/bootstrap-colorpicker/css/colorpicker.css" />
    <link rel="stylesheet" type="text/css" href="assets/bootstrap-daterangepicker/daterangepicker.css" />
    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet" />
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
      <script src="js/respond.min.js"></script>
    <![endif]-->
</head>

<body>
    <section id="container" class="">
        <!--header start-->
        <header class="header white-bg">
            <div class="sidebar-toggle-box">
                <div data-original-title="Toggle Navigation" data-placement="right" class="icon-reorder tooltips"></div>
            </div>
            <!--logo start-->
            <a href="#" class="logo"><span>克瑞米艾</span></a>
            <!--logo end-->
            <div class="nav notify-row" id="top_menu">
                <!--  notification start -->
                <!--  notification end -->
            </div>
            <div class="top-nav ">
                <!--search & user info start-->
                <ul class="nav pull-right top-menu">
                    <li>
                        <input type="text" class="form-control search" placeholder="Search">
                    </li>
                    <!-- user login dropdown start-->
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <img alt="" src="/favicon.ico">
                            <span class="username"><?php echo $_SESSION['nickname']; ?></span>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu extended logout">
                            <div class="log-arrow-up"></div>
                            <li><a href="../logout.php"><i class="icon-key"></i>退出登录</a></li>
                        </ul>
                    </li>
                    <!-- user login dropdown end -->
                </ul>
                <!--search & user info end-->
            </div>
        </header>
        <!--header end-->
        <!--sidebar start-->
        <aside>
            <div id="sidebar" class="nav-collapse ">
                <!-- sidebar menu start-->
                <ul class="sidebar-menu">
                  <li class="active">
                        <a class="" href="../../index.php">
                                <img src='/favicon.ico' />
                                <span>
                                    Creamyice首页
                                </span>
                            </a>
                    </li>
                   <li class="active">
                        <a class="" href="index.php">
                                <i class="icon-shopping-cart">
                                </i>
                                <span>
                                    补货订单
                                </span>
                            </a>
                    </li>
                    <li class="active">
                        <a class="" href="./sales/list.php">
                                <i class="icon-cogs">
                                </i>
                                <span>
                                    SalesOrders
                                </span>
                            </a>
                    </li>
                     <li class='active'>
                      <a class="" href="./store.php">
                          <i class="icon-user"></i>
                          <span>Store List</span>
                      </a>
                    </li>
                     <li class='active'>
                      <a class="" href="javascript:void();">
                          <i class="icon-user"></i>
                          <span>AddAdmin</span>
                      </a>
                    </li>
                    <!--
                  <li class="sub-menu">
                      <a href="javascript:;" class="">
                          <i class="icon-cogs"></i>
                          <span>Components</span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">
                          <li><a class="" href="grids.html">Grids</a></li>
                          <li><a class="" href="calendar.html">Calendar</a></li>
                          <li><a class="" href="charts.html">Charts</a></li>
                      </ul>
                  </li>
                  <li class="sub-menu">
                      <a href="javascript:;" class="">
                          <i class="icon-tasks"></i>
                          <span>Form Stuff</span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">
                          <li><a class="" href="form_component.html">Form Components</a></li>
                          <li><a class="" href="form_wizard.html">Form Wizard</a></li>
                          <li><a class="" href="form_validation.html">Form Validation</a></li>
                      </ul>
                  </li>
                  <li class="sub-menu">
                      <a href="javascript:;" class="">
                          <i class="icon-th"></i>
                          <span>Data Tables</span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">
                          <li><a class="" href="basic_table.html">Basic Table</a></li>
                          <li><a class="" href="dynamic_table.html">Dynamic Table</a></li>
                      </ul>
                  </li>
                  <li>
                      <a class="" href="inbox.html">
                          <i class="icon-envelope"></i>
                          <span>Mail </span>
                          <span class="label label-danger pull-right mail-info">2</span>
                      </a>
                  </li>
                  <li class="sub-menu">
                      <a href="javascript:;" class="">
                          <i class="icon-glass"></i>
                          <span>Extra</span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">
                          <li><a class="" href="blank.html">Blank Page</a></li>
                          <li><a class="" href="profile.html">Profile</a></li>
                          <li><a class="" href="invoice.html">Invoice</a></li>
                          <li><a class="" href="404.html">404 Error</a></li>
                          <li><a class="" href="500.html">500 Error</a></li>
                      </ul>
                  </li>
                  <li>
                      <a class="" href="login.html">
                          <i class="icon-user"></i>
                          <span>Login Page</span>
                      </a>
                  </li>
                  -->
                </ul>
                <!-- sidebar menu end-->
            </div>
        </aside>
        <!--sidebar end-->
        <!--main content start-->
        <section id="main-content">
            <section class="wrapper">
                <div class=" state-overview">
                    <div class="col-lg-6 col-sm-6">
                        <section class="panel" style="margin-bottom: 0;">
                            <div class="symbol terques">
                                <i class="icon-bullhorn"></i>
                            </div>
                            <div class="value">
                                <h1 style=" font-size: 26px; padding-top: 10px; color: #333;"><?php echo $totalAccount; ?></h1>
                                <p>账号总数</p>
                            </div>
                        </section>
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <section class="panel" style="margin-bottom: 0;">
                            <div class="symbol red">
                                <i class="icon-home"></i>
                            </div>
                            <div class="value">
                                <h1 style=" font-size: 26px; padding-top: 10px; color: #333;">添加账号</h1>
                                <p>HELLO</p>
                            </div>
                        </section>
                    </div>
                </div>
                <section class="wrapper" style="margin-top: 20px; padding: 0px 15px  15px  15px;">
                    <div class="row">
                        <div class="col-lg-12">
                            <!--breadcrumbs start -->
                            <ul class="breadcrumb" style="margin-bottom: 0;">
                             
                                <li class="active">添加商户账号</li>
                            </ul>
                            <!--breadcrumbs end -->
                        </div>
                    </div>
                </section>
                <form class="form-horizontal tasi-form" method="post" action='<?php htmlspecialchars($_SERVER['PHP_SELF']); ?>' onsubmit='return check_form();'>
                    <div>
                        <div class="col-lg-12">
                            <section class="panel">
                                <header class="panel-heading">
                                    确认订单信息无误后，再做修改！
                                </header>
                                <div class="panel-body">

                         

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">登陆账号</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name='account'>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">登陆密码</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name='password'>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">授权机器账号</label>
                                        <div class="col-sm-10">
                                            <input type="number" class="form-control" name='sn'>
                                        </div>
                                    </div>
                                  
                                </div>
                            </section>
                        </div>
                    </div>
                    </div>
                    <div class="col-lg-offset-2 col-lg-10">
                        <input type='submit' class="btn btn-danger" value='添加账号'>
                    </div>
                </form>
            </section>
        </section>
        <!-- js placed at the end of the document so the pages load faster -->
        <script src="js/jquery.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/jquery.scrollTo.min.js"></script>
        <script src="js/jquery.nicescroll.js" type="text/javascript"></script>
        <script src="js/jquery-ui-1.9.2.custom.min.js"></script>
       
        <script type="text/javascript" charset="utf-8" async defer>
           function check_form(){
           	 if($('input[name="account"]').val() == ''){
           	 	alert('请输入账号');
           	 	return false;
           	 }
           	 if($('input[name="password"]').val() == ''){
           	 	alert('请输入密码');
           	 	return false;
           	 }
           	 if($('input[name="sn"]').val() == '' || $('input[name="sn"]').val().length != 10){
           	 	alert('授权机器账号错误');
           	 	return false;
           	 }
             if(confirm('你确定要授权该账号吗？')){
                return;
             }else{
                return false;
             }
           }
        </script>
</body>

</html>