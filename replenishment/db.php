<?php
date_default_timezone_set('PRC');
session_start();
error_reporting(E_ALL);
$mysqli =new mysqli('localhost','wanglong','wl940207','ice');

if($mysqli->connect_errno){
	exit('Mysql Connect Error'.$mysqli->connect_error);
}

if(!$mysqli->set_charset('utf8')){
	exit('Mysql Set Charset Error'.$mysqli->error);
}
$wid = $_SESSION['wid'];
if(empty($wid) || ($wid != 700 && $wid != 39))
{
  header("Location: /input_login.php");
}

/*function encrypt_url($url){
  return base64_encode(rawurlencode($url));
}
function decrypt_url($crypt_str){
  $decrypt_url = rawurldecode(base64_decode($crypt_str));
  return $decrypt_url; 
}

function get_param(){
	$query_str = $_SERVER['QUERY_STRING'];
	$get_param =[];
	if($query_str){
		return decrypt_url($query_str,'wanglong');
		parse_str($decrypt_url,$get_param);
	}
	return $get_param;
}*/

function query_encode($str,$key='wanglong')
{
	$key =md5($key);
	return urlencode(base64_encode($key.$str.$key));
}
function query_decode($str,$key='wanglong')
{
	$key = md5($key);
	return str_replace( $key , '', base64_decode(urldecode(rawurldecode($str))));
}
function rebuild_GET()
{//重写$_GET全局变量
  $_GET = array();
  $s_query = $_SERVER['QUERY_STRING'];
   if(strlen($s_query)==0)
   {
     return;
   }
   else
   {
    $s_tem = query_decode($s_query);
    $a_tem = explode('&', $s_tem);
     foreach($a_tem as $val)
     {
      $tem = explode('=', $val);
      $_GET[$tem[0]] = $tem[1];
     }
   }
}
