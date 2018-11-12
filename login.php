<?php
$mysqli = new mysqli('localhost', 'wanglong', 'wl940207', 'ice');

if($mysqli->connect_errno){
  exit('Mysql Connect Error'.$mysqli->connect_error);
}

if(!$mysqli->set_charset('utf8')){
  exit('Mysql Set Charset Error'.$mysqli->error);
}

function randomkeys($length)
{
  $pattern='1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
  $key = '';
  for($i=0;$i<$length;$i++)
  {
    $key.=$pattern{mt_rand(0,35)};//生成php随机数
  }
  return$key;
}

$logincode=randomkeys(64);

$sql = "DELETE FROM logincode WHERE TIMESTAMPDIFF(SECOND, timestamp, CURRENT_TIMESTAMP())>125";
$result = $mysqli->query($sql);
if($result === false){
  echo $mysqli->error;
  echo $mysqli->errno;
}

$sql = "INSERT INTO logincode (code) VALUES ('".$logincode."')";
$result = $mysqli->query($sql);
if($result === false){
  echo $mysqli->error;
  echo $mysqli->errno;
}
?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
<meta http-equiv="X-UA-Compatible" content="IE=Edge，chrome=1">
<title>扫描登录-克瑞米艾</title>
<script type="text/javascript" src="js/jquery.min.js"></script>
<body style="background-color: rgb(51, 51, 51);padding: 50px;">
<!--<div style="width:100%; padding-bottom:3rem;">
<div style="width:100%; text-align: center; margin-bottom:1rem;"><img src="images/we_xcx.png" width="246"></div>
<div class="info" style="width: 280px; margin: 0 auto; ">
						<div style="padding: 7px 14px; margin-top: 15px;background-color: #232323;border-radius: 100px;-moz-border-radius: 100px;-webkit-border-radius: 100px;box-shadow: inset 0 5px 10px -5px #191919, 0 1px 0 0 #444;-moz-box-shadow: inset 0 5px 10px -5px #191919,0 1px 0 0 #444;-webkit-box-shadow: inset 0 5px 10px -5px #191919, 0 1px 0 0 #444;">
			                <div style=" font-size: 13px; color:#FFF; text-align: center; padding-top:5px;  padding-bottom:5px;">请使用微信扫描二维码进入</div>
                      <div style=" font-size: 13px; color:#FFF; text-align: center; padding-bottom:5px;">“克瑞米艾小程序”</div>
			            </div>
			        </div>
</div>
-->
<div style="width:100%;">
              <div style="width:100%; text-align: center; margin-bottom:1rem;"><img src="qr.php?id=<?=$logincode?>" width="246"></div>
              <div class="info" style="width: 280px; margin: 0 auto; ">
              						<div style="padding: 4px 14px; margin-top: 15px;background-color: #232323;border-radius: 100px;-moz-border-radius: 100px;-webkit-border-radius: 100px;box-shadow: inset 0 5px 10px -5px #191919, 0 1px 0 0 #444;-moz-box-shadow: inset 0 5px 10px -5px #191919,0 1px 0 0 #444;-webkit-box-shadow: inset 0 5px 10px -5px #191919, 0 1px 0 0 #444;">
              			                <div style=" font-size: 13px; color:#FFF; text-align: center; padding-top:5px;  padding-bottom:5px;">请使用微信小程序扫描二维码登录</div>
                                    <div style=" font-size: 13px; color:#FFF; text-align: center; padding-bottom:5px;">“克瑞米艾”</div>
              			            </div>
              			        </div>
</div>
</body>
<script type="text/javascript">
var getting = {
  url:'checklogin.php?code=<?=$logincode?>',
  dataType:'json',
  success:function(res) {
    console.log(res);
    if(res.status == 1)
    {
      window.location.href = 'index.php';
    }
  }
};
//关键在这里，Ajax定时访问服务端，不断获取数据 ，这里是1秒请求一次。
window.setInterval(function(){$.ajax(getting)},1000);
</script>
</html>

<?php
  $mysqli->close();
?>
