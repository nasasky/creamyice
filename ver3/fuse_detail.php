<?php
/**
 * Author: wanglong
 * Date: 2018-06-06
 * Desc: This Is Store FuseDetails file
 */
require_once './Api/signature_db.php';

$sn    = $_GET["sn"];
$year  = $_GET["year"];
$month = $_GET["month"];

$machine      = array();
$weixin       = array();
$price        = 0;
$bill_total43 = 0;
$bill_amount  = 0;
$bill         = array();
if (!empty($sn)) {
    //(1)query store detail
    $sql    = "SELECT m.sn,m.name, m.ver,p.starttime,p.endtime,p.store,p.address,p.zone FROM machine m LEFT JOIN place p ON p.sn=m.sn WHERE m.sn=" . $sn;
    $result = $mysqli->query($sql);
    if ($result === false) {
        echo $mysqli->error;
        echo $mysqli->errno;
    }
    if ($row = $result->fetch_assoc()) {
        $machine['sn']        = $row["sn"];
        $machine['name']      = $row["name"];
        $machine['store']     = $row["store"];
        $machine['starttime'] = $row["starttime"];
        $machine['endtime']   = $row["endtime"];
        $machine['address']   = $row["address"];
        $machine['zone']      = $row["zone"];
        $machine['ver']       = $row["ver"];
    }
    //(2)FROM stat time, monthly and days bill
    $sql = "SELECT d.sn,d.year, d.month, d.day, d.num count43  FROM new_cusproduct d WHERE d.sn={$sn} AND d.year={$year} AND d.month={$month}
	AND DATEDIFF(CONCAT(d.year,'-',d.month,'-',d.day),'" . $machine['starttime'] . "')>=0 ORDER BY d.day DESC";

    $result = $mysqli->query($sql);
    if ($result === false) {
        echo $mysqli->error;
    }
    while ($row = $result->fetch_assoc()) {
        $bill_total43 += $row["count43"];
        $bill_amount += $row['count43'] * 3;
        $bill[] = $row;
    }
} else {

    //(1)query store nickname and count machine
    $sql    = "SELECT count(*) as ct,nickname FROM `weixinusr` wu, weixin w WHERE w.wid=wu.id AND w.wid=" . $wid;
    $result = $mysqli->query($sql);
    if ($result === false) {
        echo $mysqli->error;
        echo $mysqli->errno;
    }
    if ($row = $result->fetch_assoc()) {
        $weixin['machine_count'] = $row["ct"];
        $weixin['nickname']      = $row["nickname"];
    }

    //(2)query total store monthly and days bill
    $sql2 = "SELECT d.sn,d.year,d.day,d.month, SUM(d.num) as count43 FROM new_cusproduct d, weixin w,place l WHERE d.sn=w.sn
    AND d.sn=l.sn AND w.sn=l.sn AND d.year={$year} AND d.month={$month} AND w.wid={$wid}
	AND DATEDIFF(CONCAT(year,'-',month,'-',day),l.starttime)>=0 GROUP BY d.sn,d.year,d.month,d.day ORDER BY d.day desc,d.sn";

    $result2 = $mysqli->query($sql2);
    if ($result2 === false) {
        echo $mysqli->error;
    }
    $i = 0;
    while ($row2 = $result2->fetch_assoc()) {
        $bill_total43 += $row2["count43"];
        $bill_amount += ($row2['count43'] * 3);
        $row2['amount'] = $row2['count43'] * 3;
        $bill[]         = $row2;
    }
    $bill = arrToNewArr($bill, 'day', 'count43', 'amount');
}
$mysqli->close();

//$bill_amount = $bill_total43*$price;
$arr = array("machine" => $machine, "weixin"           => $weixin, "bill" => $bill,
    "bill_total43"         => $bill_total43, "bill_amount" => $bill_amount);
echo json_encode($arr);

/**
 * [arrToNewArr description]
 * @param  [type] $arr     [description]
 * @param  [type] $useKey  [description]
 * @param  [type] $return1 [description]
 * @return [type]          [description]
 */
function arrToNewArr($arr, $useKey, $return1, $return2)
{
    $res = array();
    foreach ($arr as $item) {
        if (!isset($res[$item[$useKey]])) {
            $res[$item[$useKey]] = $item;
        } else {
            $res[$item[$useKey]][$return1] += $item[$return1];
            $res[$item[$useKey]][$return2] += $item[$return2];
        }
    }
    //chongzu array
    $return = array();
    foreach ($res as $val) {
        $return[] = $val;
    }
    return $return;
}
