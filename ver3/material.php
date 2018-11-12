<?php
/**
 * @author wanglong <[12855057893@qq.com]>
 * Date   2018-06-28
 * Description  this page is material
 */
require_once './Api/signature_db.php';

$type = $_GET['type'];
$status =0;
$list =[];
switch ($type) {
    case 'query':
        //(1)query material table details
        $sql    = "SELECT*FROM material";
        $result = $mysqli->query($sql);
        if ($result === false) {
            $code = $mysqli->errno;
            $msg  = $mysqli->error;
            break;
        }
        while ($row = $result->fetch_assoc()) {
            $row['num']      = 0;
            $row['selected'] = false;
            $row['select']   = explode(',', $row['select']);
            $material[]      = $row;
        }

        if (!$material) {
            $code = -1;
            $msg  = 'query material is empty';
            break;
        }
        $code = 0;
        $msg  = 'query material is successful';
        break;
    case 'add':
        $telephone = $_GET['telephone'];
        $telName = $_GET['telName'];
        $courTime = $_GET['courTime'];
        $message = $_GET['message']? :'备注为空';
        $address = $_GET['address'];
        $machineName = $_GET['machineName'];
        $apply_date = date('Y-m-d H:i:s');
        $number =time();
        $sql ="INSERT INTO purchase_order(number,name,address,telephone,wid,status,courier,courier_telephone,store,apply_date,cour_date,message) 
        VALUES($number,'$telName','$address','$telephone',$wid,1,'待定','待定','$machineName','$apply_date','$courTime','$message')";
        $result = $mysqli->query($sql);
        if($result === false){
            $code = $mysqli->errno; $msg = $mysqli->error;
            break;
        }

        $lastId = $mysqli->insert_id;
        $material = json_decode($_GET['material'],true);
        $sqls ='';
        foreach($material as $key=>$val){
            $sqls .="INSERT INTO po_item(purchase_id,material_id,count) VALUES($lastId,".$val['id'].",".$val['num'].");";
        }
        $result =$mysqli->multi_query($sqls);
        if($result === false){
            $code =$mysqli->errno; $msg =$mysqli->error;
            break;
        }
        $code =0; $msg = 'add purchase_order is successful';
        break;
    case 'status':
        $sql = "SELECT status FROM purchase_order WHERE wid=$wid AND status<4 LIMIT 1";
        $result = $mysqli->query($sql);
        if($result === false){
            $code =$mysqli->errno; $msg =$mysqli->error;
            break;
        }
        if($row = $result->fetch_assoc()){
            $status = $row['status'];
        }
        $code = 0; $msg = 'successful';
        break;
    case 'list':
        $page = $_GET['page'];
        $start = $page * 20;
        $sql ="SELECT p.id,p.number,p.name,p.address,p.telephone,p.status,p.message,p.store,p.apply_date,p.cour_date,group_concat(m.id,'-',m.name,'-',m.unit,'-',i.count) detail 
        FROM purchase_order p,material m,po_item i WHERE p.id=i.purchase_id AND m.id=i.material_id AND p.wid=$wid AND i.count>0 GROUP BY p.id ORDER BY p.id DESC LIMIT $start,20";
        $result = $mysqli->query($sql);
        if($result === false){
            $code =$mysqli->errno; $msg=$mysqli->error;
            break;
        }
        while($row =$result->fetch_assoc()){
            $detail = explode(',',$row['detail']);
            sort($detail,SORT_NUMERIC);
            $row['detail'] =[];
            foreach ($detail as $key => $value) {
                $row['detail'][$key] =explode('-',$value);
            }
            $list[] =$row;
        }
        $code =0; $msg ='list is successful';
        break;
     case 'queryOne':
        $repid = $_GET['repid'];
        $sql ="SELECT p.id,p.number,p.name,p.address,p.telephone,p.status,p.message,p.store,p.apply_date,p.cour_date,group_concat(m.id,'=',m.name,'=',m.unit,'=',i.count,'=',m.img) detail 
        FROM purchase_order p,material m,po_item i WHERE p.id=i.purchase_id AND m.id=i.material_id AND p.id=$repid GROUP BY p.id ORDER BY p.id DESC";
        $result = $mysqli->query($sql);
        if($result === false){
            $code =$mysqli->errno; $msg=$mysqli->error;
            break;
        }
        while($row =$result->fetch_assoc()){
            $detail = explode(',',$row['detail']);
            sort($detail,SORT_NUMERIC);
            $row['detail'] =[];
            foreach ($detail as $key => $value) {
                $numArr =explode('=',$value);
                $assoc['id'] =$numArr[0];
                $assoc['name'] =$numArr[1];
                $assoc['unit'] =$numArr[2];
                $assoc['num'] =$numArr[3];
                $assoc['img'] =$numArr[4];
                $row['detail'][$key] =$assoc;
            }
            $material = $row['detail'];
            $list = $row;
        }
        $code =0; $msg ='query one is successful';
        break;
    case 'confirm':
        $sql ="UPDATE purchase_order SET status=4,finish_date='".date('Y-m-d H:i:s')."' WHERE id=".$_GET['repid'];
        $result = $mysqli->query($sql);
        if($result === false){
            $code =$mysqli->errno; $msg =$mysqli->error;
            break;
        }
        $code =0;
        $msg ='confirm is successful';
        break;
    case 'update':
        $repid = $_GET['repid'];
        $material = json_decode($_GET['material'],true);
        $date = date('Y-m-d H:i:s');
        $sql = "UPDATE purchase_order SET status=1,apply_date='$date' WHERE id=$repid;";
        foreach ($material as $key => $value) {
            $sql .="UPDATE po_item SET count=".$value['num']." WHERE purchase_id=$repid AND material_id=".$value['id'].";";
        }
        $result = $mysqli->multi_query($sql);
        if($result === false){
            $code = $mysqli->errno; $msg = $mysqli->error;
            break;
        }
        $code =0; $msg ='update successful';
        break;
    default:
        $code = 110; $msg = 'Sorry You Dont permision access fro me';
        break;
}

echo json_encode(array('code' => $code, 'msg' => $msg, 'material' => $material,'status'=>$status,'list'=>$list));

$mysqli->close();
