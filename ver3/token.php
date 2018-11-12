<?php
/**
 * @author wanglong <[1285505793@qq.com]>
 * Date   2018-06-28
 * Description   this page is token identified
 */
require_once './Api/signature_db.php';
$user      = $_GET["user"];
$code      = $_GET["code"];
$wid       = -1;
$appid     = 'wx04ca36692510a666';
$appsecret = '4570405a75d0dc0b2cc7c600a29f95f8';
if (empty($code)) {
    $this->error('Authentication empty.');
}

$session_url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret=' . $appsecret . '&js_code=' . $code . '&grant_type=authorization_code';
$session     = json_decode(file_get_contents($session_url));
if (isset($session->errcode)) {
    echo '<h1>ERR:</h1>' . $session->errcode;
    echo '<br/><h2>ERR:</h2>' . $session->errmsg;
    exit;
} else {

    $sql    = "SELECT id from weixinusr WHERE openid='" . $session->openid . "'";
    $result = $mysqli->query($sql);
    if ($result === false) {
        echo $mysqli->error;
        echo $mysqli->errno;
    }

    if ($row = $result->fetch_assoc()) {
        $wid = $row["id"];

        $sql    = "UPDATE weixinusr SET lastlogin=CURRENT_TIMESTAMP(), nickname='" . $user . "' WHERE openid='" . $session->openid . "'";
        $result = $mysqli->query($sql);
        if ($result === false) {
            echo $mysqli->error;
            echo $mysqli->errno;
        }
    } else {
        $sql    = "INSERT INTO weixinusr (openid, nickname) VALUES('" . $session->openid . "', '" . $user . "')";
        $result = $mysqli->query($sql);
        if ($result === false) {
            echo $mysqli->error;
            echo $mysqli->errno;
        }
        $sql    = "SELECT id from weixinusr WHERE openid='" . $session->openid . "'";
        $result = $mysqli->query($sql);
        if ($result === false) {
            echo $mysqli->error;
            echo $mysqli->errno;
        }

        if ($row = $result->fetch_assoc()) {
            $wid = $row["id"];
        }
    }
}
/*
$token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appsecret.'&code='.$code.'&grant_type=authorization_code';

$token = json_decode(file_get_contents($token_url));
if (isset($token->errcode))
{
echo '<h1>ERR:</h1>'.$token->errcode;
echo '<br/><h2>ERR:</h2>'.$token->errmsg;
exit;
}
$access_token_url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$appid.'&grant_type=refresh_token&refresh_token='.$token->refresh_token;

$access_token = json_decode(file_get_contents($access_token_url));
if (isset($access_token->errcode))
{
echo '<h1>ERR:</h1>'.$access_token->errcode;
echo '<br/><h2>ERR:</h2>'.$access_token->errmsg;
exit;
}
$user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token->access_token.'&openid='.$access_token->openid.'&lang=zh_CN';

$user_info = json_decode(file_get_contents($user_info_url));
if (isset($user_info->errcode))
{
echo '<h1>ERR:</h1>'.$user_info->errcode;
echo '<br/><h2>ERR:</h2>'.$user_info->errmsg;
exit;
}

echo '<pre>';
print_r($user_info);
echo '</pre>';
 */

$mysqli->close();

http_response_code(201);

$arr = array("wid" => $wid);
echo json_encode($arr);
