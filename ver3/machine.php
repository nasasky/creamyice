<?php
/**
 * Auth: wanglong
 * Date: 2018-06-27
 * Desc: this is machine administration
 */
require_once './Api/signature_db.php';

if (isset($_GET['type'])) {
    $type = $_GET['type'];
    if ($type == 'getCount43') {
        $date = date('Y-n');

        $sql    = "SELECT p.sn sn,p.count43 enable FROM place p,weixin w WHERE p.sn=w.sn AND w.wid={$wid} ORDER BY p.count43 DESC";
        $result = $mysqli->query($sql);
        if ($result === false) {
            echo json_encode(['code' => -2, 'msg' => 'mysqli select is error']);
        }
        $countList = array();
        $count43   = 0;
        while ($row = $result->fetch_assoc()) {
            $countList[] = $row;
        }

        $count_sql    = "SELECT SUM(c.num) AS num FROM new_cusproduct c,weixin w WHERE c.sn=w.sn AND w.wid={$wid} AND concat(c.year,'-',c.month)='" . date('Y-n') . "'";
        $count_result = $mysqli->query($count_sql);
        if ($count_result === false) {
            echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
        }
        if ($row = $count_result->fetch_assoc()) {
            if ($row['num']) {
                $count43 = $row['num'];
            }

        }

        $mysqli->close();
        if (!$countList) {
            echo json_encode(['code' => -1, 'msg' => 'query count43 is empty']);
        }

        echo json_encode(['code' => 0, 'msg' => 'select count43 is successful!', 'countList' => $countList, 'count43' => $count43]);
    } else if ($type == 'confirmCount43') {
        $sn  = $_GET['sn'];
        $num = $_GET['num'];
        if ($sn && $num) {
            /**
             * (1)insert command to command
             * (2)write history to comamnd_history
             * (3)write new_cusproduct and generate fuse billy
             */
            $mysqli->autocommit(false);
            $in_sql    = "INSERT INTO command(sn, cmd, addr, param0, param1, param2) VALUES({$sn}, 83,46,0,{$num},0)";
            $in_result = $mysqli->query($in_sql);
            if ($in_result === false) {
                $mysqli->rollback();
                $mysqli->autocommit(true);
                $mysqli->close();
                echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
            }

            $in_sql2    = "INSERT INTO command_history(sn, cmd, addr, param0, param1, param2, source) VALUES({$sn}, 83, 46, 0,{$num},0,'wx-cre')";
            $in_result2 = $mysqli->query($in_sql2);
            if ($in_result2 === false) {
                $mysqli->rollback();
                $mysqli->autocommit(true);
                $mysqli->close();
                echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
            }

            $year  = date('Y');
            $month = date('n');
            $day   = date('j');
            $date  = date('Y-n-j');
            /**
             * begin insert query today record,if have update if no record insert
             * (1)select
             * (2)insert or update
             */
            $que_sql    = "SELECT id,num FROM new_cusproduct WHERE concat(year,'-',month,'-',day)='{$date}' AND sn={$sn}";
            $que_result = $mysqli->query($que_sql);
            if ($que_result === false) {
                echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
            }
            //query data record from today
            if ($row = $que_result->fetch_assoc()) {
                $newNum  = $num + $row['num'];
                $in_sql3 = "UPDATE new_cusproduct SET num=" . $newNum . ",timestamp='" . date('Y-m-d H:i:s') . "' WHERE id=" . $row['id'];
            } else {
                $in_sql3 = "INSERT INTO new_cusproduct(sn,num,year,month,day) VALUES({$sn},{$num},{$year},{$month},{$day})";
            }

            $in_result3 = $mysqli->query($in_sql3);
            if ($in_result3 === false) {
                $mysqli->rollback();
                $mysqli->autocommit(true);
                $mysqli->close();
                echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
            }

            //all successfully commit transaction
            $mysqli->commit();
            $mysqli->autocommit(true);
            $mysqli->close();
            echo json_encode(['code' => 0, 'msg' => 'Insert all successful']);

            /*//操作成功将对应的数量减去$num
        $newCount43 =$count43 - $num;
        $in_sql3 ="UPDATE cus_product SET count43={$newCount43} WHERE sn=".$sn;
        $in_result3 = $mysqli->query($in_sql3);
        if($in_result3 === false){
        $mysqli->rollback();
        $mysqli->autocommit(TRUE);
        $mysqli->close();
        exit(json_encode(['code'=>$mysqli->errno,'msg'=>$mysqli->error]));
        }*/
        }
    }

} else {
    $machines      = array();
    $last_31       = array();
    $last_31_index = array();
    for ($i = 0; $i < 31; $i++) {
        $last_31[$i] = 0;
        $this_day    = strtotime(date("Y-m-d")) - 86400 * $i;
        $month       = date("m", $this_day);
        $day         = date("d", $this_day);

        $last_31_index[$month * 31 + $day] = 30 - $i;
    }

    if (isset($_GET["wid"]) && !isset($_GET['sn'])) {
        $wid = $_GET["wid"];
        $sql = "SELECT machine.sn, machine.name, machine.barcode, response.param1 as mstatus, machine.timestamp as lt, CURRENT_TIMESTAMP() as ct, weixin.enable,machine.ver
				FROM machine, weixin, response WHERE machine.sn=weixin.sn AND wid=" . $wid . " AND response.sn=machine.sn AND response.cmd=71 and response.addr=0 ORDER BY machine.sn";

        $result = $mysqli->query($sql);
        if ($result === false) {
            echo $mysqli->error;
            echo $mysqli->errno;
        }
        $i = 0;

        while ($row = $result->fetch_assoc()) {
            $machine            = array();
            $machine['sn']      = $row["sn"];
            $machine['name']    = $row["name"];
            $machine['barcode'] = $row["barcode"];
            $machine['status']  = ($row["enable"] == 0) ? 10 : $row["mstatus"];
            $machine['ver']     = $row['ver'];
            if (empty($machine['name'])) {
                $machine['name'] = "";
            }

            $machines[$i++] = $machine;
        }

        $arr = array("machines" => $machines);
        echo json_encode($arr);
    } else if (isset($_GET["sn"])) {
        $sn = $_GET["sn"];

        $machine = array();
        $sql     = "SELECT m.sn,m.name,p.starttime,p.endtime,p.store,p.address,p.zone,m.ver,p.enable from machine m left join place p on p.sn=m.sn where m.sn='" . $sn . "'";
        $result  = $mysqli->query($sql);
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
            $machine['enable']    = $row['enable'];
        }

        $myProp = array(0 => 0, 1    => "", 2  => 0,
            10                => "", 11  => "", 12 => "", 13 => "", 14 => "", 15 => "", 16 => "", 17 => "", 18 => "", 19 => "",
            20                => "", 21  => "", 22 => "", 23 => "", 24 => "", 25 => "", 26 => "", 27 => "", 28 => "", 29 => "",
            30                => "", 31  => "", 32 => "", 33 => "", 34 => "", 35 => "", 36 => "",
            37                => 255, 38 => "", 39 => "", 40 => "", 41 => "");
        $myTime = array(0 => "", 1  => "", 2  => "",
            10                => "", 11 => "", 12 => "", 13 => "", 14 => "", 15 => "", 16 => "", 17 => "", 18 => "", 19 => "",
            20                => "", 21 => "", 22 => "", 23 => "", 24 => "", 25 => "", 26 => "", 27 => "", 28 => "", 29 => "",
            30                => "", 31 => "", 32 => "", 33 => "", 34 => "", 35 => "", 36 => "",
            37                => "", 38 => "", 39 => "", 40 => "", 41 => "");

        $sql = "SELECT id,sn,cmd,addr, param0, param1, param2,timestamp as lt,
				CURRENT_TIMESTAMP() as ct FROM response WHERE sn='" . $sn . "' AND cmd=71";

        $result = $mysqli->query($sql);
        if ($result === false) {
            echo $mysqli->error;
            echo $mysqli->errno;
        }
        while ($row = $result->fetch_assoc()) {
            $val = 0;
            if ($row['addr'] == 39) {
                $val = $row['param2'] * 65536 + $row['param0'] * 256 + $row['param1'];
            } else {
                $val = $row['param0'] * 256 + $row['param1'];
            }

            $myProp[$row['addr']] = $val;
            $myTime[$row['addr']] = $row['lt'];
        }

        if ($myProp[40] > 32767) {
            $myProp[40] = $myProp[40] - 65536;
        }

        if ($myProp[41] > 32767) {
            $myProp[41] = $myProp[41] - 65536;
        }

        //$sweety = 0;
        /*$sql = "SELECT count as ct FROM diary
        WHERE sn='".$sn."' AND year = YEAR(CURDATE()) and month = MONTH(CURDATE()) and day=DAY(CURDATE()) LIMIT 1";*/
        $year     = date('Y');
        $month    = date('n');
        $day      = date('j');
        $dateTime = date('Y-m-d H:i:s');
        if ($machine['enable'] == 1) {
            $sql = "SELECT SUM(count) count FROM so_diary WHERE sn={$sn} AND year={$year} AND month={$month} AND day={$day}";
        } else {
            $sql = "SELECT count FROM diary WHERE sn={$sn} AND year={$year} AND month={$month} AND day={$day}";
        }

        $result = $mysqli->query($sql);
        if ($result === false) {
            echo $mysqli->error;
            echo $mysqli->errno;
        } else {
            $sweety = 0;
            if ($row = $result->fetch_assoc()) {
                $sweety = $row['count'] ? $row['count'] : 0;
            }
        }

        $arr = array("sweety" => $sweety, "machine" => $machine, "props" => $myProp, "timestamps" => $myTime, "stat" => $last_31, 'dateTime' => $dateTime);
        echo json_encode($arr);
    }
    $mysqli->close();
}
