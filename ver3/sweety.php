<?php
/**
 * Author: wanglong
 * Date: 2018-06-06
 * Desc: This Is Get Store Sweety file
 */
require_once './Api/signature_db.php';
$thisMonthStat = 0;
$lastMonthStat = 0;

$last_31       = array();
$last_31_index = array();
$categories    = array();
$machines      = array();
$machineNames  = array();
$address = [];
for ($i = 0; $i < 31; $i++) {
    $last_31[$i]    = 0;
    $this_day       = strtotime(date("Y-m-d")) - 86400 * $i;
    $categories[$i] = date("m-d", $this_day);
    $month          = date("m", $this_day);
    $day            = date("d", $this_day);

    $last_31_index[$month * 31 + $day] = 30 - $i;
}

$categories = array_reverse($categories);
//(1)get machine names
$sql    = "SELECT machine.sn,machine.name,p.enable,p.starttime,p.address FROM machine,weixin,place p WHERE machine.sn=weixin.sn AND machine.sn=p.sn AND weixin.sn=p.sn AND wid=" . $wid . " ORDER BY machine.sn ASC";
$result = $mysqli->query($sql);
if ($result === false) {
    echo $mysqli->error;
    echo $mysqli->errno;
}
while ($row = $result->fetch_assoc()) {
    $name = $row['name'];
    if (empty($name)) {
        $name = $row['sn'];
    }

    $machines[]     = $row['sn'];
    $machineNames[] = $name;
    $address[] = $row['address'];
    $enables[]      = $row['enable'];
    $starttimes[]   = $row['starttime'];
}
//(2)calculate of the month and last month
$thismonthyear = date("Y");
$thismonth     = date("m");
$lastmonthyear = $thismonthyear;
$lastmonth     = $thismonth - 1;
if ($lastmonth < 1) {
    $lastmonth     = 12;
    $lastmonthyear = $thismonthyear - 1;}
$last2monthyear = $lastmonthyear;
$last2month     = $lastmonth - 1;
if ($last2month < 1) {
    $last2month     = 12;
    $last2monthyear = $lastmonthyear - 1;}

//(3)get store's sweety and return result
if (isset($_GET["sn"])) {
    $sn = $_GET["sn"];
    //(1)Judging the name of the query table based on the sales pattern
    $online_sql    = "SELECT enable,starttime FROM place WHERE sn=" . $sn;
    $online_result = $mysqli->query($online_sql);
    if ($online_result === false) {
        echo $mysqli->error;
    }
    $online = 0;
    if ($online_row = $online_result->fetch_assoc()) {
        $online    = $online_row['enable'];
        $starttime = $online_row['starttime'];
    }
    if ($online == 0) {
        $table = 'diary';
    } else {
        $table = 'so_diary';
    }

    //(2)query same Month sweety And last Month sweety
    $thisMonthStat = get_month_count($sn, $mysqli, $thismonthyear, $thismonth, $table);
    $lastMonthStat = get_month_count($sn, $mysqli, $lastmonthyear, $lastmonth, $table);

    //(3)last 31 days
    $sql = "SELECT SUM(count) as ct, year, month, day FROM " . $table . " WHERE sn=" . $sn . " AND DATEDIFF(CONCAT(year,'-',month,'-',day),'" . $starttime . "')>=0 GROUP BY year, month, day, sn
            ORDER BY year DESC, month DESC, day DESC LIMIT 31";

    $result = $mysqli->query($sql);
    if ($result === false) {
        echo $mysqli->error;
    } else {
        while ($row = $result->fetch_assoc()) {
            $counter = $row['ct'];
            $day     = $row['day'];
            $month   = $row['month'];

            if (isset($last_31_index[$month * 31 + $day])) {
                $idx = $last_31_index[$month * 31 + $day];
                if ($counter < 0) {
                    $counter = 0;
                }

                $last_31[$idx] = $counter;
            }
        }
    }
} else {

    //(1)Judging the name of the query table based on the sales pattern
    if ($enables[0] == 0) {
        $table = 'diary';
    } else {
        $table = 'so_diary';
    }

    //(2)query same Month sweety And last Month sweety
    $thisMonthStat = get_month_count($machines[0], $mysqli, $thismonthyear, $thismonth, $table);
    $lastMonthStat = get_month_count($machines[0], $mysqli, $lastmonthyear, $lastmonth, $table);

    //(3)last 31 days
    $sql = "SELECT SUM(count) as ct, year, month, day FROM " . $table . " WHERE sn=" . $machines[0] . " AND  DATEDIFF(CONCAT(year,'-',month,'-',day),'" . $starttimes[0] . "')>=0 GROUP BY year, month, day, sn
            ORDER BY year DESC, month DESC, day DESC LIMIT 31";

    $result = $mysqli->query($sql);
    if ($result === false) {
        echo $mysqli->error;
    } else {
        while ($row = $result->fetch_assoc()) {
            $counter = $row['ct'];
            $day     = $row['day'];
            $month   = $row['month'];

            if (isset($last_31_index[$month * 31 + $day])) {
                $idx = $last_31_index[$month * 31 + $day];
                if ($counter < 0) {
                    $counter = 0;
                }

                $last_31[$idx] = $counter;
            }
        }
    }
}
$mysqli->close();

$sweety = $last_31[30];
$arr    = array("sweety" => $sweety, "this_month" => $last_31, "this_month_stat" => $thisMonthStat, "last_month_stat" => $lastMonthStat, "categories" => $categories, "machines" => $machines, "machine_names" => $machineNames,'address'=>$address);
echo json_encode($arr);

/**
 * 获取机器每月最大的售卖数量
 * @param  [type] $sn  [description]
 * @param  [type] $my  [description]
 * @param  [type] $yea [description]
 * @param  [type] $mon [description]
 * @return [type]      [description]
 */
function get_month_count($sn, $my, $yea, $mon, $table)
{
    $max_val = 0;

    //$sql = "SELECT SUM(count) AS ct FROM diary WHERE month=".$mon." AND year=".$yea." AND sn='".$sn."'";
    $sql = "SELECT SUM(count) AS ct FROM " . $table . " WHERE month=" . $mon . " AND year=" . $yea . " AND sn='" . $sn . "'";

    $result = $my->query($sql);
    if ($result === false) {
        echo $my->error;
        echo $my->errno;
    } else {
        while ($row = $result->fetch_assoc()) {
            $max_val += $row['ct'];
        }
    }
    //echo $max_val." ..  ";
    return $max_val;
}
