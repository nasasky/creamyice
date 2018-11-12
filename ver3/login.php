<?php
/**
 * @author wanglong <[1285505793@qq.com]>
 * Date   2018-06-28
 * Description  this page is store version for login
 */
require_once './Api/signature_db.php';
$account  = $_GET['account'];
$password = md5(md5(substr($_GET['password'], 3)));
if (!get_magic_quotes_gpc()) {
    $account = addslashes($account);
}
//query weixinusr
$sql    = "SELECT id from weixinusr WHERE account='{$account}' AND password='{$password}'";
$result = $mysqli->query($sql);
if ($result === false) {
    echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
}
//fetch result wid
if ($row = $result->fetch_assoc()) {
    $wid    = $row["id"];
    $sql    = "UPDATE weixinusr SET lastlogin=CURRENT_TIMESTAMP() WHERE id=" . $wid;
    $result = $mysqli->query($sql);
    if ($result === false) {
        echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
    }
    echo json_encode(['code' => 0, 'msg' => 'query account and password is successful', 'wid' => $wid, 'time' => LOGINTIME, 'intervalTime' => INTERVAL]);
} else {
    echo json_encode(['code' => -1, 'msg' => 'account and password not match']);
}

$mysqli->close();
