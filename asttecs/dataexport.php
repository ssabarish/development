<?php
header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: post-check=0, pre-check=0',false);
header('Pragma: no-cache');
session_cache_limiter('public, no-store');

session_set_cookie_params(0);
if (!session_id()) session_start();
setcookie('PHPSESSID', session_id());


if ($_SESSION['curuser']['usertype'] != 'admin' && $_SESSION['curuser']['usertype'] != 'groupadmin' && $_SESSION['curuser']['usertype'] != 'reseller') 
	header("Location: systemstatus.php");

require_once ("db_connect.php");
require_once ('include/astercrm.class.php');
//$sql = $_REQUEST['hidSql'];
$sql = $_SESSION['export_sql'];

if ($sql == '') exit;
	$result = mysql_query($sql);
	
	

while ($row = mysql_fetch_assoc($result)) 
{
$download[]= $row['filename'];
   
}
//print_r($download);


//mysql_free_result($result);

$file_names=$download;
$myFile = "zipVloger.zip";
unlink($myFile);

//$archive_file_name[]= null;
  $archive_file_name='zipVloger.zip';

  $file_path="/var/spool/asterisk/monitor/movedvoicefiles/";
 //echo $file_path;
  zipFilesAndDownload($file_names,$archive_file_name,$file_path);
 

function zipFilesAndDownload($file_names,$archive_file_name,$file_path)
{
  //create the object

     $zip = new ZipArchive();


  //create the file and throw the error if unsuccessful
  if ($zip->open($archive_file_name, ZIPARCHIVE::CREATE )!==TRUE)
  {
    exit("cannot open  the file\n");
  }
$i=0;
  //add each files of $file_name array to archive
  foreach($file_names as $files)
  {
   
 if($zip->addEmptyDir('newDirectory'.$i)) 
	{
        echo 'Created a new root directory';
 $zip->addFile($file_path.$files,$files);    
}
 $i=$i+1;
  }
  $zip->close();
//$myFile = "zipVloger.zip";
//unlink($myFile);
  //then send the headers to foce download the zip file
  header("Content-type: application/zip");
  header("Content-Disposition: attachment; filename=$archive_file_name");
  header("Pragma: no-cache");
  header("Expires: 0");
  readfile("$archive_file_name");
  exit;
}

















	/*
if ($_SESSION['curuser']['usertype'] != 'admin'){
	if($_SESSION['curuser']['usertype'] == 'groupadmin'){
		if (strpos(strtolower($sql),'where'))
			$sql .= " and groupid = ".$_SESSION['curuser']['groupid'];
		else
			$sql .= " where groupid = ".$_SESSION['curuser']['groupid'];
	}elseif($_SESSION['curuser']['usertype'] == 'reseller'){
		if (strpos(strtolower($sql),'where'))
			$sql .= " and resellerid = ".$_SESSION['curuser']['resellerid'];
		else
			$sql .= " where resellerid = ".$_SESSION['curuser']['resellerid'];
	}
}

*//*
ob_start();
header("charset=uft-8");   
header('Content-type:  application/force-download');
header('Content-Transfer-Encoding:  Binary');
header('Content-disposition:  attachment; filename=astercc.csv');
echo astercrm::exportDataToCSV($sql);
ob_end_flush();
unset($_SESSION['export_sql']);*/
?>