<?php
//防止直接运行
$_s = $_SERVER['PHP_SELF'];
$_s = strtolower(substr($_s,strripos($_s,"/")+1,-4));
if($_s=="output"){die();}
 
set_time_limit(0);
include '../db.php';
require './PHPExcel/Classes/PHPExcel.php';
$sql ="SELECT p.store 'name',d.count39 'bottom',d.count42 'middle',d.count43 'top',d.count 'total',concat(d.year,d.month,d.day) 'date' FROM diary d,place p WHERE d.sn=p.sn AND d.year=2018 AND d.month=8 
ORDER BY d.sn,d.day";
$result =$mysqli->query($sql);
if($result === false){
    exit(json_encode(['code'=>$mysqli->errno,'msg'=>$mysqli->error]));
}
while($row = $result->fetch_assoc()){
    $datas[] =$row;
}
$totalcount = count($datas);
//创建、设置Excel
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()
    ->setCreator("guohua.info")
    ->setLastModifiedBy("guohua.info")
    ->setTitle("PHPExcel Test Document")
    ->setSubject("PHPExcel Test Document")
    ->setDescription("Test document for PHPExcel, generated using PHP classes.")
    ->setKeywords("office PHPExcel php")
    ->setCategory("Test result file");
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
            ->setCellValue('A1', 'store')
            ->setCellValue('B1', 'bottom')
            ->setCellValue('C1', 'middle')
            ->setCellValue('D1', 'top')
            ->setCellValue('E1', 'total')
            ->setCellValue('F1', 'date');
//设置行高
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);
//设置填充的样式和背景色
$objPHPExcel->getActiveSheet()->getStyle("A1:F1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle("A1:F1")->getFill()->getStartColor()->setARGB('FF808080');
$objPHPExcel->getActiveSheet()->getStyle("A1:F1")->grtFont()->setSize(20)->setBold(true);
//对齐方式
$objPHPExcel->getActiveSheet()->getStyle("A1:F1")->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//水平方向上两端对齐
$objPHPExcel->getActiveSheet()->getStyle("A1:F1")->getAlignment()
    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直方向上中间居中
 
$index = 2;
foreach($datas as $k=>$v){
    //设置行高
    //$objPHPExcel->getActiveSheet()->getRowDimension($index)->setRowHeight(60);
    //对齐方式
    $objPHPExcel->getActiveSheet()->getStyle("A{$index}:F{$index}")->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//水平方向上两端对齐
    $objPHPExcel->getActiveSheet()->getStyle("A{$index}:F{$index}")->getAlignment()
        ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直方向上中间居中
 
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$index, $v['name']);
    $objPHPExcel->getActiveSheet()->setCellValue('B'.$index, $v['bottom']);
    $objPHPExcel->getActiveSheet()->setCellValue('C'.$index, $v['middle']);
 
    /*实例化excel图片处理类
    $objDrawing = new PHPExcel_Worksheet_Drawing();//每张图都需要实例化一个
    $objDrawing->setPath($v['img_url']);/*设置图片路径 切记：只能是本地图片，不能带http
    $objDrawing->setOffsetX(1);设置图片所在单元格的起始坐标
    $objDrawing->setOffsetY(1);
    $objDrawing->setWidth(60);设置图片宽高
    $objDrawing->setHeight(60);
    $objDrawing->setCoordinates("C$index");/*插入单元格
    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());//不可少*/
     
    $objPHPExcel->getActiveSheet()->setCellValue("D{$index}", $v['top']);
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
$objPHPExcel->getActiveSheet()->setTitle('20180803-online');
// 设置第一张表为活跃表
$objPHPExcel->setActiveSheetIndex(0);
//弹窗式保存
ob_end_clean();//清除缓冲区,避免乱码
header("Pragma: public");
header("Expires: 0");
header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
header("Content-Type:application/force-download");
header("Content-Type:application/vnd.ms-execl");
header("Content-Type:application/octet-stream");
header("Content-Type:application/download");;
header('Content-Disposition:attachment;filename="20180803-online.xlsx"');
header("Content-Transfer-Encoding:binary");
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
//$objWriter->save('shite.xlsx');//这种方式默认保存在php文件同一文件夹下
$objWriter->save('php://output');
