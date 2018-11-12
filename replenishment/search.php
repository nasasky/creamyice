<?php
if (isset($_GET['type'])) {
    require './db.php';
    $type = $_GET['type'];
    $code = 0;
    $msg  = 'successful';
    $list = [];
    switch ($type) {
        case 'number':
            $number = $_GET['number'];
            $sql    = "SELECT o.id,o.number,o.name,o.address,o.telephone,o.wid,o.status,o.courier,o.store,o.apply_date,o.confirm_date,o.cour_date,o.timestamp,o.message,GROUP_CONCAT(m.id,'=',m.name,'(',i.count,'/',m.unit,')') detail FROM purchase_order o,po_item i,material m WHERE o.id=i.purchase_id AND i.material_id=m.id AND o.number=$number GROUP BY o.id";
            $result = $mysqli->query($sql);
            if ($result === false) {
                $code = $mysqli->errno;
                $msg  = $mysqli->error;
                break;
            }
            if($row = $result->fetch_assoc()) {
                $detail = explode(',', $row['detail']);
                sort($detail, SORT_NUMERIC);
                $row['details'] = $detail;
                arr_reset($detail);
                $row['detail'] = implode(',', $detail);
                $list       = $row;
            }
            break;
        case 'date':
            $start = $_GET['start'];
            $end = $_GET['end'];
            if($start == $end){
                $sql    = "SELECT o.id,o.number,o.name,o.address,o.telephone,o.wid,o.status,o.courier,o.store,o.apply_date,o.confirm_date,o.cour_date,o.timestamp,o.message,GROUP_CONCAT(m.id,'=',m.name,'(',i.count,'/',m.unit,')') detail FROM purchase_order o,po_item i,material m WHERE o.id=i.purchase_id AND i.material_id=m.id AND o.cour_date='{$start}' GROUP BY o.id";
            }else{
                $sql    = "SELECT o.id,o.number,o.name,o.address,o.telephone,o.wid,o.status,o.courier,o.store,o.apply_date,o.confirm_date,o.cour_date,o.timestamp,o.message,GROUP_CONCAT(m.id,'=',m.name,'(',i.count,'/',m.unit,')') detail FROM purchase_order o,po_item i,material m WHERE o.id=i.purchase_id AND i.material_id=m.id AND o.cour_date BETWEEN '{$start}' AND '{$end}' GROUP BY o.id";
            }
            $result = $mysqli->query($sql);
            if($result === false){
                $code =$mysqli->errno;  $msg = $mysqli->error;
                break;
            }
            while($row =$result->fetch_assoc()){
                $detail = explode(',', $row['detail']);
                sort($detail, SORT_NUMERIC);
                $row['details'] = $detail;
                arr_reset($detail);
                $row['detail'] = implode(',', $detail);
                $list[]       = $row;
            }
            break;
        default:
            $code = 110;
            $msg  = 'You Dont Permission Access Me';
            break;
    }
} else {
    header('HTTP/1.1 404 NotFound ');
    exit;
}

echo json_encode(['code' => $code, 'msg' => $msg, 'list' => $list]);
$mysqli->close();

function arr_reset(&$arr)
{
    foreach ($arr as $key => $val) {
        $arr[$key] = mb_substr($val, strpos($val, '=') + 1);
    }
    return true;
}
