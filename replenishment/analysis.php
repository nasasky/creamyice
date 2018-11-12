<?php
require 'db.php';
error_log(E_ALL);
$code      = 110;
$msg       = 'Sorry You Dont Permision Access To me';
$analysis  = [];
$proCounts = [
  ['pro_id'=>39,'avg'=>0,'next'=>0,'total'=>0,'sums'=>0],
  ['pro_id'=>42,'avg'=>0,'next'=>0,'total'=>0,'sums'=>0],
  ['pro_id'=>43,'avg'=>0,'next'=>0,'total'=>0,'sums'=>0]
];
$milk =0;
$wid       = $_GET['wid'];
$next      = $_GET['next'];
if ($wid) {
    $sql    = "SELECT o.id,o.number,o.name,o.address,o.telephone,o.wid,o.status,o.courier,o.store,o.apply_date,o.confirm_date,o.cour_date,o.timestamp,GROUP_CONCAT(m.id,'=',m.name,'(',i.count,'/',m.unit,')') detail FROM purchase_order o,po_item i,material m WHERE o.id=i.purchase_id AND i.material_id=m.id AND o.wid=$wid AND o.status=4 GROUP BY o.id ORDER BY o.id DESC LIMIT 1";
    $result = $mysqli->query($sql);
    $code   = -1;
    $msg    = 'The result is empty';
    if ($result === false) {
        $code = -2;
        $msg  = 'The server is to be busy';
    }
    if ($row = $result->fetch_assoc()) {
        $detail = explode(',', $row['detail']);
        sort($detail, SORT_NUMERIC);
        arr_reset($detail);
        $row['detail'] = implode(',', $detail);
        $analysis      = $row;
        $cour_date = $row['cour_date'];
        //query so_diary and analysis check the material uselly detail
        $sql    = 'SELECT sn FROM weixin WHERE wid=' . $wid;
        $result = $mysqli->query($sql);
        if ($result === false) {
            $code = -2;
            $msg  = 'The server is to be busy';
        } else {
            if ($row = $result->fetch_assoc()) {
                $sn           = $row['sn'];
                $diary_sql    = "SELECT pro_id,sum(count) sums FROM so_diary WHERE sn=$sn AND DATEDIFF(CONCAT(year,'-',month,'-',day),'{$cour_date}')>=0 GROUP BY pro_id ORDER BY pro_id";
                $diary_result = $mysqli->query($diary_sql);
                if ($diary_result === false) {
                    $code = -2;
                    $msg  = 'The server is to be busy';
                }else{
                    $now = date('Y-m-d');
                    $day     = (strtotime($now) - strtotime($cour_date)) / (3600*24) + 1;
                    //exit(json_encode(['code'=>$day]));
                    $nextDay = (strtotime($next) - strtotime($now)) / (3600*24);
                    $milk =0;
                    while ($row = $diary_result->fetch_assoc()) {
                        $row['avg']   = $row['sums'] / $day;
                        $row['next']  = round( $row['avg'] * $nextDay );
                        $row['total'] = $row['next'] + $row['sums'];
                        if($row['pro_id'] == 39){                       
                            $proCounts[0]  = $row;                          
                        }else if($row['pro_id'] == 42){
                            $proCounts[1]  = $row;                          
                        }else if($row['pro_id'] == 43){
                            $proCounts[2]  = $row;                          
                        }                       
                        $milk +=$row['total'];
                    }
                    $milk = round( $milk / 90 , 3 );
                    $code = 0;
                    $msg  = 'successful';
                }
                
            }
        }
    }
}
$mysqli->close();
exit(json_encode(['code' => $code, 'msg' => $msg, 'analysis' => $analysis, 'proCounts' => $proCounts,'milk'=>$milk]));

function arr_reset(&$arr)
{
    foreach ($arr as $key => $val) {
        $arr[$key] = mb_substr($val, strpos($val, '=') + 1);
    }
    return true;
}
