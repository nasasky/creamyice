<?php
/**
 * @author wanglong <[1285505793@qq.com]>
 * Date   2018-06-28
 * Description  this page is run commend
 */
require_once './Api/signature_db.php';
$sn  = $_GET["sn"];
$cmd = $_GET["cmd"];

if (isset($sn) && isset($cmd)) {
    if ($cmd == -1) {
        $sql = "INSERT INTO command (sn, cmd, addr)
				VALUES('" . $sn . "', 71, 0)";

        $result = $mysqli->query($sql);
        if ($result === false) {
            echo $mysqli->error;
            echo $mysqli->errno;
        }
    } else {
        $sql = "INSERT INTO command (sn, cmd, addr, param0, param1)
				VALUES('" . $sn . "', 83, 2, 0, " . $cmd . ")";

        $result = $mysqli->query($sql);
        if ($result === false) {
            echo $mysqli->error;
            echo $mysqli->errno;
        }

        $sql = "INSERT INTO command_history (sn, cmd, addr, param0, param1, source)
		VALUES('" . $sn . "', 83, 2, 0, " . $cmd . ", 'weixin')";

        $result = $mysqli->query($sql);
        if ($result === false) {
            echo $mysqli->error;
            echo $mysqli->errno;
        }
    }
}
$mysqli->close();

$arr = array("status" => "ok");
echo json_encode($arr);
