<?php
include 'db.php';

$list = [];
$sql  = "SELECT p.sn,p.store,p.address,u.account FROM place p,weixin w,weixinusr u
    WHERE p.sn=w.sn AND w.wid=u.id GROUP BY u.id HAVING u.id NOT IN (30,39,43) ORDER BY p.sn";

$result = $mysqli->query($sql);
if ($result === false) {
    echo 'Mysql Select Error:' . $mysqli->error;
}
while ($row = $result->fetch_assoc()) {
	$row['password'] = $row['account'].'1234';
    $list[] = $row;
}
if ($list) {
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
    //设置宽高
    $objPHPExcel->getActiveSheet()
        ->setCellValue('A1', 'store')
        ->setCellValue('B1', 'sn')
        ->setCellValue('C1', 'address')
        ->setCellValue('D1', 'account')
        ->setCellValue('E1', 'password');
    //设置行高
    $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(15);
    //设置填充的样式和背景色
    $objPHPExcel->getActiveSheet()->getStyle("A1:E1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $objPHPExcel->getActiveSheet()->getStyle("A1:E1")->getFill()->getStartColor()->setARGB('FF808080');
    $objPHPExcel->getActiveSheet()->getStyle("A1:E1")->getFont()->setSize(10)->setBold(true);
    //对齐方式
    $objPHPExcel->getActiveSheet()->getStyle("A1:E1")->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //水平方向上两端对齐
    $objPHPExcel->getActiveSheet()->getStyle("A1:E1")->getAlignment()
        ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直方向上中间居中

    $index = 2;
    foreach ($list as $k => $v) {
        //设置行高
        //$objPHPExcel->getActiveSheet()->getRowDimension($index)->setRowHeight(60);
        //对齐方式
        $objPHPExcel->getActiveSheet()->getStyle("A{$index}:E{$index}")->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //水平方向上两端对齐
        $objPHPExcel->getActiveSheet()->getStyle("A{$index}:E{$index}")->getAlignment()
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER); //垂直方向上中间居中

        $objPHPExcel->getActiveSheet()->setCellValue('A' . $index, $v['store']);
        $objPHPExcel->getActiveSheet()->setCellValue('B' . $index, $v['sn']);
        $objPHPExcel->getActiveSheet()->setCellValue('C' . $index, $v['address']);
        $objPHPExcel->getActiveSheet()->setCellValue("D{$index}", $v['account']);
        $objPHPExcel->getActiveSheet()->setCellValue("E{$index}", $v['password']);

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
    exit('<script>alert("SORRY ACCOUNT IS EMPTY PLESE REINPUT");history.back(-1);</script>');
}
