<?php
ini_set('date.timezone','Asia/Shanghai');
include './db.php';
if(isset($_POST) && !empty($_POST)){
   function get_access_token( $appid  , $appsecret ,$mysqli){
      $sql ="select*from access_token where id =1";
        $result =$mysqli->query($sql);
        if($result === false){
            $data =json_decode( file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret ) , true);
            $time =time()+7000;
            $sql2 ="insert into access_token(access_token,time) values('".$data['access_token']."', {$time})";
        }else{
           $row =$result->fetch_assoc();
           $time =time()+7000;
           if(empty($row)){
            $data =json_decode( file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret ) , true);
            $sql2 ="insert into access_token(access_token,time) values('".$data['access_token']."',{$time})";
           }else{
              if($row['time'] > time()) {
                $data =$row;
              }
              else {
                $data =json_decode( file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret) , true);
                $sql2 ="update access_token set access_token='".$data['access_token']."',time={$time} where id=1";
              }       
           }
        }
        if(isset($sql2) && !empty($sql2))  $result2 =$mysqli->query($sql2);
        return $data['access_token'];
  }

  function https_curl_json($url,$data,$type){
      if($type=='json'){//json $_POST=json_decode(file_get_contents('php://input'), TRUE);
          $headers = array("Content-type: application/json;charset=UTF-8","Accept: application/json","Cache-Control: no-cache", "Pragma: no-cache");
          $data=json_encode($data);
      }
      $curl = curl_init();
      curl_setopt($curl , CURLOPT_TIMEOUT , 100);
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
      if (!empty($data)){
          curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
      }
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers );
      $output = curl_exec($curl);
      if (curl_errno($curl)) {
          //echo 'Errno'.curl_error($curl);//捕抓异常
          exit(json_encode(curl_error($curl)));
      }
      curl_close($curl);
      return $output;
  }
  //这是数据库连接及接口验证文件
  $mysqli = new mysqli('127.0.0.1', 'wanglong', 'wl940207', 'ice');
  if ($connect_errno =$mysqli->connect_errno) {
      exit("<script>alert('数据库连接失败。');window.location.href='http://wanglong.com/getQrcode.php'</script>");
  }
  //设置字符集
  if (!$mysqli->set_charset("utf8")){
    exit("<script>alert('设置字符集失败。');window.location.href='http://wanglong.com/getQrcode.php';</script>");
  }

  //小程序二维码生成文件
  $appid ='wx16cfcfded8eb72d2';
  $appsecret ='975e906d69447e41ab50cfb78c4db36b';
  
  //表单post过来的值
  $path =$_POST['path'];
  $queryStr =$_POST['queryStr'];
  $key =$_POST['key'];

  $access_token =get_access_token($appid,$appsecret,$mysqli);
  
    //exit(json_encode($access_token));
  $url ='https://api.weixin.qq.com/wxa/getwxacode?access_token='.$access_token;
  
  //组装小程序二维码所需数据
  $data =[
    'path' => $path.'?'.$key.'='.$queryStr,
    'width' =>430,
    'auto_color' =>true
  ];

  $res = https_curl_json($url,$data,'json');
  if(!$res){
     exit("<script>alert('accessToken错误。');window.location.href='http://wanglong.com/getQrcode.php';</script>");
  }
  $put = file_put_contents('./qrimg/'.$queryStr.'.jpg', $res);
  echo '<script>alert("恭喜你，生成二维码成功");window.location.href="http://wanglong.com/getQrcode.php";</script>';
}

?>
<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8" />
      <meta name="viewport" content="initial-scale=1, maximum-scale=3, minimum-scale=1, user-scalable=no">
      <title>Create wanglong miniprogram Qrcode image</title>
      <style type="text/css">
        body{
          width:100%;
          height:100%;
        }
        div{
          margin:100px auto;
          width:500px;
        }
        form input{
          margin:30px auto;
        }
      </style>
   </head>
   <body>
     <div>
        <h1>Create Qrcode image</h1>
        <hr />
        <form action="getQrcode.php" method="POST">
            选择时间：<input type="date" name="bdaytime"> <br />
            选择路径：<input type="text" value="page/detail/detail" name="path" /> <br />
            设置key：<input type="text" value="sn" name="key" /> <br />
            设置参数：<input type="text" value="2017090030" name="queryStr" /> <br />
            <input type="submit" style="background-color:green;border-radius:0 0;" value="CREATE QR CODE" /> 
        </form>
     </div>
     <hr />
   </body>
</html>
