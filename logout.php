<?php
session_start();
$_SESSION['wid']='';
session_destroy();
header("Location: /input_login.php");
?>
