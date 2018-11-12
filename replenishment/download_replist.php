<?php
include 'db.php';
if (isset($_GET) && !empty($_GET)) {

    if (isset($_GET['page']) && isset($_GET['size'])) {
        $page  = $_GET['page'];
        $size  = $_GET['size'];
        $start = ($page - 1) * $size;
        $lists = [];
        $sql   = "SELECT o.id,o.number,o.name,o.address,o.telephone,o.wid,o.status,o.courier,o.store,o.apply_date,o.confirm_date,o.cour_date,o.timestamp,o.message,GROUP_CONCAT(m.name,'=',i.count) detail
    FROM purchase_order o,po_item i,material m WHERE o.id=i.purchase_id AND i.material_id=m.id GROUP BY o.id ORDER BY o.id DESC LIMIT $start,$size";
    } else if(isset($_GET['ids'])){
        $ids = trim($_GET['ids'],',');
        $sql   = "SELECT o.id,o.number,o.name,o.address,o.telephone,o.wid,o.status,o.courier,o.store,o.apply_date,o.confirm_date,o.cour_date,o.timestamp,o.message,GROUP_CONCAT(m.name,'=',i.count) detail
    FROM purchase_order o,po_item i,material m WHERE o.id=i.purchase_id AND i.material_id=m.id AND o.id in($ids) GROUP BY o.id ORDER BY o.id DESC";
    }
    $result = $mysqli->query($sql);
    if ($result === false) {
        $lists = '服务器正忙';
    } else {
        while ($row = $result->fetch_assoc()) {
            $arr = explode(',', $row['detail']);
            foreach ($arr as $key => $val) {
                $row[substr($val, 0, strpos($val, '='))] = substr($val, strpos($val, '=') + 1);
            }
            switch ($row['status']) {
                case '1':
                    $row['status'] = '订单申请';
                    break;
                case '2':
                    $row['status'] = '审核通过';
                    break;
                case '3':
                    $row['status'] = '正在配送';
                    break;
                case '4':
                    $row['status'] = '订单完成';
                    break;
                default:
                    $row['status'] = '订单异常';
                    break;
            }
            $lists[] = $row;
        }

        if ($lists) {
            set_time_limit(0);
            require './sales/phpexcel/PHPExcel.php';
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
            $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', '订单号')
                ->setCellValue('B1', '商店名称')
                ->setCellValue('C1', '商店地址')
                ->setCellValue('D1', '联系电话')
                ->setCellValue('E1', '订单状态')
                ->setCellValue('F1', '下单时间')
                ->setCellValue('G1', '配送日期')
                ->setCellValue('H1', '原味冰淇淋奶浆(箱装11KG)')
                ->setCellValue('I1', '原味甜筒(一箱340个)')
                ->setCellValue('J1', '竹炭味甜筒(一箱340个)')
                ->setCellValue('K1', '冰淇淋杯子(一箱200个含勺子)')
                ->setCellValue('L1', '另加勺子(100个一组)')
                ->setCellValue('M1', '备注信息');
            //设置行高
            $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(15);
            //设置填充的样式和背景色
            $objPHPExcel->getActiveSheet()->getStyle("A1:M1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $objPHPExcel->getActiveSheet()->getStyle("A1:M1")->getFill()->getStartColor()->setARGB('FF808080');
            $objPHPExcel->getActiveSheet()->getStyle("A1:M1")->getFont()->setSize(10)->setBold(true);
            //对齐方式
            $objPHPExcel->getActiveSheet()->getStyle("A1:M1")->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //水平方向上两端对齐
            $objPHPExcel->getActiveSheet()->getStyle("A1:M1")->getAlignment()
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直方向上中间居中

            $index = 2;
            foreach ($lists as $k => $v) {
                //设置行高
                //$objPHPExcel->getActiveSheet()->getRowDimension($index)->setRowHeight(60);
                //对齐方式
                $objPHPExcel->getActiveSheet()->getStyle("A{$index}:L{$index}")->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //水平方向上两端对齐
                $objPHPExcel->getActiveSheet()->getStyle("A{$index}:L{$index}")->getAlignment()
                    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直方向上中间居中

                $objPHPExcel->getActiveSheet()->setCellValue('A' . $index, $v['number']);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $index, $v['store']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $index, $v['address']);
                $objPHPExcel->getActiveSheet()->setCellValue("D{$index}", $v['telephone']);

                /*实例化excel图片处理类
                $objDrawing = new PHPExcel_Worksheet_Drawing();//每张图都需要实例化一个
                $objDrawing->setPath($v['img_url']);/*设置图片路径 切记：只能是本地图片，不能带http
                $objDrawing->setOffsetX(1);设置图片所在单元格的起始坐标
                $objDrawing->setOffsetY(1);
                $objDrawing->setWidth(60);设置图片宽高
                $objDrawing->setHeight(60);
                $objDrawing->setCoordinates("C$index");/*插入单元格
                $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());//不可少*/

                $objPHPExcel->getActiveSheet()->setCellValue("E{$index}", $v['status']);
                $objPHPExcel->getActiveSheet()->setCellValue("F{$index}", $v['apply_date']);
                $objPHPExcel->getActiveSheet()->setCellValue("G{$index}", $v['cour_date']);
                $objPHPExcel->getActiveSheet()->setCellValue("H{$index}", $v['原味冰淇淋奶浆']);
                $objPHPExcel->getActiveSheet()->setCellValue("I{$index}", $v['原味甜筒']);
                $objPHPExcel->getActiveSheet()->setCellValue("J{$index}", $v['竹炭味甜筒']);
                $objPHPExcel->getActiveSheet()->setCellValue("K{$index}", $v['冰淇淋杯子']);
                $objPHPExcel->getActiveSheet()->setCellValue("L{$index}", $v['另加勺子']);
                $objPHPExcel->getActiveSheet()->setCellValue("M{$index}", $v['message']);
                $index++;
            }
            $date = date('Y-m-d-H-i-s');
            // 表名称
            $objPHPExcel->getActiveSheet()->setTitle($date);
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
            header('Content-Disposition:attachment;filename=' . $date . '.xlsx');
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