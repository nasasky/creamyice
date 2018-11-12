<?php
/**
 * @author wanglong  <[1285505793@qq.com]>
 * Date  2018-06-28
 * Description     this page is user's comments details
 */
require_once './Api/signature_db.php';
$type = $_GET['type'] ? $_GET['type'] : 'query';
switch ($type) {
    //query comments details by order ID
    case 'queryOCDetail':
        $orid = (int) $_GET['orid'];
        $sql  = "SELECT c.order_id,c.content,c.img,c.time,c.name,c.sn,c.audit,group_concat(i.name) tagname FROM comments c,comments_selects s,comments_item i
        WHERE c.id=s.comments_id AND s.items_id=i.id AND c.order_id={$orid} GROUP BY s.comments_id LIMIT 1";
        $result = $mysqli->query($sql);
        if ($result === false) {
            echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
        }
        $data = array();
        if ($row = $result->fetch_assoc()) {
            $data = $row;
        }
        echo json_encode(['code' => 0, 'msg' => 'queryOCDetail is successful', 'comments' => $data]);
        break;
    //load comment items list
    case 'query':
        $dtype  = $_GET['dtype'] ?: 0;
        $sql    = "SELECT*FROM comments_item";
        $result = $mysqli->query($sql);
        if ($result === false) {
            echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
        }
        $ids            = array();
        $comments_items = array();
        while ($row = $result->fetch_assoc()) {
            $row['checked'] = $row['checked'] == 1 ? true : false;
            if ($row['checked'] == 1) {
                $ids[] = $row['id'];
            }

            $comments_items[] = $row;
        }
        //这里判断反回的数据类型
        if ($dtype == 1) {
            $sn = $_GET['sn'] ?: 0;
            //全部评论总的记录数
            $total_count = 0;
            if ($sn == 0) {
                $total_sql = "SELECT count(c.id) as counts FROM comments c WHERE c.user_id=" . $wid;
            } else {
                $total_sql = "SELECT count(c.id) as counts FROM comments c WHERE c.sn={$sn} AND c.audit=1";
            }

            $total_result = $mysqli->query($total_sql);
            if ($total_result === false) {
                echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
            }
            if ($total_row = $total_result->fetch_assoc()) {
                $total_count = (int) $total_row['counts'];
            }
            if ($total_count > 0) {
                //根据$comments_items数组里面的id值和传来的sn计算各种评论的总记录数
                foreach ($comments_items as $key => $val) {
                    if ($sn == 0) {
                        $sqls = "SELECT count(c.id) as counts FROM comments c,comments_selects cs WHERE c.id=cs.comments_id AND c.user_id={$wid} AND cs.items_id=" . $val['id'];
                    } else {
                        $sqls = "SELECT count(c.id) as counts FROM comments c,comments_selects cs WHERE c.id=cs.comments_id AND c.sn={$sn} AND c.audit=1 AND cs.items_id=" . $val['id'];
                    }

                    $res = $mysqli->query($sqls);
                    if ($res === false) {
                        echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
                    }
                    //将每种评论的记录数填入对应的数组中
                    if ($row = $res->fetch_assoc()) {
                        $comments_items[$key]['count'] = $row['counts'];
                    }
                }
            } else {
                foreach ($comments_items as $key => $val) {
                    $comments_items[$key]['count'] = 0;
                }
            }
            $array            = ['id' => 0, 'name' => '全部评论', 'checked' => 1, 'groups' => -1, 'count' => $total_count];
            $comments_items[] = $array;
            $comments_items   = array_reverse($comments_items);
            echo json_encode(['code' => 0, 'msg' => 'query comments_items AND return comments successful', 'items' => $comments_items]);
        } else {
            //重组从数据库取出的数据，方便小程序使用
            require_once './Api/function.php';
            if (!empty($comments_items) && is_array($comments_items)) {
                $new_comments_items = array_val_chunk($comments_items, 'groups');
            }
            echo json_encode(['code' => 0, 'msg' => 'query comments_items AND return newcomments successful', 'items' => $new_comments_items, 'ids' => $ids]);
        }
        break;
    //this is user add comment details
    case 'add':
        $ids_json      = $_GET['ids'];
        $ids           = json_decode($ids_json, true);
        $text          = addslashes(trim($_GET['text']));
        $sn            = $_GET['sn'] ?: 110;
        $orid          = (int) $_GET['orid'] ?: 0;
        $comments_name = $_GET['comments_name'] ?: '郁金香雪吻';
        //关闭事物自动提交
        $mysqli->autocommit(false);
        $sql    = "INSERT INTO comments(user_id,content,time,sn,order_id,name,audit) VALUES({$wid},'{$text}',CURRENT_TIMESTAMP(),{$sn},{$orid},'{$comments_name}',0)";
        $result = $mysqli->query($sql);
        if ($result === false) {
            $mysqli->rollback();
            echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
        }
        $last_id = mysqli_insert_id($mysqli);
        foreach ($ids as $key => $val) {
            $sqls    = "INSERT INTO comments_selects(comments_id,items_id) values({$last_id},{$val})";
            $results = $mysqli->query($sqls);
            if ($results === false) {
                $mysqli->rollback();
                echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
                break;
            }
        }
        //所有的评论插入操作都成功，更新订单状态status=4
        $up_sql    = "UPDATE sales_order SET status=4 WHERE id={$orid}";
        $up_result = $mysqli->query($up_sql);
        if ($up_result === false) {
            echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
        }

        $mysqli->commit();
        echo json_encode(['code' => 0, 'msg' => 'insert comments successful']);
        break;
    //query store comments by sn of one
    case 'selectone':
        $sn   = (int) $_GET['sn'] ?: '';
        $orid = (int) $_GET['orid'] ?: 0;
        if ($orid == 0) {
            $sql = "SELECT count(c.id) as totalcomments,u.name as username,c.name as productname,c.time,c.content FROM users u
	     	INNER  JOIN comments c on u.id=c.user_id  WHERE c.sn={$sn} AND c.audit=1 ORDER BY c.id DESC LIMIT 1";
        } else {
            $sql = "SELECT count(*) as totalcomments,content,name,sn,img,time FROM comments WHERE order_id={$orid}";
        }
        $result = $mysqli->query($sql);
        if ($result === false) {
            echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
        }
        if ($row = $result->fetch_assoc()) {
            if ($row['totalcomments'] == 0) {
                echo json_encode(['code' => -4, 'msg' => 'query comments details total is 0']);
            }
            $comment = $row;
            echo json_encode(['code' => 0, 'msg' => 'query comments detail successful', 'comment' => $comment]);
        } else {
            echo json_encode(['code' => -1, 'msg' => 'query comments detail is empty']);
        }
        break;
    //query all comments by wid or sn
    case 'selectall':
        $sn         = (int) $_GET['sn'] ?: 0;
        $selects_id = (int) $_GET['selects_id'] ?: 0;
        //获取分页码，计算LIMIT起始向量
        $page = (int) $_GET['page'] ?: 1;
        //三元运算
        ($page > 1) ? $start = ($page - 1) * SIZE : $start = 0;
        $sql                 = "";
        //如果selects_id为0是查询每个item类型的评论
        if ($selects_id == 0) {
            //根据sn的值作为判断 0：查询用户下面的评论 else：查询 机器下面所有的评论
            if ($sn == 0) {
                $sql = "SELECT u.nickname as username,c.name as productname,c.content as content,c.time as time FROM comments c,customer u WHERE c.user_id=u.id AND c.user_id={$wid} ORDER BY c.id DESC LIMIT {$start}," . SIZE;
            } else {
                $sql = "SELECT u.nickname as username,c.name as productname,c.content as content,c.time as time FROM comments c,customer u WHERE c.user_id=u.id AND c.sn={$sn} AND c.audit=1 ORDER BY c.id DESC LIMIT {$start}," . SIZE;
            }

        }
        //否则查询所有的评论类型
        else {
            //根据sn的值作为判断 0：查询用户下面的评论 else：查询 机器下面所有的评论
            if ($sn == 0) {
                $sql = "SELECT u.nickname as username,c.name as productname,c.content as content,c.time as time FROM comments c,customer u,
	         	comments_selects cs WHERE c.user_id=u.id AND cs.comments_id=c.id AND cs.items_id={$selects_id} AND c.user_id={$wid} ORDER BY c.id DESC LIMIT {$start}," . SIZE;
            } else {
                $sql = "SELECT u.nickname as username,c.name as productname,c.content as content,c.time as time FROM comments c,customer u,
	         	comments_selects cs WHERE c.user_id=u.id AND cs.comments_id=c.id AND cs.items_id={$selects_id} AND c.sn={$sn} AND c.audit=1 ORDER BY c.id DESC LIMIT {$start}," . SIZE;
            }
        }
        $result = $mysqli->query($sql);
        if ($result === false) {
            echo json_encode(['code' => $mysqli->errno, 'msg' => $mysqli->error]);
        }
        $comments = array();
        $count    = 0;
        while ($row = $result->fetch_assoc()) {
            $count++;
            $comments[] = $row;
        }
        if ($count == 0 && empty($comments)) {
            echo json_encode(['code' => -5, 'msg' => 'select all comments is empty']);
        }
        echo json_encode(['code' => 0, 'msg' => 'select all comments is successful', 'comments' => $comments]);
        break;
    default:
        echo json_encode(['code' => -3, 'msg' => 'type请求的数据不合法']);

}

$mysqli->close();
