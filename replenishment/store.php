<?php
include './db.php';
rebuild_GET();
//var_dump($_SERVER);
$list      = [];
$total = $totalPage = 0; $size  = 20;
$sql       = "SELECT count(id) total FROM weixinusr WHERE id NOT IN(30,39,43)";
$result    = $mysqli->query($sql);
if ($result !== false) {
    if ($row = $result->fetch_assoc()) {
        $total = $row['total'];
        $totalPage = ceil($total / $size);
        $page  = (isset($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1;       
        $start = ceil(($page - 1) * $size);
        $sql   = "SELECT p.sn,p.store,p.address,p.zone,p.enable,p.active,p.count43,p.pro_list,p.type,u.account,u.password,u.id wid FROM place p,weixin w,weixinusr u
        WHERE p.sn=w.sn AND w.wid=u.id GROUP BY u.id HAVING u.id NOT IN (30,39,43) ORDER BY p.sn LIMIT $start,$size";

        $result = $mysqli->query($sql);
        if ($result === false) {
            echo 'Mysql Select Error:' . $mysqli->error;
        }
        while ($row = $result->fetch_assoc()) {
            $list[] = $row;
        }
    }
}


?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8" />
  <title>Creamyice Store List</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <style type="text/css">
        *{margin:0;padding:0;}
       body{width:100%;height:100%;background-color: #D4F3F4;}
        ul>li{list-style:none;}
        a{text-decoration: none;}
        .customer{width:100%;overflow: hidden;}
        #header{
          width:100%;
          height:60px;
          overflow: hidden;
          line-height:60px;
          color:#FF6C60;
        }
        #header .title-left{
          float:left;
          font-size:160%;

          margin-left:4%;
        }
        #header .title-middle{
          float:left;
          font-size:120%;
          margin-left:30%;
        }
        #header .title-right{
          float:right;
          margin-right: 4%;
        }
        .nav-left{
          width:10%;
          height:820px;
          background-color:#2a3542;
          float:left;
          padding-top:10px;
        }
        .nav-left .active{
          width:90%;
          height:50px;
          background-color:#35404d;
          margin:10px auto;
          font-size:80%;
          text-align:center;
          line-height: 50px;
        }
        .nav-left .active a{
          color:white;

        }
        .list{
          width:90%;
          float:right;
          font-size:80%;
        }
        .lists,.place{width:100%;overflow:hidden;}
        .place:hover{background-color:#F1F3F3; }
        .detail{width:12.4%;float:left;height:40px;text-align:center;line-height:40px;position:relative;}
        /*.detail:last-child{width:15%;border-right:none;}*/
        .place-title>.detail{background-color:#9CC4EE; font-weight:bold;}
        input[type='submit']{width:25%;background-color: #5DC3D2;border-radius: 40%;}
        input{width:60%;}
        ul.pagination {
            display: inline-block;
            padding: 0;
            margin: 0 auto;
        }
        ul.pagination li {display: inline;}
        ul.pagination li a {
            float: left;
            padding: 8px 16px;
            font-size:160%;
            text-decoration: none;
        }
  </style>
</head>
<body>
  <div class="customer">
        <header id="header" class="titles">
          <div class='title-left'>克瑞米艾</div>
          <div class='title-middle'>
            <span><?php echo $total; ?>家商户，共<?php echo $totalPage; ?>页
              <a style='background-color:red;border-radius: 40%;color:black;margin-left:40px;font-size:70%;' href='download_account.php'>导出账号列表</a>
            </span>
          </div>
          <div class="title-right">
            <img src="/favicon.ico" alt="log">
            <span><?php echo $_SESSION['nickname']; ?></span>
            <a href='../logout.php'>退出登录</a>
          </div>
        </header><!-- /header -->
        <div class="nav-left">
                <!-- sidebar menu start-->
                <ul class="menu">
                   <li class="active">
                        <a class="" href="../../index.php">Creamyice首页</a>
                    </li>
                    <li class="active">
                        <a class="" href="./index.php">补货订单</a>
                    </li>
                    <li class="active">
                        <a class="" href="./sales/list.php">Export订单</a>
                    </li>
                    <li class='active'>
                      <a class="" href="javascript:void();">Manager商户</a>
                    </li>
                    <li class='active'>
                      <a class="" href="./add_admin.php">Add账号</a>
                    </li>
                </ul>
        </div>
       <ul class="list">
           <li class="lists">
               <ul class="place-title">
                <li class="detail">商店名称</li>
                <li class="detail">机器编号</li>
                <li class="detail">销售产品</li>
                <li class="detail">销售模式</li>
                <li class="detail">融合开关</li>
                 <li class="detail">活动类型</li>
                <li class="detail">登录账号</li>
                <li class="detail">密码重置</li>

               </ul>
           </li>
           <?php if ($list && is_array($list)) {?>
            <?php foreach ($list as $key => $val): ?>
           <li class="lists">
               <ul class="place">
                <li class="detail"><?php echo $val['store'] ?></li>
                <li class="detail"><?php echo $val['sn'] ?></li>
                <li class="detail">
               <input type="text" id="p<?php echo $val['wid'] ?>" value="<?php echo $val['pro_list'] ?: '暂无上架产品'; ?>" name="pros"/>&nbsp;&nbsp;&nbsp;<input type="submit" value="SWITCH" onclick="pros(<?php echo $val['wid']; ?>,<?php echo $val['sn']; ?>);" />
                </li>
                <li class="detail"><input type="number" min="0" max="1" id="e<?php echo $val['wid'] ?>" value="<?php echo $val['enable'] ?>" name="enable"/>&nbsp;&nbsp;&nbsp;<input type="submit" value="SWITCH" onclick="enable(<?php echo $val['wid']; ?>,<?php echo $val['sn']; ?>);" /></li>
                <li class="detail"><input type="number" min="0" max="1" id="cou<?php echo $val['wid'] ?>" value="<?php echo $val['count43'] ?>" name="count43"/>&nbsp;&nbsp;&nbsp;<input type="submit" value="ON-OFF" onclick="count43(<?php echo $val['wid']; ?>,<?php echo $val['sn']; ?>);" /></li>

                <li class="detail"><input type="number" min="0" max="3" id="t<?php echo $val['wid'] ?>" value="<?php echo $val['type'] ?>" name="types"/>&nbsp;&nbsp;&nbsp;<input type="submit" value="SWITCH" onclick="types(<?php echo $val['wid']; ?>,<?php echo $val['sn']; ?>);" /></li>

                <li class="detail"><input type="text" id="a<?php echo $val['wid'] ?>" value="<?php echo $val['account'] ?>" name="account"/>&nbsp;&nbsp;&nbsp;<input type="submit" value="CHANGE" onclick="account(<?php echo $val['wid']; ?>,<?php echo $val['sn']; ?>);" /></li>
                <li class="detail"><input type="password" id="w<?php echo $val['wid'] ?>" name="password"/>&nbsp;&nbsp;&nbsp;<input type="submit" value="RESET" onclick="resetPassword(<?php echo $val['wid'] ?>);" /></li>
               </ul>
           </li>
           <?php endforeach;?>
           <?php }?>
           <div style='text-align:center;'>
             <ul class="pagination">
              <?php if ($page > 1) {?>
                <li><a href="?<?php $prev = $page -1; echo query_encode("page=$prev");?>">«</a></li>
              <?php }?>
              <li><a class="active" href="javascript:void()"><?php echo $page; ?></a></li>
              <?php if ($page < $totalPage) {?>
                <li><a href="?<?php $next = $page +1; echo query_encode("page=$next");?>">»</a></li>
              <?php }?>
            </ul>
           </div>
       </ul>
  </div>
  <script type="text/javascript">
     var xmlHttpRequest;
     function resetPassword(id){
         var wid =id;
         var pwd =document.getElementById('w'+wid).value;
         if(pwd == ''){
            alert('Please Enter Password');
            return;
         }
         //Call a function to Send a Request
         xmlSend('/resetPassword.php?wid='+wid+'&password='+pwd+'&type=reset','GET',null,zswFun);
     }
     function pros(id,sn){
         var wid =id;
         var pros =document.getElementById('p'+wid).value;
         //Call a function to Send a Request
         xmlSend('/resetPassword.php?wid='+wid+'&pros='+pros+'&sn='+sn+'&type=pros','GET',null,zswFun);
     }
     function account(id,sn){
         var wid =id;
         var account =document.getElementById('a'+wid).value;
         if( account == '' ){
            alert('Parameter Illegality ');
            return;
         }
         //Call a function to Send a Request
         xmlSend('/resetPassword.php?wid='+wid+'&account='+account+'&sn='+sn+'&type=change','GET',null,zswFun);
     }

     function enable(id,sn){
         var wid =id;
         var enable =document.getElementById('e'+wid).value;
         if( enable == '' ){
            alert('Parameter Illegality ');
            return;
         }
         //Call a function to Send a Request
         xmlSend('/resetPassword.php?wid='+wid+'&enable='+enable+'&sn='+sn+'&type=switch','GET',null,zswFun);
     }

      function types(id,sn){
         var wid =id;
         var types =document.getElementById('t'+wid).value;
         if( types == '' ){
            alert('Parameter Illegality ');
            return;
         }
         //Call a function to Send a Request
         xmlSend('/resetPassword.php?wid='+wid+'&types='+types+'&sn='+sn+'&type=types','GET',null,zswFun);
      }

      function count43(id,sn){
         var wid =id;
         var count43 =document.getElementById('cou'+wid).value;
         if( count43 == '' ){
            alert('Parameter Illegality ');
            return;
         }
         //Call a function to Send a Request
         xmlSend('/resetPassword.php?wid='+wid+'&count43='+count43+'&sn='+sn+'&type=count43','GET',null,zswFun);
      }
      //XmlHttpRequest对象
      function createXmlHttpRequest(){
          if(window.ActiveXObject){ //如果是IE浏览器
              return new ActiveXObject("Microsoft.XMLHTTP");
          }else if(window.XMLHttpRequest){ //非IE浏览器
              return new XMLHttpRequest();
          }
      }

      function xmlSend(url,method,data,callback){

          //1.创建XMLHttpRequest组建
          xmlHttpRequest = createXmlHttpRequest();

          //2.设置回调函数
          xmlHttpRequest.onreadystatechange = callback;

          //3.初始化XMLHttpRequest组建
          xmlHttpRequest.open(method,url,true);

          //4.发送请求
          xmlHttpRequest.send(data);
      }

      //回调函数
      function zswFun(){
          if(xmlHttpRequest.readyState == 4 && xmlHttpRequest.status == 200){
              var b = xmlHttpRequest.responseText;
              var json =eval("("+b+")");
              alert(json.msg);
          }
      }
  </script>
</body>
</html>