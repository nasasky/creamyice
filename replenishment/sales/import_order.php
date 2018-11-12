<?php
require '../db.php';
$data = [];
$file = $downloadToken = '';
if (isset($_GET) && !empty($_GET)) {
    $type      = $_GET['type'];
    $dateStart = $_GET['dateStart'];
    $dateEnd   = $_GET['dateEnd'];

    if ($type == 'online') {
        $sql    = "SELECT sn,store FROM place ORDER BY sn";
        $result = $mysqli->query($sql);
        while ($row = $result->fetch_assoc()) {
            $sn[] = $row;
        }
    }
    if ($type == 'online') {
        //query online so_diary order and import excel
        $sql = "SELECT d.sn,p.store name,concat(d.year,'-',d.month,'-',d.day) date,SUM(d.count) total,GROUP_CONCAT(d.pro_id,'=',d.count) detail
        FROM place p,so_diary d WHERE p.sn=d.sn AND DATEDIFF(CONCAT(d.year,'-',d.month,'-',d.day),'{$dateStart}')>-1 AND DATEDIFF(CONCAT(d.year,'-',d.month,'-',d.day),'{$dateEnd}')<1
        GROUP BY d.sn,d.year,d.month,d.day ORDER BY d.sn,d.month,d.day";
    } else {
        //query offline diary order and import excel
        $sql = "SELECT p.store name,d.count39 bottom,d.count42 middle,d.count43 top,d.count total,concat(d.year,'-',d.month,'-',d.day) date FROM diary d,place p
        WHERE d.sn=p.sn AND DATEDIFF(CONCAT(d.year,'-',d.month,'-',d.day),'{$dateStart}')>-1 AND DATEDIFF(CONCAT(d.year,'-',d.month,'-',d.day),'{$dateEnd}')<1 ORDER BY d.sn,d.month,d.day";
    }
    $result = $mysqli->query($sql);
    if ($result === false) {
        $mysqli->close();
        exit('<script>alert("SERVER EXCEPTION PLESE TRY AGAIN LATER");history.back(-1);;</script>');
    } else {
        if($type == 'online'){
            $dates = create_date_order($dateStart, $dateEnd);
            $data = create_origin_data($sn,$dates);
        }
        while ($row = $result->fetch_assoc()) {
            if ($type == 'online') {
                $row[39] = 0;
                $row[42] = 0;
                $row[43] = 0;
                $arr     = explode(',', $row['detail']);
                foreach ($arr as $key => $value) {
                    $row[substr($value, 0, 2)] = substr($value, strpos($value, '=') + 1);
                }
                $row['date'] =date('Y-m-d',strtotime($row['date']));
                //$data[] = $row;
                foreach($data as $key=>&$val){
                    if($row['sn'] == $val['sn'] && $row['date'] == $val['date']){
                        $val = $row;
                        break;
                    }
                }
            }else{
                $row['date'] = date('Y-m-d', strtotime($row['date']));
                $data[]      = $row;
            }
        }
        $mysqli->close();
        if (!empty($data)) {
            set_time_limit(0);
            require './phpexcel/PHPExcel.php';
            //创建、设置Excel
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()
                ->setCreator("wanglong")
                ->setLastModifiedBy("wanglong")
                ->setTitle("order export")
                ->setSubject("order export")
                ->setDescription("order export description")
                ->setKeywords("office PHPExcel php")
                ->setCategory("order export");
            //设置活跃表
            $objPHPExcel->setActiveSheetIndex(0);
            //设置宽度
            /*$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(50);*/

            //设置默认行高
            //$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(10);

            //设置E列(电话)为文本格式
            //$objPHPExcel->getActiveSheet()->getStyle('E')->getNumberFormat()->setFormatCode("@");
            //->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

            //第一行
            //$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');//合并单元格
            //$objPHPExcel->getActiveSheet()->setCellValue('A1', "订单数据");
            //第二行
            if ($type == 'online') {
                $objPHPExcel->getActiveSheet()
                    ->setCellValue('A1', 'store')
                    ->setCellValue('B1', '郁金香雪吻')
                    ->setCellValue('C1', '格罗宁根黑松')
                    ->setCellValue('D1', '海牙圣杯')
                    ->setCellValue('E1', 'total')
                    ->setCellValue('F1', 'date');
            } else {
                $objPHPExcel->getActiveSheet()
                    ->setCellValue('A1', 'store')
                    ->setCellValue('B1', 'bottom')
                    ->setCellValue('C1', 'middle')
                    ->setCellValue('D1', 'top')
                    ->setCellValue('E1', 'total')
                    ->setCellValue('F1', 'date');
            }
            //设置行高
            $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(15);
            //设置填充的样式和背景色
            $objPHPExcel->getActiveSheet()->getStyle("A1:F1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle("A1:F1")->getFill()->getStartColor()->setARGB('FF808080');
            $objPHPExcel->getActiveSheet()->getStyle("A1:F1")->getFont()->setSize(10)->setBold(true);
            //对齐方式
            $objPHPExcel->getActiveSheet()->getStyle("A1:F1")->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //水平方向上两端对齐
            $objPHPExcel->getActiveSheet()->getStyle("A1:F1")->getAlignment()
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直方向上中间居中

            $index = 2;
            foreach ($data as $k => $v) {
                //设置行高
                //$objPHPExcel->getActiveSheet()->getRowDimension($index)->setRowHeight(60);
                //对齐方式
                $objPHPExcel->getActiveSheet()->getStyle("A{$index}:F{$index}")->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //水平方向上两端对齐
                $objPHPExcel->getActiveSheet()->getStyle("A{$index}:F{$index}")->getAlignment()
                    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直方向上中间居中

                $objPHPExcel->getActiveSheet()->setCellValue('A' . $index, $v['name']);
                if ($type == 'online') {
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $index, $v[39]);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $index, $v[42]);
                    $objPHPExcel->getActiveSheet()->setCellValue("D{$index}", $v[43]);
                } else {
                    $objPHPExcel->getActiveSheet()->setCellValue('B' . $index, $v['bottom']);
                    $objPHPExcel->getActiveSheet()->setCellValue('C' . $index, $v['middle']);
                    $objPHPExcel->getActiveSheet()->setCellValue("D{$index}", $v['top']);
                }
                /*实例化excel图片处理类
                $objDrawing = new PHPExcel_Worksheet_Drawing();//每张图都需要实例化一个
                $objDrawing->setPath($v['img_url']);/*设置图片路径 切记：只能是本地图片，不能带http
                $objDrawing->setOffsetX(1);设置图片所在单元格的起始坐标
                $objDrawing->setOffsetY(1);
                $objDrawing->setWidth(60);设置图片宽高
                $objDrawing->setHeight(60);
                $objDrawing->setCoordinates("C$index");/*插入单元格
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());//不可少*/

                $objPHPExcel->getActiveSheet()->setCellValue("E{$index}", $v['total']);
                $objPHPExcel->getActiveSheet()->setCellValue("F{$index}", $v['date']);
                /* $objPHPExcel->getActiveSheet()->setCellValue("G{$index}", $v['amount']);
                $time_print = intval($v['time_print'])<1?'':date('Y-m-d',$v['time_print']);
                $objPHPExcel->getActiveSheet()->setCellValue("H{$index}", $time_print);
                $time_fahuo0 =intval($v['time_fahuo0'])<1?'':date('Y-m-d',$v['time_fahuo0']);
                $objPHPExcel->getActiveSheet()->setCellValue("I{$index}", $time_fahuo0);
                $time_fahuo1 =intval($v['time_fahuo1'])<1?'':date('Y-m-d',$v['time_fahuo1']);
                $objPHPExcel->getActiveSheet()->setCellValue("J{$index}", $time_fahuo1);
                $objPHPExcel->getActiveSheet()->setCellValue("K{$index}", $v['express_no']);
                $objPHPExcel->getActiveSheet()->setCellValue("L{$index}", c_getexpress($v['express_id']));
                $objPHPExcel->getActiveSheet()->setCellValue("M{$index}", $v['price']);
                $objPHPExcel->getActiveSheet()->setCellValue("N{$index}", c_getpayed($v['status_pay']));
                $objPHPExcel->getActiveSheet()->setCellValue("O{$index}", $v['username']);
                $objPHPExcel->getActiveSheet()->setCellValue("P{$index}", $v['remark']);*/

                $index++;
            }
            // 表名称
            $objPHPExcel->getActiveSheet()->setTitle($dateStart . '--' . $dateEnd . '-' . $type);
            // 设置第一张表为活跃表
            $objPHPExcel->setActiveSheetIndex(0);
            //弹窗式保存
            ob_end_clean(); //清除缓冲区,避免乱码
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
            header("Content-Type:application/force-download");
            header("Content-Type:application/vnd.ms-execl");
            header("Content-Type:application/octet-stream");
            header("Content-Type:application/download");
            header('Content-Disposition:attachment;filename=' . $dateStart . '--' . $dateEnd . '-' . $type . '.xlsx');
            header("Content-Transfer-Encoding:binary");
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            //$file = __DIR__ .'/excelorders/'.$dateStart.'--'.$dateEnd.'-'.$type.'.xlsx';
            //$objWriter->save($file);//这种方式默认保存在php文件同一文件夹下
            $objWriter->save('php://output');
            exit;
        } else {
            exit('<script>alert("SORRY ORDER DATA IS EMPTY PLESE REINPUT");history.back(-1);</script>');
        }
    }
} else {
    $mysqli->close();
    header('HTTP/1.1 404 NotFound');
    exit;
}

//echo json_encode(['code' => $code, 'msg' => $msg, 'data' => $data,'file'=>$file,'token'=>$downloadToken]);

function create_origin_data($sn, $date)
{
    $data = [];
    foreach ($sn as $val) {
        foreach ($date as $k) {
            $data[] = ['sn' => $val['sn'],'name'=>$val['store'], 39 => 0,42=>0,43=>0,'total'=>0, 'date' => $k];
        }
    }
    return $data;
}

function create_date_order($start_date, $end_date)
{
    $start = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24) + 1;
    $date  = [$start_date];
    for ($i = 1; $i < $start; $i++) {
        $date[$i] = date('Y-m-d', strtotime($start_date) + $i * 60 * 60 * 24);
    }
    return $date;
}
