<?php
/**
 * @author [Wanglong] <[1285505793@qq.com]>
 * Date:   2018-06-27
 * Desc    This page is storer replenishment
 */
require_once './Api/signature_db.php';

//if have list is a value,it's query replenishment's lists
if (isset($_POST['list']) && !empty($_POST['list'])) {
    //(1)Pagunation
    $index = $_POST['index'];
    if ($index == 0 || $index == 1) {
        $limit = 0;
    } else {
        $limit = ($index - 1) * $size;
    }

    //计算总的记录数
    $co_sql       = "SELECT count(id) as count FROM purchase_order WHERE wid={$wid}";
    $count_result = $mysqli->query($co_sql);
    if ($count_result === false) {
        echo json_encode(['code' => -2, 'msg' => '查询总记录数出错']);
    } else {
        if ($row = $count_result->fetch_assoc()) {
            $count = (int) $row['count'];
        }
    }
    //分页传过来的索引量与总的记录数比较大小计算是否有这么多数据
    if ($count == 0) {
        exit(json_encode(['code' => -3, 'msg' => '亲，您还没有补货记录呦！']));
    }

    if ($index > ceil($count / $size)) {
        exit(json_encode(['code' => -4, 'msg' => '分页索引太大了，已经没有数据了！']));
    }

    //分页sql查询
    $sql    = "SELECT*FROM purchase_order WHERE wid=" . $wid . " order by id desc limit {$limit},{$size}";
    $result = $mysqli->query($sql);
    if ($result === false) {
        echo json_encode(array('code' => -1, 'message' => '查询数据库出错'));
    } else {
        $lists = array();
        while ($row = $result->fetch_assoc()) {
            $row['status']            = $row['status'] == 0 ? '补货中' : '已补货';
            $row['courier']           = empty($row['courier']) ? '待定' : $row['courier'];
            $row['courier_telephone'] = empty($row['courier_telephone']) ? '待定' : $row['courier_telephone'];
            $lists[]                  = $row;
        }
        if (isset($lists) && !empty($lists)) {
            echo json_encode(array('code' => 0, 'message' => 'ok', 'lists' => $lists));
        } else {
            echo json_encode(array('code' => 1, 'message' => '已经查看完所有的数据了'));
        }

    }
} elseif (isset($_POST['purchase_id']) && !empty($_POST['purchase_id'])) {
    //这是查询列表的详情内容
    $id     = $_POST['purchase_id'];
    $sql    = "SELECT po_item.count,po_item.price,po_item.amount,material.name,material.unit,material.index,material.SELECT FROM po_item,material WHERE po_item.purchase_id={$id} and po_item.material_id=material.id";
    $result = $mysqli->query($sql);
    if ($result === false) {
        echo json_encode(array('code' => -1, 'msg' => '查询数据库失败'));
    } else {
        $lists = array();
        while ($row = $result->fetch_assoc()) {
            $lists[] = $row;
        }
        if (isset($lists) && !empty($lists)) {
            echo json_encode(array('code' => 0, 'msg' => 'success', 'lists' => $lists));
        } else {
            echo json_encode(array('code' => 1, 'msg' => '还没有数据呦'));
        }

    }
} else {
    //接收补货新增post过来的数据进行新增操作
    $name    = $_POST['name'];
    $address = $_POST['address'];
    $mobile  = $_POST['mobile'];
    $company = $_POST['company'];
    $store   = $_POST['store'];
    $date    = $_POST['date'];
    $time    = time();

    //接收数组对象进行插入po_item表中操作
    $material     = $_POST['material']; //是一个json字符串格式，需要转换成数组
    $material     = json_decode($material, true);
    $total_amount = 0;
    foreach ($material as $key => $val) {
        $total_amount += $val['price'] * $val['select'][$val['index']];
        $material[$key]['total_price'] = $val['price'] * $val['select'][$val['index']];
    }
    //先进行插入purchase_order表操作获得插入自增id
    //$timestamp =date("Y-m-d H:i:s",time());
    $mysqli->autocommit(false); //关闭事物自动提交
    $sql    = "INSERT INTO purchase_order(name,address,telephone,company,amount,wid,timestamp,store,date) VALUES('{$name}','{$address}','{$mobile}','{$company}',{$total_amount},{$wid},now(),'{$store}','{$date}')";
    $result = $mysqli->query($sql);
    if ($result === false) {
        $mysqli->rollback();
        $mysqli->close();
        exit(json_encode(array('code' => -2, 'msg' => '新增失败', 'data' => '')));
    } else {
        //插入数据成功，获取自增id，在进行插入po_item操作
        $last_id = mysqli_INSERT_id($mysqli);
        foreach ($material as $key => $val) {
            $sql    = "INSERT INTO po_item(purchase_id,material_id,count,price,amount) VALUES({$last_id},{$val['id']},{$val['select'][$val['index']]},{$val['price']},{$val['total_price']})";
            $result = $mysqli->query($sql);
            if ($result === false) {
                //echo json_encode(array('code'=>-1,'msg'=>'补货失败','data'=>''));
                $mysqli->rollback();
                $mysqli->close();
                exit(json_encode(['code' => -1, 'msg' => '补货失败']));
            }
        }
        $mysqli->commit();
        echo json_encode(array('code' => 0, 'msg' => '补货成功', 'data' => ''));
    }
}
$mysqli->close();
