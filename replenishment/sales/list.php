 <?php
include '../db.php';
rebuild_GET();
$total = 0;
//(1) query replenishment orders total
$sql = 'SELECT count(*) total FROM (select count(id) FROM so_diary GROUP BY sn,year,month,day) so';
$result = $mysqli->query($sql);
if($result === false){
  $total = '读取失败';
}else{
  if($row = $result->fetch_assoc()){
    $total =$row['total'];
  }
}

//(2) query storer number
$store_total =0;
$sql ="SELECT count(id) total FROM weixinusr";
$result = $mysqli->query($sql);
if($result === false){
  $store_total = '查询失败';
}else{
  if($row = $result->fetch_assoc()){
    $store_total = $row['total'] - 2;
  }
}

//page 
$curPage =1;
$size = 10;
$start =0;
$totalPage =ceil($total / $size);
if(isset($_GET['page'])){
  $curPage =$_GET['page'];
  $start =($curPage - 1 > 0)?($curPage - 1) * $size:0;
}

//(3)query replenishment orders lists
$lists =[];
$month =date('n');
$sql = "SELECT d.sn,p.store 'name',concat(d.year,'-',d.month,'-',d.day) date,SUM(d.count) total,GROUP_CONCAT(d.pro_id,'=',d.count) detail FROM place p,so_diary d 
WHERE p.sn=d.sn GROUP BY d.sn,d.year,d.month,d.day ORDER BY d.sn,d.day LIMIT $start,$size";
$result = $mysqli->query($sql);
if($result === false){
  $lists = '服务器正忙';
}else{
  while($row = $result->fetch_assoc()){
    $arr =explode(',',$row['detail']);
    foreach ($arr as $key => $value) {
       $row[substr($value,0,2)] = substr($value,strpos($value,'=')+1);
    }
    $row['date'] =date('Y-m-d',strtotime($row['date']));
    $lists[] =$row;
  }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="" name="description" />
    <meta content="Mosaddek" name="author" />
    <meta content="FlatLab, Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina" name="keyword" />
    <link href="img/favicon.html" rel="shortcut icon" />
    <title>
        克瑞米艾商户版
    </title>
    <!-- Bootstrap core CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet" />
    <link href="../css/bootstrap-reset.css" rel="stylesheet" />
    <!--external css-->
    <link href="../assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="../css/style.css" rel="stylesheet" />
    <link href="../css/style-responsive.css" rel="stylesheet" />
</head>

<body>
    <section class="" id="container">
        <!--header start-->
        <header class="header white-bg">
            <div class="sidebar-toggle-box">
                <div class="icon-reorder tooltips" data-original-title="Toggle Navigation" data-placement="right">
                </div>
            </div>
            <!--logo start-->
            <a class="logo" href="#">
                    <span>
                        克瑞米艾
                    </span>
                </a>
            <!--logo end-->
            <div class="nav notify-row" id="top_menu">
                <!--  notification start -->
                <!--  notification end -->
            </div>
            <div class="top-nav ">
                <!--search & user info start-->
                <ul class="nav pull-right top-menu">
                    <li>
                        <input class="form-control search" placeholder="Search" type="text">
                        </input>
                    </li>
                    <!-- user login dropdown start-->
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                <img alt="" src="/favicon.ico">
                                    <span class="username">
                                        <?php echo $_SESSION['nickname']; ?>
                                    </span>
                                    <b class="caret">
                                    </b>
                                </img>
                            </a>
                        <ul class="dropdown-menu extended logout">
                            <div class="log-arrow-up">
                            </div>
                            <li>
                                <a href="../../logout.php">
                                        <i class="icon-key">
                                        </i>
                                        退出登录
                                    </a>
                            </li>
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
            <div class="nav-collapse " id="sidebar">
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
                        <a class="" href="../index.php">
                                <i class="icon-shopping-cart">
                                </i>
                                <span>
                                    补货订单
                                </span>
                            </a>
                    </li>
                    <li class="active">
                        <a class="" href="javascript:void();">
                                <i class="icon-cogs">
                                </i>
                                <span>
                                    SalesOrders
                                </span>
                            </a>
                    </li>
                     <li class='active'>
                      <a class="" href="../store.php">
                          <i class="icon-user"></i>
                          <span>Store List</span>
                      </a>
                    </li>
                     <li class='active'>
                      <a class="" href="../add_admin.php">
                          <i class="icon-user"></i>
                          <span>AddAdmin</span>
                      </a>
                    </li>
                   <!-- 
                  <li class="sub-menu">
                      <a href="javascript:;" class="">
                          <i class="icon-cogs"></i>
                          <span>SalesOrders</span>
                          <span class="arrow"></span>
                      </a>
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
                <!--state overview start-->
                <div class="row state-overview">
                    <div class="col-lg-6 col-sm-6">
                        <section class="panel" style="margin-bottom: 0;">
                            <div class="symbol terques">
                                <i class="icon-user">
                                    </i>
                            </div>
                            <div class="value">
                                <h1 style=" font-size: 26px; padding-top: 10px; color: #333;">
                                        <?php echo $store_total;?>
                                    </h1>
                                <p>
                                    商户数量
                                </p>
                            </div>
                        </section>
                    </div>
                    <div class="col-lg-6 col-sm-6">
                        <section class="panel" style="margin-bottom: 0;">
                            <div class="symbol red">
                                <i class="icon-tags">
                                    </i>
                            </div>
                            <div class="value">
                                <h1 style=" font-size: 26px; padding-top: 10px; color: #333;">
                                        <?php echo $total;?>
                                    </h1>
                                <p>
                                    记录总数
                                </p>
                            </div>
                        </section>
                    </div>
                </div>
                <!--state overview end-->
            </section>
            <!--main content start-->
            <section class="wrapper" style="margin-top: 10px; padding: 0px 15px  0px  15px;">
                <div class="row">
                    <div class="col-lg-12">
                        <!--breadcrumbs start -->
                        <ul class="breadcrumb" style="margin-bottom: 0;">
                            <!--<li><a href="#"><i class="icon-home"></i> 补货订单</a></li>
                          <li><a href="#">Library</a></li>-->
                            <li class="active">
                                <i class="icon-home">
                                    </i> 在线销售账单
                            </li>
                        </ul>
                        <!--breadcrumbs end -->
                    </div>
                </div>
            </section>
            <section>
                <section class="wrapper" style="margin-top: 10px; padding: 0px 15px  15px  15px;">
                    <!-- page start-->
                    <div class="row">
                        <div class="col-lg-12">
                            <section class="panel">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <section class="">
                                            <div class="panel-body">
                                                <form action="#" class="form-horizontal tasi-form">
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-1">
                                                            选择订单类型
                                                        </label>
                                                        <div class="col-sm-6">
                                                          <div class="input-prepend">
                                                                线上账单：<input type="radio" value='online' name='order' checked />
                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                线下账单：<input type="radio" value='offline' name='order' />
                                                            </div>
                                                            <!-- <input class="form-control" id="byNumber" type="number" placeholder="线上账单输入1 OR 线下实际账单输入0 默认为0" /> -->
                                                        </div> 
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-sm-1">
                                                            一键下载订单
                                                        </label>
                                                        <div class="col-sm-6">
                                                            <div class="input-prepend">
                                                                导出订单起始日期：<input  id="dateStart" type="date" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                导出订单截至日期：<input  id="dateEnd" type="date" />
                                                            </div>
                                                        </div>
                                                        <button class="btn btn-shadow btn-info" id="dateButton" type="button">
                                                            下载订单
                                                        </button>
                                                    </div>
                                                    <!--date picker end-->
                                                </form>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                    <table class="table table-striped border-top" id="sample_1" style="background-color:#FFF;">
                        <thead>
                            <tr>
                                <th class="hidden-phone" style=" width: 14.2%;">
                                    机器编号
                                </th>
                                <th class="hidden-phone" style=" width: 14.2%;">
                                    商店名称
                                </th>
                                <th class="hidden-phone" style=" width: 14.2%;">
                                    统计日期
                                </th>
                                <th>
                                    郁金香雪吻
                                </th>
                                <th class="hidden-phone" style=" width: 14.2%;">
                                    格罗宁根黑松
                                </th>
                                <th class="hidden-phone" style=" width: 14.2%;">
                                    海牙圣杯
                                </th>
                                <th class="hidden-phone" style=" width: 14.2%;">
                                    总数量
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($lists) && is_array($lists)) { ?>
                            <?php foreach($lists as $key=>$val){ ?>
                            <tr class="odd gradeX">
                                <td style="line-height: 30px;">
                                    <?= $val['sn']?>
                                </td>
                                <td style="line-height: 30px;">
                                    <?= $val['name'] ?>
                                </td>
                                <td style="line-height: 30px;">
                                    <?php echo $val['date']; ?>
                                </td>
                                <td style="line-height: 30px;">
                                    <?php echo isset($val[39])? $val[39]: 0; ?>
                                </td>
                                <td class="hidden-phone" style="line-height: 30px;">
                                    <?php echo isset($val[42])?$val[42] : 0; ?>
                                </td>
                                <td class="center hidden-phone" style="line-height: 30px;">
                                    <?php echo isset($val[43])?$val[43] : 0; ?>
                                </td>
                                <td class="hidden-phone" style="line-height: 30px;">
                                    <?php echo $val['total']; ?>
                                </td>
                                
                            </tr>
                            <?php } ?>
                            <?php }else { ?>
                            <tr class="odd gradeX">
                                <td align="center" colspan="10">
                                    补货账单为空
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <div class="text-center" id='paginate'>
                        <ul class="pagination">
                            <li>
                                <?php if($curPage > 1){ ?>
                                  <a href="?<?php $prev = $curPage -1; echo query_encode("page=$prev"); ?>">
                                        «
                                    </a>
                                <?php } ?>
                            </li>
                            <li>
                                <a href="#" style='color: green;font-weight:bold;'>
                                        <?php echo $curPage; ?>
                                    </a>
                            </li>
                            <li>
                               <?php if($curPage < $totalPage){ ?>
                                   <a href="?<?php $next = $curPage + 1; echo query_encode("page=$next"); ?>">
                                        »
                                    </a>
                               <?php } ?>
                            </li>
                        </ul>
                    </div>
                </section>
            </section>
        </section>
    </section>
</body>

</html>
<!-- page end-->
<script src="../js/jquery.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script type="text/javascript" charset="utf-8" async defer>
  $("#dateButton").click(function(res){
    var order = $('input[name="order"]:checked').val();
    var dateStart = $('#dateStart').val();
    if(dateStart == ''){
      alert('查询起始日期不能为空');
      return false;
    }
    var dateEnd = $('#dateEnd').val();
    if(dateEnd  == ''){
      alert('查询截至日期不能为空');
       return false;
    }
    var startTime = new Date(dateStart);
    var endTime = new Date(dateEnd);
    if( startTime.getTime() > endTime.getTime() ){
      alert('搜索截至日期必须大于或等于搜索日期');
      return false;
    }
    location.href = './import_order.php?type='+order+'&dateStart='+dateStart+'&dateEnd='+dateEnd;
   /* $.post('./import_order.php', {type:order,dateStart:dateStart,dateEnd:dateEnd}, function(res){
      if(res.code == 0){
        location.href ='./download.php?url='+res.file+'&token='+res.token;
      }else{
        alert(res.msg+'请稍后再试');
      }
    },'json')*/
  })
</script>