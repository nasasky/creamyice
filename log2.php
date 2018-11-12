<?php
include './db.php';

@$sn = $_GET["sn"];
@$month = $_GET["m"];
@$year = $_GET["y"];

if(!empty($sn) && !empty($month) && !empty($year))
{
$lastmonth = $month-1;
$lastmonthyear = $year;
if($lastmonth<1) {$lastmonth=12; $lastmonthyear = $year-1;}
$lastMax = 0;
$sql = "SELECT MAX(param2*65535+param0*256+param1) as ct FROM LOG 
WHERE MONTH(TIMESTAMP)=".$lastmonth." AND YEAR(TIMESTAMP)=".$lastmonthyear."
AND sn = '".$sn."' AND addr=39";
echo $sql;
$result = $mysqli->query($sql);
if($result === false){//执行失败
    echo $mysqli->error;
    echo $mysqli->errno;
}
else {
  if($row = $result->fetch_assoc()){
    $lastMax = $row['ct'];
  }
}
echo $lastMax;

$sql = "SELECT MAX(param2*65535+param0*256+param1) AS ct, DATE(TIMESTAMP) AS dt
FROM
(SELECT * FROM LOG WHERE MONTH(TIMESTAMP)=".$month." AND YEAR(TIMESTAMP)=".$year.") AS monthlog
WHERE sn = '".$sn."' AND addr=39 GROUP BY DATE(TIMESTAMP) ORDER BY dt ASC";

$result = $mysqli->query($sql);
if($result === false){//执行失败
    echo $mysqli->error;
    echo $mysqli->errno;
}
function get_days_by_year($year, $month){
    //首先判断闰年
    if($year%400 == 0  || ($year%4 == 0 && $year%100 !== 0)){
        $rday = 29;
    }else{
        $rday = 28;
    }
    $i = $month;
    if($i==2){
        $days = $rday;
    }else{
        //判断是大月（31），还是小月（30）
        $days = (($i - 1)%7%2) ? 30 : 31;
    }
    return $days;
}

$last_31 = array();
for($i=0; $i<31; $i++)
{
	$last_31[$i] = 0;
}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Creamy Ice !!</title>
<head>
<style type="text/css">
body {font: normal 300% Helvetica, Arial, sans-serif;}
span {margin-right:20px;}
span.red {color:red}
</style>
<body>
<div>Creamy Ice Log for <?=$sn?></div>

<div> <span><a href="index.php">Home</a></span><span><a href="machine.php?sn=<?=$sn?>">Status</a></span><span><a href="setting.php?sn=<?=$sn?>">Setting</a></span></div>

<div> Log </div>
<ul>
<?php
$i=1;
while($row = $result->fetch_assoc()){
  $startdate = strtotime($row['dt']);
  $day = date("d", $startdate);
  $last_31[$day-1] = $row['ct']-$lastMax;
  $lastMax = $row['ct'];
?>
<li><?=$i++?>  <?=$row['ct']?> <span><?=$row['dt']?></span></li>
<?php
}
?>
</ul>
<?php
$days = get_days_by_year($year, $month);
for($i=0; $i<$days; $i++)
{
  echo '<div>'.$year.'-'.$month.'-'.($i+1).':'.$last_31[$i].'</div>';
}

 ?>

</body>
</html>

<?php
}
$mysqli->close();
?>
