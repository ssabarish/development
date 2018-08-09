 <?php
 
$myserver="localhost";
$username="root";
$password="";
$database="voicelogger";
$db=mysql_connect($myserver,$username,$password) or die("mysql conncetion failed"); 
mysql_select_db($database) or die('Could not select database');

// require_once ("voicefile.config.php");

$file_path= '/var/spool/asterisk/monitorDONE/GSM/';
$targetpath='/var/spool/asterisk/monitor/movedvoicefiles/';

$d = dir($file_path) or die("Wrong path: $file_path");

while (false !== ($entry = $d->read()))
 {
if($entry != '.' && $entry != '..' )
$voicefiles[] = $entry;
}
$d->close();



foreach($voicefiles as $files )
{

$filename = explode("_",$files);//print_r($filename);
$datetime = explode("-",$filename['0']);	
$date1= $datetime['0']; 
$date=substr($date1, 0,4)."-".substr($date1,4,2)."-".substr($date1,6,2);

$year=substr($date1,0,4);
$month=substr($date1,4,2);
$day=substr($date1,6,2);
$monthName = date("F", mktime(0, 0, 0, $month, 10));

$time1=$datetime['1'];
$time=substr($time1, 0,2).":".substr($time1,2,2).":".substr($time1,4,2);
$destination=$filename['1'];


$sourse1=explode("-",$filename['2']);
$sourse2=explode(".",$sourse1['0']);
$sourse=$sourse2[0];
$extension= $sourse['0'];
$dur=wavDur($file_path.$files);
//echo $files."duration=".$dur."\n";
if($files !="")
{


if(is_dir($targetpath))
{
	$dir = opendir($targetpath);
	$targetpath1="/var/spool/asterisk/monitor/movedvoicefiles/".$year;
	if(is_dir($targetpath1))
	{
	}
	else
	{
		mkdir('/var/spool/asterisk/monitor/movedvoicefiles/'.$year);
		chmod("/var/spool/asterisk/monitor/movedvoicefiles/".$year, 0777);
	}
 	$targetpath12="/var/spool/asterisk/monitor/movedvoicefiles/".$year."/".$monthName;
	if(is_dir($targetpath12))	
	{
	}
	else
	{
	//creting month folder 
	mkdir("/var/spool/asterisk/monitor/movedvoicefiles/".$year."/".$monthName."/"); 
	chmod("/var/spool/asterisk/monitor/movedvoicefiles/".$year."/".$monthName."/", 0777);
	}
	$targetpath11="/var/spool/asterisk/monitor/movedvoicefiles/".$year."/".$monthName."/".$day;
			

		if(is_dir($targetpath11))
			{
			}
                else
			 {
			  	//creting date
				mkdir("/var/spool/asterisk/monitor/movedvoicefiles/".$year."/".$monthName."/".$day); 
				chmod("/var/spool/asterisk/monitor/movedvoicefiles/".$year."/".$monthName."/".$day, 0777);
			}
		if(is_dir($targetpath11))
		{	
			$query = "INSERT INTO voicefiles (date,time,sourse,destination,duration,filename) VALUES ('".$date."','".$time."','".$destination."','".$sourse."','".$dur."','".$files."')";
			mysql_query($query)or die("errrror");				
			$command = "mv $file_path$files $targetpath11";
			exec($command, $output, $return_var);

		}

}

}
}
function wavDur($file)
{

       $type=substr($file,-3);     
       if($type=="gsm")
       {
       $fp = fopen($file, 'r');
       $size_in_bytes = filesize($file);
       $sec = ceil($size_in_bytes/1650);
       $minutes = intval(($sec / 60) % 60);
       $seconds = intval($sec % 60);
       return str_pad($minutes,2,"0", STR_PAD_LEFT).":".str_pad($seconds,2,"0", STR_PAD_LEFT);
       }
       
       else
       {
      $fp = fopen($file, 'r');
       if (fread($fp,4) == "RIFF") 
              {
              fseek($fp, 20);
              $rawheader = fread($fp, 16);
              $header = unpack('vtype/vchannels/Vsamplerate/Vbytespersec/valignment/vbits',$rawheader);
              $pos = ftell($fp);
              while (fread($fp,4) != "data" && !feof($fp))
              {
                     $pos++;
                     fseek($fp,$pos);
              }
              $rawheader = fread($fp, 4);
              $data = unpack('Vdatasize',$rawheader);
              $sec = $data[datasize]/$header[bytespersec];
              $minutes = intval(($sec / 60) % 60);
              $seconds = intval($sec % 60);
              return str_pad($minutes,2,"0", STR_PAD_LEFT).":".str_pad($seconds,2,"0", STR_PAD_LEFT);
              }

       }
}      
	

?>
