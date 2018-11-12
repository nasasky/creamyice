<?php
include './replenishment/db.php';

if ($_GET['type'] == 'switch') {
    if (isset($_GET['enable']) && $_GET['sn']) {
        //(3)Receive the value of the request
        $sn     = $_GET['sn'];
        $enable = $_GET['enable'];
        //(4)Assemble SQL
        $sql    = "UPDATE place SET enable=" . $enable . " WHERE sn=" . $sn;
        $result = $mysqli->query($sql);
        if ($result === false) {
            exit(json_encode(['code' => -1, 'msg' => $mysqli->error]));
        }
        //(5)Request Successful And Return message
        exit(json_encode(['code' => 0, 'msg' => 'SWITCH MODE IS Successful']));
    }
    exit(json_encode(['code' => 1, 'msg' => 'Request Parameter Error']));
}
else if ($_GET['type'] == 'change') {
    if ($_GET['wid'] && $_GET['account']) {
        $wid     = $_GET['wid'];
        $sn      = $_GET['sn'];
        $account = $_GET['account'];
        $sql     = "UPDATE weixinusr SET account='" . $account . "' WHERE id=" . $wid;
        $result  = $mysqli->query($sql);
        if ($result === false) {
            exit(json_encode(['code' => -1, 'msg' => $mysqli->error]));
        }
        //(5)Request Successful And Return message
        exit(json_encode(['code' => 0, 'msg' => 'Reset Account IS Successful']));
    }
    exit(json_encode(['code' => 1, 'msg' => 'Request Parameter Error']));
} 
else if ($_GET['type'] == 'count43') {
    if (isset($_GET['count43']) && $_GET['sn']) {
        //(3)Receive the value of the request
        $sn     = $_GET['sn'];
        $count43 = $_GET['count43'];
        //(4)Assemble SQL
        $sql    = "UPDATE place SET count43=" . $count43 . " WHERE sn=" . $sn;
        $result = $mysqli->query($sql);
        if ($result === false) {
            exit(json_encode(['code' => -1, 'msg' => $mysqli->error]));
        }
        //(5)Request Successful And Return message
        exit(json_encode(['code' => 0, 'msg' => 'ON-OFF MODE IS Successful']));
    }
    exit(json_encode(['code' => 1, 'msg' => 'Request Parameter Error']));
}
else if ($_GET['type'] == 'pros') {
    if (isset($_GET['pros']) && $_GET['sn']) {
        //(3)Receive the value of the request
        $sn     = $_GET['sn'];
        $pros = $_GET['pros'];
        //(4)Assemble SQL
        $sql    = "UPDATE place SET pro_list='" . $pros . "' WHERE sn=" . $sn;
        $result = $mysqli->query($sql);
        if ($result === false) {
            exit(json_encode(['code' => -1, 'msg' => $mysqli->error]));
        }
        //(5)Request Successful And Return message
        exit(json_encode(['code' => 0, 'msg' => 'SWITCH Product IS Successful']));
    }
    exit(json_encode(['code' => 1, 'msg' => 'Request Parameter Error']));
}
if ($_GET['type'] == 'types') {
    if (isset($_GET['types']) && $_GET['sn']) {
        //(3)Receive the value of the request
        $sn     = $_GET['sn'];
        $types = $_GET['types'];
        //(4)Assemble SQL
        $sql    = "UPDATE place SET type=" . $types . " WHERE sn=" . $sn;
        $result = $mysqli->query($sql);
        if ($result === false) {
            exit(json_encode(['code' => -1, 'msg' => $mysqli->error]));
        }
        //(5)Request Successful And Return message
        exit(json_encode(['code' => 0, 'msg' => 'SWITCH TYPE IS Successful']));
    }
    exit(json_encode(['code' => 1, 'msg' => 'Request Parameter Error']));
}
else {
    if ($_GET['wid'] && $_GET['password']) {
        //(3)Receive the value of the request
        $wid    = $_GET['wid'];
        $pwd    = $_GET['password'];
        $newPwd = md5(md5($pwd));
        //(4)Assemble SQL
        $sql    = "UPDATE weixinusr SET password='" . $newPwd . "' WHERE id=" . $wid;
        $result = $mysqli->query($sql);
        if ($result === false) {
            exit(json_encode(['code' => -1, 'msg' => $mysqli->error]));
        }
        //(5)Request Successful And Return message
        exit(json_encode(['code' => 0, 'msg' => 'Reset Password IS Successful']));
    }

    exit(json_encode(['code' => 1, 'msg' => 'Request Parameter Error']));
}
