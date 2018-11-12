<?php
/**
 * Auth: wanglong
 * Date: 2018-06-27
 * Desc: this is fuse order
 */
require_once './Api/signature_db.php';

@$sn          = (int) $_GET["sn"];
$machine      = array();
$weixin       = array();
$bill_total43 = 0;
$bill_amount  = 0;
$bill         = array();
$machines     = array();
$machineNames = array();
if (!empty($sn)) {
    //(1)query machine,place detail from sn
    $sql    = "SELECT m.sn,m.name, m.ver,p.starttime,p.endtime,p.store,p.address,p.zone FROM machine m LEFT JOIN place p ON p.sn=m.sn WHERE m.sn={$sn}";
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
    //(2)FROM stat time, monthly bill
    $sql = "SELECT year, month, SUM(num) AS count43  FROM new_cusproduct d  WHERE d.sn={$sn}
    AND DATEDIFF(CONCAT(year,'-',month,'-',day),'" . $machine['starttime'] . "')>=0 GROUP BY year, month ORDER BY year DESC,month DESC LIMIT 10";

    $result = $mysqli->query($sql);
    if ($result === false) {
        echo $mysqli->error;
    }
    $i = 0;

    while ($row = $result->fetch_assoc()) {
        $bill_total43 += $row["count43"];
        $bill_amount += ($row['count43'] * 3);
        $row["amount"] = $row["count43"] * 3;
        $bill[$i++]    = $row;
    }
} else {
    //(1)All machines detail from wid
    $sql = "SELECT machine.sn, machine.name,machine.ver,p.starttime FROM machine,weixin,place p WHERE machine.sn=weixin.sn AND machine.sn=p.sn AND weixin.sn=p.sn AND wid={$wid}
    AND p.enable=1 ORDER BY machine.sn ASC";

    $result = $mysqli->query($sql);
    if ($result === false) {
        echo $mysqli->error;
    }
    while ($row = $result->fetch_assoc()) {
        $name = $row['name'];
        if (empty($name)) {
            $name = $row['sn'];
        }

        if ($row['ver'] == 1) {
            $weixin['ver'] = 1;
        }

        $machines[]     = $row['sn'];
        $machineNames[] = $name;
        $starttimes[]   = $row['starttime'];
    }
    //(2)FROM stat time, monthly bill
    $sql = "SELECT year, month, SUM(num) AS count43  FROM new_cusproduct d  WHERE d.sn=" . $machines[0] . "
    AND DATEDIFF(CONCAT(year,'-',month,'-',day),'" . $starttimes[0] . "')>=0 GROUP BY year, month ORDER BY year DESC,month DESC LIMIT 10";

    $result = $mysqli->query($sql);
    if ($result === false) {
        echo $mysqli->error;
    }

    while ($row = $result->fetch_assoc()) {
        $bill_total43 += $row["count43"];
        $bill_amount += ($row['count43'] * 3);
        $row["amount"] = $row["count43"] * 3;
        $bill[]        = $row;
    }
}
$mysqli->close();

$arr = array("machine" => $machine, "weixin"           => $weixin, "bill" => $bill,
    "bill_total43"         => $bill_total43, "bill_amount" => $bill_amount,
    "machines"             => $machines, "machine_names"   => $machineNames);
echo json_encode($arr);

/**
 * [array new array]
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
            //$res[$item[$useKey]][$return2] += $item[$return2];
        }
    }
    //chongzu array
    $return = array();
    foreach ($res as $val) {
        $return[] = $val;
    }
    return $return;
}
