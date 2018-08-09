<?php

$zip_name=$_REQUEST['f'];

//$zip_name='download.zip';
//$file_name = $_POST['zipname'];
//$file_url = 'C:/wamp/www/PPInt'. $file_name;
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary"); 
header("Content-disposition: attachment; filename=\"".$zip_name."\""); 
readfile($zip_name);

unlink($zip_name);
//header('Location: backup.php');
?>