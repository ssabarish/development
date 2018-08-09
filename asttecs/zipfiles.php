
<?php
unlink("Voicefiles.zip");
 $start_date = $_POST['sdate'];
 $end_date =$_POST['tdate'];


$total_days = round(abs(strtotime($end_date) - strtotime($start_date)) / 86400, 0) + 1;

if ($end_date >= $start_date)
 {
  	for ($day = 0; $day < $total_days; $day++)
  	{
    	$dates[]= date("Y-m-d", strtotime("{$start_date} + {$day} days"));
  	
	}
}



foreach($dates as $date)
{
$d=explode("-",$date);
$monthName = date("F", mktime(0, 0, 0, $d[1], 10)); 
$file_path1="/var/spool/asterisk/monitor/movedvoicefiles/".$d[0]."/".$monthName."/".$d[2]; 
if(is_dir($file_path1))
{

shell_exec("zip -r Voicefiles voicefiles/".$d[0]."/".$monthName."/".$d[2]);
//sleep(1);
$sucess=1;


}

}//$zip_name="voicefiles.zip";
 if($sucess=='1')
	{
	header('Location: backup.php?value=1');
	}
	else
	{
	header('Location: backup.php?value=0');
	}


?>