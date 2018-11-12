<?php
if ($_POST) {
    $mysqli = new mysqli('127.0.0.1', 'root', 'wl940207', 'ice');
    if ($mysqli->connect_errno) {
        exit('Mysql Connect Error' . $mysqli->connect_error);
    }
    if (!$mysqli->set_charset('utf8')) {
        exit('Mysql Set Charset Error' . $mysqli->error);
    }
    $username = stripslashes(trim($_POST['username']));
    $password = md5(md5(trim($_POST['password'])));
    $sql      = "SELECT id,lastlogin,account FROM weixinusr WHERE account='{$username}' AND password='{$password}' LIMIT 1";
    $result   = $mysqli->query($sql);
    if ($result === false) {
        echo "<script>alert(服务器正忙，请稍候再试);window.location.reload;</script>";
    } else {
        if ($row = $result->fetch_assoc()) {
            $wid       = $row['id'];
            $nickname  = $row['account'];
            $lastlogin = $row['lastlogin'];
            session_start();
            $_SESSION['wid']       = $wid;
            $_SESSION['nickname']  = $nickname;
            $_SESSION['lastlogin'] = $lastlogin;
            if($wid == 39){
              header('Location: ./replenishment/index.php');
              exit();
            }else if($wid == 700){
              header('Location: ./index.php');
            }else{
              echo "<script>alert('account dont permission!!!');window.location.reload;</script>";
            }            
        }else{
            echo "<script>alert('账号或密码错误');window.location.reload;</script>";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Mosaddek">
    <meta name="keyword" content="FlatLab, Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
    <link rel="shortcut icon" href="./replenishment/img/favicon.html">

    <title>克瑞米艾-登录</title>

    <!-- Bootstrap core CSS -->
    <link href="./replenishment/css/bootstrap.min.css" rel="stylesheet">
    <link href="./replenishment/css/bootstrap-reset.css" rel="stylesheet">
    <!--external css-->
    <link href="./replenishment/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <!-- Custom styles for this template -->
    <link href="./replenishment/css/style.css" rel="stylesheet">
    <link href="./replenishment/css/style-responsive.css" rel="stylesheet" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
</head>

  <body class="login-body">

    <div class="container">

      <form class="form-signin" method='post' action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <h2 class="form-signin-heading">克瑞米艾商户版</h2>
        <div class="login-wrap">
            <input type="text" class="form-control" name='username' placeholder="用户名" autofocus required>
            <input type="password" class="form-control" name='password' placeholder="密码" required>
            <!-- <label class="checkbox">
                <input type="checkbox" value="remember-me"> Remember me
            </label> -->
            <input class="btn btn-lg btn-login btn-block" type="submit" value='登陆'>


        </div>

      </form>

    </div>


  </body>
</html>

<!-- <!DOCTYPE html>
<html>
<head>

<title>登录-克瑞米艾</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel='icon' href='images/logo.ico' type='image/x-ico' />
  <style type="text/css" media="screen">
      body{
         background-color: rgba(240, 255, 255, 0.5);

          border-radius: 10px;
      }
      #login_frame {
          width: 100%;
          height: 600px;
          padding: 25px;

          position: absolute;
          top:20%;
          text-align: center;
      }

      form p > * {
          display: inline-block;
          vertical-align: middle;
      }

      #image_logo {
          margin-top: 22px;
      }

      .label_input {
          font-size: 14px;
          font-family: 宋体;

          width: 65px;
          height: 28px;
          line-height: 28px;
          text-align: center;

          color: white;
          background-color: #3CD8FF;
          border-top-left-radius: 5px;
          border-bottom-left-radius: 5px;
      }

      .text_field {
          width: 278px;
          height: 28px;
          border-top-right-radius: 5px;
          border-bottom-right-radius: 5px;
          border: 0;
      }

      #btn_login {
          font-size: 14px;
          font-family: 宋体;

          width: 120px;
          height: 28px;
          line-height: 28px;
          text-align: center;

          color: white;
          background-color: #3BD9FF;
          border-radius: 6px;
          border: 0;

          float: left;
      }

      #forget_pwd {
          font-size: 12px;
          color: white;
          text-decoration: none;
          position: relative;
          float: right;
          top: 5px;

      }

      #forget_pwd:hover {
          color: blue;
          text-decoration: underline;
      }

      #login_control {
          padding: 0 28px;
      }
  </style>
</head>
<body>
  <div id="login_frame">
    <h1>克瑞米艾管理员登录</h1>
    <form method="post">

        <p><label class="label_input">用户名</label><input type="text" name="username" class="text_field"/></p>
        <p><label class="label_input">密码</label><input type="text" name="password" class="text_field"/></p>

        <div id="login_control">
            <input type="submit" value="登录"/>
        </div>
    </form>
</div>
</body>
</html> -->