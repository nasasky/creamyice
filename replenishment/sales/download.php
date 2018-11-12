<?php
session_start();
if(!$_SESSION['wid'] || $_SESSION['wid'] != 39){
	header('HTTP/1.1 404 NotFound');
	exit;
}
if(isset($_GET['url']) && isset($_GET['token'])){

	$filePath = $_GET['url'];
	$token = $_GET['token'];
	if($token === $_SESSION['token']){
		if(!file_exists($filePath)){
            header("HTTP/1.0 404 Not Found");
            exit;
        }else{
        	//ob_end_clean();
            $file = @fopen($filePath,"r");
            if(!$file){
                header("HTTP/1.0 505 Internal server error");
                exit;
            }
            $fileName =substr($filePath,strrpos($filePath,'/')+1);

            header("Content-type: application/octet-stream");
            header("Accept-Ranges: bytes");
            header("Accept-Length: ".filesize($file));
            header("Content-Disposition: attachment; filename=" . $fileName);
            while(!feof($file)){
                echo fread($file,2048);
            }
            fclose($file);
            @unlink($filePath);
            $_SESSION['token'] =NULL;
            exit();
        }
	}else{
		echo 'token is not match';
	}
}else{
	echo 'parameter failuer';
	exit;
}