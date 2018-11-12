<?php
/**
 * @author Wanglong  <[1285505793@qq.com]>
 * Date   2018-06-28
 * Desc   This page is stat details
 */
require_once './Api/signature_db.php';
$sn    = $_GET["sn"];
$year  = $_GET["year"];
$month = $_GET["month"];
if (empty($year)) {
    $year = date("Y");
}

if (empty($month)) {
    $month = date("n");
}

$machine     = array();
$weixin      = array();
$bill_total  = 0;
$bill_amount = 0;
$bill        = array();
if (!empty($sn)) {
    //(1)query Machine detail from sn
    $sql    = "SELECT m.sn,m.name, m.ver,p.starttime,p.endtime,p.store,p.address,p.zone,p.enable FROM machine m LEFT JOIN place p ON p.sn=m.sn WHERE m.sn=" . $sn;
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
        $machine['enable']    = $row["enable"];
    }
    //(2)FROM stat time, monthly bill
    if ($machine['enable'] == 1) {
        $sql = "SELECT d.year, d.month, d.day, d.count,d.amount,p.name,d.pro_id,concat(d.year,'-',d.month,'-',d.day) date  FROM so_diary d,product p WHERE d.pro_id=p.id AND d.sn=" . $sn . "
		AND d.year=" . $year . " AND d.month=" . $month . " AND DATEDIFF(CONCAT(d.year,'-',d.month,'-',d.day),'" . $machine['starttime'] . "') >=0 GROUP BY d.pro_id,d.day ORDER BY d.day DESC";
        //get result
        $result = $mysqli->query($sql);
        if ($result === false) {
            echo $mysqli->error;
        }
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $bill_total += $row["count"];
            $bill_amount += $row['amount'];
            $bill[$i++] = $row;
        }
        $bill = cusMergeArr($bill, 'date', 'count', 'amount', 'name', 'pro_id');
    } else {
        $sql = "SELECT year,month,day,count,count39,count42,count43,concat(year,'-',month,'-',day) date  FROM diary WHERE sn=" . $sn . " AND year=" . $year . " AND month=" . $month . " AND
	    DATEDIFF(CONCAT(year,'-',month,'-',day),'" . $machine['starttime'] . "') >=0 ORDER BY day desc";
        //get result
        $result = $mysqli->query($sql);
        if ($result === false) {
            echo $mysqli->error;
        }
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $bill_total += $row["count"];
            $row['amount'] = $row['count'] * 12;
            $bill_amount += ($row['count'] * 12);
            $bill[$i++] = $row;
        }
    }
}
/*else
{
//(1)All machines
$sql = "SELECT machine.ver,p.enable FROM machine, weixin,place p WHERE machine.sn=weixin.sn AND machine.sn=p.sn AND wid={$wid}";

$result = $mysqli->query($sql);
if($result === false){
echo $mysqli->error;
echo $mysqli->errno;
}
while($row = $result->fetch_assoc()){
if($row['ver'] == 1) $weixin['ver']=1;
if($row['enable'] == 1) $weixin['enable']=1;
}

///////////
$sql = "SELECT count(*) as ct,nickname FROM `weixinusr` wu, weixin w WHERE w.wid=wu.id AND w.wid=".$wid;
$result = $mysqli->query($sql);
if($result === false){
echo $mysqli->error;
}
if($row = $result->fetch_assoc()){
$weixin['machine_count'] = $row["ct"];
$weixin['nickname'] = $row["nickname"];
}

$sql = "SELECT d.year,d.day,d.month, SUM(d.count) count,SUM(d.amount) amount,l.starttime,d.sn,d.pro_id,p.name,concat(d.year,'-',d.month,'-',d.day) date
FROM so_diary d, weixin w,place l,product p WHERE d.sn=w.sn AND d.sn=l.sn AND w.sn=l.sn AND d.pro_id=p.id AND d.year={$year} AND d.month={$month}
AND DATEDIFF(CONCAT(d.year,'-',d.month,'-',d.day),l.starttime)>=0 AND w.wid={$wid} GROUP BY d.year, d.month, d.day,d.pro_id ORDER BY d.day DESC";
$result = $mysqli->query($sql);
if($result === false){
echo $mysqli->error;
}
$i = 0;
while($row = $result->fetch_assoc()){
$bill_total +=$row['count'];
$bill_amount +=$row['amount'];
$bill[$i++] = $row;
}
$bill =cusMergeArr($bill,'date','count','amount','name','pro_id');
}*/
$mysqli->close();
$arr = array("machine" => $machine, "bill" => $bill, "bill_total" => $bill_total,
    "bill_amount"          => $bill_amount);
echo json_encode($arr);
function cusMergeArr($arr, $key, $return1, $return2, $return3, $suffix)
{
    $res = [];
    foreach ($arr as $val) {
        if (!$res[$val[$key]]) {
            $res[$val[$key]]                           = $val;
            $res[$val[$key]][$return1 . $val[$suffix]] = $val[$return1];
            $res[$val[$key]][$return2 . $val[$suffix]] = $val[$return2];
            $res[$val[$key]][$return3 . $val[$suffix]] = $val[$return3];
        } else {
            $res[$val[$key]][$return1] += $val[$return1];
            $res[$val[$key]][$return1 . $val[$suffix]] = $val[$return1];
            $res[$val[$key]][$return2 . $val[$suffix]] = $val[$return2];
            $res[$val[$key]][$return3 . $val[$suffix]] = $val[$return3];
        }
    }
    //return nummeric index arr
    $returnArr = [];
    foreach ($res as $val) {
        $returnArr[] = $val;
    }
    return $returnArr;
}
function arrToNewArr($arr, $key, $return1, $return2, $return3, $suffix)
{
    $res = [];
    foreach ($arr as $val) {
        if (!$res[$val[$key]]) {
            $res[$val[$key]]                           = $val;
            $res[$val[$key]][$return1 . $val[$suffix]] = $val[$return1];
            $res[$val[$key]][$return2 . $val[$suffix]] = $val[$return2];
            $res[$val[$key]][$return3 . $val[$suffix]] = $val[$return3];
        } else {
            $res[$val[$key]][$return1 . $val[$suffix]] = $val[$return1];
            $res[$val[$key]][$return2 . $val[$suffix]] = $val[$return2];
            $res[$val[$key]][$return3 . $val[$suffix]] = $val[$return3];
        }
    }
    //return nummeric index arr
    $returnArr = [];
    foreach ($res as $val) {
        $returnArr[] = $val;
    }
    return $returnArr;
}
