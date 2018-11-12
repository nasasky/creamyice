<?php
require_once './Api/signature_db.php';
@$sn   = $_GET["sn"];
@$name = $_GET["name"];

if (isset($sn) && isset($name)) {

    $sql = "UPDATE machine SET name='" . $name . "' WHERE sn='" . $sn . "'";

    $result = $mysqli->query($sql);
    if ($result === false) {
        echo $mysqli->error;
        echo $mysqli->errno;
    }
}
$mysqli->close();

$arr = array("status" => "ok");
echo json_encode($arr);
