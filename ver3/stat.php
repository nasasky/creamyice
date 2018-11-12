<?php
/**
 * @author [Wanglong] <[1285505793@qq.com]>
 * Date   2018-06-28
 * Desc   this page is stat
 */
require_once './Api/signature_db.php';
@$sn  = (int) $_GET["sn"];
@$wid = (int) $_GET["wid"];

$machine      = array();
$weixin       = array();
$bill_total   = 0;
$bill_amount  = 0;
$bill         = array();
$machines     = array();
$machineNames = array();
if (!empty($sn)) {
    //(1)query store machine detail from sn
    $sql    = "SELECT m.sn,m.name, m.ver,p.starttime,p.endtime,p.store,p.address,p.zone,p.enable FROM machine m LEFT JOIN place p ON p.sn=m.sn WHERE m.sn={$sn}";
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
        $sql = "SELECT d.year, d.month, SUM(d.count) AS count, SUM(d.amount) amount,concat(d.year,'-',d.month) date FROM so_diary d,product p WHERE d.pro_id=p.id AND d.sn={$sn}
		AND DATEDIFF(CONCAT(d.year,'-',d.month,'-',d.day),'" . $machine['starttime'] . "')>=0 GROUP BY d.year, d.month, d.pro_id ORDER BY d.year DESC,d.month DESC LIMIT 93";
        //get result
        $result = $mysqli->query($sql);
        if ($result === false) {
            echo $mysqli->error;
        }
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $bill_total += $row['count'];
            $bill_amount += $row['amount'];
            $bill[$i++] = $row;
        }

        $bill = cusMergeArr($bill, 'date', 'amount', 'count');
    } else {
        $sql = "SELECT d.year,d.month,SUM(d.count) count,SUM(d.count39) count39,SUM(d.count42) count42,SUM(d.count43) count43,concat(d.year,'-',d.month) date FROM diary d WHERE d.sn={$sn} AND
        DATEDIFF(CONCAT(d.year,'-',d.month,'-',d.day),'" . $machine['starttime'] . "')>=0 GROUP BY d.year, d.month ORDER BY d.year DESC,d.month DESC LIMIT 90";
        //get result
        $result = $mysqli->query($sql);
        if ($result === false) {
            echo $mysqli->error;
        }
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $bill_total += $row['count'];
            $row['amount'] = $row['count'] * 12;
            $bill_amount += $row['amount'];
            $bill[$i++] = $row;
        }
    }

} else if (!empty($wid)) {
    //(1)query All machines detail
    $sql = "SELECT machine.sn, machine.name,machine.ver,p.enable,p.starttime FROM machine, weixin,place p WHERE machine.sn=weixin.sn AND machine.sn=p.sn AND wid={$wid} ORDER BY machine.sn ASC";

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

        if ($row['enable'] == 1) {
            $weixin['enable'] = 1;
        }

        $machines[]     = $row['sn'];
        $machineNames[] = $name;
        $enables[]      = $row['enable'];
        $starttimes[]   = $row['starttime'];
    }

    if ($enables[0] == 1) {
        $sql = "SELECT d.year, d.month, SUM(d.count) AS count, SUM(d.amount) amount,concat(d.year,'-',d.month) date FROM so_diary d,product p WHERE d.pro_id=p.id AND d.sn=" . $machines[0] . "
		AND DATEDIFF(CONCAT(d.year,'-',d.month,'-',d.day),'" . $starttimes[0] . "')>=0 GROUP BY d.year, d.month, d.pro_id ORDER BY d.year DESC,d.month DESC LIMIT 90";
        //get result
        $result = $mysqli->query($sql);
        if ($result === false) {
            echo $mysqli->error;
        }
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $bill_total += $row['count'];
            $bill_amount += $row['amount'];
            $bill[$i++] = $row;
        }

        $bill = cusMergeArr($bill, 'date', 'amount', 'count');
    } else {
        $sql = "SELECT d.year,d.month,SUM(d.count) count,SUM(d.count39) count39,SUM(d.count42) count42,SUM(d.count43) count43,concat(d.year,'-',d.month) date FROM diary d WHERE
        d.sn=" . $machines[0] . " AND DATEDIFF(CONCAT(d.year,'-',d.month,'-',d.day),'" . $starttimes[0] . "')>=0 GROUP BY d.year, d.month ORDER BY d.year DESC,d.month DESC LIMIT 90";
        //get result
        $result = $mysqli->query($sql);
        if ($result === false) {
            echo $mysqli->error;
        }
        $i = 0;
        while ($row = $result->fetch_assoc()) {
            $bill_total += $row['count'];
            $row['amount'] = $row['count'] * 12;
            $bill_amount += $row['amount'];
            $bill[$i++] = $row;
        }
    }
}
$mysqli->close();

//$bill_amount = $bill_total39*6 + $bill_total42*8 + $bill_total43*6;
$arr = array("machine" => $machine, "weixin"         => $weixin, "bill"              => $bill, "bill_total" => $bill_total,
    "machines"             => $machines, "machine_names" => $machineNames, 'bill_amount' => $bill_amount);
echo json_encode($arr);
/**
 * [cusMergeArr description]
 * @param  [type] $arr     [description]
 * @param  [type] $key     [description]
 * @param  [type] $return  [description]
 * @param  [type] $return2 [description]
 * @param  [type] $return3 [description]
 * @return [type]          [description]
 */
function cusMergeArr($arr, $key, $return, $return2)
{
    $res = [];
    foreach ($arr as $val) {
        if (!$res[$val[$key]]) {
            $res[$val[$key]] = $val;
        } else {
            $res[$val[$key]][$return] += $val[$return];
            $res[$val[$key]][$return2] += $val[$return2];
        }
    }
    //return nummeric index arr
    $numArr = [];
    foreach ($res as $val) {
        $numArr[] = $val;
    }
    return $numArr;
}
