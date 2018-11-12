<?php
/**
 * Author: wanglong
 * Date:2018-06-28
 * Description: this is store orders page
 */
require_once './Api/signature_db.php';

$type = $_GET['type'];
$date = $tabs = '';
$orders = [];
switch ($type) {
    case 'confirm2':
        $orid = $_GET['orid'];
        $sql    = "UPDATE sales_order SET status=3,shiptime='" . date('Y-m-d H:i:s') . "' WHERE id={$orid}";
        $result = $mysqli->query($sql);
        if ($result === false) {
            $code = $mysqli->errno;
            $msg = $mysqli->error;
            break;
        }
        //(3)Insert command table execute machine
        $sn     = $_GET['sn'];
        $detail = json_decode($_GET['detail'], true);
        $param0 = $param1 = $param2 = 0;  
        foreach ($detail as $key => $val) {
            /*if ($val[2] == 39) {
                $param1 += $val[1];
            } elseif ($val[2] == 42) {
                $param2 += $val[1];
            } else {
                $param0 += $val[1];
            }*/
            $param0 += $val[1];
        }
        $in_sql    = "INSERT INTO command(sn, cmd, addr, param0, param1, param2) VALUES(" . $sn . ", 83,46," . $param0 . "," . $param1 . "," . $param2 . ")";
        $in_result = $mysqli->query($in_sql);
        if ($in_result === false) {
            $code = $mysqli->errno;
            $msg = $mysqli->error;
            break;
        }
        //(4)Insert command_history table record history
        $in_sql2    = "INSERT INTO command_history(sn, cmd, addr, param0, param1, param2, source) VALUES(" . $sn . ", 83,46," . $param0 . "," . $param1 . "," . $param2 . ",'wx-cre')";
        $in_result2 = $mysqli->query($in_sql2);
        if ($in_result2 === false) {
           $code = $mysqli->errno;
           $msg = $mysqli->error;
           break;
        }
        $code = 0;
        $msg = 'confirm is successful';
        break;
    
    case 'get':
        $page    = (int) $_GET['page'];
        $curPage = ($page - 1) > 0 ? ($page - 1) * SIZE : 0;
        $date    = date('Y-m-d H:i:s', SHIPTIME);
        $sql     = "SELECT n.number,o.sn,n.date AS ndate,o.id,o.paytime,o.total_count,o.status,pc.store,group_concat(p.name,'=',i.count,'=',p.addr) detail   FROM sales_order_number n,sales_order o,so_item i,product p,place pc WHERE n.orid=o.id AND o.id=i.sales_id AND i.product_id=p.id AND o.sn=pc.sn AND o.status>1 AND n.date='" . date('Y-m-d') . "' AND o.shiptime>'{$date}' AND o.sn in( SELECT sn FROM weixin WHERE wid={$wid} ) GROUP BY o.id ORDER BY o.status ASC,o.id DESC LIMIT {$curPage}," . SIZE;
        $result = $mysqli->query($sql);
        if ($result === false) {
           $code = $mysqli->errno;
           $msg = $mysqli->error;
           break;
        }
        while ($row = $result->fetch_assoc()) {
            $detail = explode(',', $row['detail']);
            foreach ($detail as $key => $val) {
                $row['details'][$key] = explode('=', $val);
            }
            $orders[] = $row;
        }
        if (empty($orders)) {
            $code = -1;
            $msg = 'query numbers is empty';
            break;
        }
        $code = 0;
        $msg = 'query orders is successful';
        break;

    case 'query':
        $number = (int) $_GET['number'];
        $sql    = "SELECT n.number,o.sn,n.date AS ndate,o.id,o.paytime,o.total_count,o.status,p.name,pc.store,group_concat(p.name,'=',i.count,'=',p.addr) detail   FROM sales_order_number n,sales_order o,so_item i,product p,place pc WHERE n.orid=o.id AND o.id=i.sales_id AND i.product_id=p.id AND o.sn=pc.sn AND n.date=curdate() AND o.status>1 AND n.number={$number} AND o.sn in( SELECT sn FROM weixin WHERE wid={$wid} ) GROUP BY o.id";

        $result = $mysqli->query($sql);
        if ($result === false) {
            $code = $mysqli->errno;
            $msg = $mysqli->error;
            break;
        }
        while ($row = $result->fetch_assoc()) {
            $detail = explode(',', $row['detail']);
            foreach ($detail as $key => $val) {
                $row['details'][$key] = explode('=', $val);
            }
            $orders[] = $row;
        }
        if (empty($orders)) {
           $code = -1;
           $msg = 'query numbers is empty';
           break;
        }
        $code = 0;
        $msg = 'query numbers is successful';
        break;

    case 'getMy':
        $date      = date('Y-m-d');
        $tabs      = [0 => '已领取(0)', 1 => '未领取(0)', 2 => '已过期(0)'];
        $count_sql = "SELECT COUNT(n.orid) count,o.status,n.date FROM sales_order_number AS n,sales_order AS o,place AS pc
        WHERE n.orid=o.id AND o.sn=pc.sn AND o.status>2 AND o.sn IN (SELECT sn FROM weixin WHERE wid={$wid})
        UNION ALL SELECT COUNT(n.orid) count,o.status,n.date FROM sales_order_number AS n,sales_order AS o,place AS pc
        WHERE n.orid=o.id AND o.sn=pc.sn AND o.status=2 AND n.date='{$date}' AND o.sn IN (SELECT sn FROM weixin WHERE wid={$wid})
        UNION ALL  SELECT COUNT(n.orid) count,o.status,n.date FROM sales_order_number AS n,sales_order AS o,place AS pc
        WHERE n.orid=o.id AND o.sn=pc.sn AND o.status=2 AND n.date<'{$date}' AND o.sn IN (SELECT sn FROM weixin WHERE wid={$wid})";
        $count_result = $mysqli->query($count_sql);
        if ($count_result === false) {
            $code = $mysqli->errno;
            $msg = $mysqli->error;
            break;
        }
        $count = 0;
        while ($row = $count_result->fetch_assoc()) {
            if ($row['status'] == 2) {
                if ($row['date'] == $date) {
                    $tabs[1] = '未领取(' . $row['count'] . ')';
                } else {
                    $tabs[2] = '已过期(' . $row['count'] . ')';
                }

            }
            if ($row['status'] > 2) {
                $count += $row['count'];
                $tabs[0] = '已领取(' . $count . ')';}
        }
        /*
         * @status for order tables
         *  1:non-payment  2:have paid  3:Already received  4:Already comment
         */
        $page    = (int) $_GET['page'];
        $curPage = ($page - 1) > 0 ? ($page - 1) * SIZE : 0;
        $status  = (int) $_GET['status'];
        /**
         * [$status description]
         * @status  index
         * 0:Already Received       1:have paid and non-received       2:Already expired
         */
        if ($status == 0) {
            $sql = "SELECT n.number,o.sn,n.date AS ndate,o.id,o.paytime,o.total_count,o.status,p.name,pc.store,group_concat(p.name,'=',i.count,'=',p.addr) detail   FROM sales_order_number n,sales_order o,so_item i,product p,place pc
        WHERE n.orid=o.id AND o.id=i.sales_id AND i.product_id=p.id AND o.sn=pc.sn AND o.status>2 AND o.sn in( SELECT sn FROM weixin WHERE wid={$wid} ) GROUP BY o.id ORDER BY o.paytime DESC LIMIT {$curPage}," . SIZE;
        }
        else if ($status == 1) {
            $sql = "SELECT n.number,o.sn,n.date AS ndate,o.id,o.paytime,o.total_count,o.status,p.name,pc.store,group_concat(p.name,'=',i.count,'=',p.addr) detail   FROM sales_order_number n,sales_order o,so_item i,product p,place pc
      WHERE n.orid=o.id AND o.id=i.sales_id AND i.product_id=p.id AND o.sn=pc.sn AND o.status=2 AND n.date='{$date}' AND o.sn in( SELECT sn FROM weixin WHERE wid={$wid} ) GROUP BY o.id LIMIT {$curPage}," . SIZE;
        }
        else if ($status == 2) {
            $sql = "SELECT n.number,o.sn,n.date AS ndate,o.id,o.paytime,o.total_count,o.status,p.name,pc.store,group_concat(p.name,'=',i.count,'=',p.addr) detail FROM sales_order_number n,sales_order o,so_item i,product p,place pc  WHERE n.orid=o.id AND o.id=i.sales_id AND i.product_id=p.id AND o.sn=pc.sn AND o.status=2 AND n.date<'{$date}' AND o.sn in( SELECT sn FROM weixin WHERE wid={$wid} ) GROUP BY o.id LIMIT {$curPage}," . SIZE;
        }
        $result = $mysqli->query($sql);
        if ($result === false) {
            $code = -1;
            $msg = $mysqli->error;
            break;
        }
        while ($row = $result->fetch_assoc()) {
            $detail = explode(',', $row['detail']);
            foreach ($detail as $key => $val) {
                $row['details'][$key] = explode('=', $val);
            }
            $orders[] = $row;
        }
        if (empty($orders)) {
           $code  = -1;
           $msg = 'query numbers is empty';
           break;
        }
        $code = 0;
        $msg = 'query orders is successful';
        break;

    case 'confirm':
        $orid = (int) $_GET['orid'];
        //Update order state by orid
        $sql    = "UPDATE sales_order SET status=3 WHERE id={$orid}";
        $result = $mysqli->query($sql);
        if ($result === false) {
            $code = $mysqli->errno;
            $msg = $mysqli->error;
            break;
        }
        $code = 0;
        $msg = 'confirm is successful';
        break;

    default:
        $code =250;
        $msg = 'Sorry You Dont permetion access the page';
        break;
}

echo json_encode(['code'=>$code,'msg'=>$msg,'numbers' => $orders,'tabs' => $tabs,'date' => $date]);
$mysqli->close();