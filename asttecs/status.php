<?php
/*******************************************************************************
********************************************************************************/

require_once('status.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
	<title>Status</title>
		<?php $xajax->printJavascript('include/'); ?>
		<meta http-equiv="Content-Language" content="utf-8" />
		<SCRIPT LANGUAGE="JavaScript">
		<!--

			function init(){
				xajax_init();
				dragresize.apply(document);
				xajax_playmonitor(1);
			}

      function searchFormSubmit(numRows,limit,id,type){
			//alert(xajax.getFormValues("searchForm"));
			xajax_searchFormSubmit(xajax.getFormValues("searchForm"),numRows,limit,id,type);
			return false;
		}

		function  addOption(objId,optionVal,optionText)  {
			objSelect = document.getElementById(objId);
			var _o = document.createElement("OPTION");
			_o.text = optionText;
			_o.value = optionVal;
			objSelect.options.add(_o);
		} 

		function setGroup(){
			var resellerid = xajax.$('resellerid').value;
			if (resellerid == ''){
				document.getElementById("groupid").options.length = 1;
				return;
			}
			//清空campaignid
			document.getElementById("groupid").options.length = 1;
			if (resellerid != 0)
				xajax_setGroup(resellerid);
		}
		function shortcutUpdateSave(mid){
			newRate = xajax.$(mid+'-mrateinitial').value;
			if(mid > 0) xajax_shortcutUpdateSave(mid,newRate);
			return false;
		}

		//-->
		</SCRIPT>
		

		<script type="text/javascript" src="js/dragresize.js"></script>
		<script type="text/javascript" src="js/dragresizeInit.js"></script>
		<script type="text/javascript" src="js/astercrm.js"></script>
		<script type="text/javascript" src="js/common.js"></script>
		<script type="text/javascript" src="//code.jquery.com/jquery-1.9.1.js"></script>

		<LINK href="skin/default/css/style.css" type=text/css rel=stylesheet>
		<LINK href="skin/default/css/dragresize.css" type=text/css rel=stylesheet>
		<style type="text/css">
		.space
		{	font-family:Verdana;
			color:#006699;
			font-size:14px;
			margin-left:10px;
		}
		.space1
		{	font-family:Arial Black;

			font-size:18px;
			margin-left:10px;
			font-weight:bold;
		}
		.space2
		{	font-family:Arial Black;

			font-size:18px;
			margin-left:10px;
			font-weight:bold;
		}
		.space3
		{
			font-size:18px;
			margin-left:10px;
			font-weight:bold;
			color:red;
		}
		.space4
		{
			font-size:14px;
			margin-left:10px;
			
		}
		.space5
		{
			font-family:Arial Black;
			font-size:14px;
			margin-left:10px;
			font-weight:bold;
			color:darkgreen;
		}

		</style>

	</head>
	<body onload="init();" id="report">
	
		<div style="border:1px solid;"><div id="divNav"></div><br>
		<table  width="100%" border="0" style="background: #F9F9F9; padding: 1px;">
		<td style="padding: 0px;">
		<?php
		$r= shell_exec('df -h');//echo $r; exit;
		$ty=explode("G",$r);
		$use=$ty[2];
		$aval=$ty[3];
		$useper=($use+$aval);
		$uper=($use/$useper)*100;
		?>
		<div style="border:1px solid purple; font-family: serif;height: 400px;margin-left: 100px;margin-top: 10px;width: 80%;">
		<div style="background-color:#8c489f;height:40px;"><h1 style="font-size:18px;color:white;text-align:center;font-family:Arial Black;text-transform:uppercase;">Hard Disk Drive Status</h1></div>
		<br><br>
		<table border="0">
		<tr><td>
		<label class="space1"> HDD SPACE USAGE </label></td>
		<td></tr>
		<tr><td><label class="space"> Free Space   : <?php echo $aval; ?> GB</label></td></tr>
		<br><br>
		<tr><td>
		<label class="space"> Used Space   : <?php echo $use; ?> GB</label>
		</td></tr>
		
		<tr><td><br><label class="space2"> VOICEFILES </label></td></tr>
		<tr>
		<td>
		<?php 
			function getFileCount($path) {
    			$size = 0;
    			$ignore = array('.','..','cgi-bin','.DS_Store');
    			$files = scandir($path);
    			foreach($files as $t) {
        		if(in_array($t, $ignore)) continue;
        		if (is_dir(rtrim($path, '/') . '/' . $t)) {
            	$size += getFileCount(rtrim($path, '/') . '/' . $t);
        		} else {
            	$size++;
        		}   
    			}
    			return $size;
			}
			$path='/var/spool/asterisk/monitor/movedvoicefiles/';
			$t=getFilecount($path);
		?>
<label class="space"> Number Of Voicefiles  <?php echo $t; ?></label>
<?php //mysql_close($con);

			?>
		</td>
		</tr>
		<tr>
		<td>
		<br>

		<label class="space2"> WARNING </label></td><tr>

		<tr>
		
		<td>
		<?php 
		if($uper >75)
		{?>
		<blink><label class="space3"> <p id="blinkText">Harddisk Space To Low</p></label></blink>
		<?php }
		else
		{?>
		<label class="space5"> No Warning</label>
		<?php }
		?>
		</td></tr>
		
		</table>
		<div id="formDiv"  class="formDiv drsElement" style="left: 450px; top: 50px;width:500px;"></div>
			<div id="grid" name="grid" align="center"> </div>
			<div id="msgZone" name="msgZone" align="left"> </div>
			<div id="formplaymonitor"  
			style="left: 450px; top: 100px;width: 250px;margin-left:360px;margin-top:-250px;"></div>
			</div>
			
			</td>
			</tr>
			<tr><td>
</td></tr>
		</table>

		<form name="exportForm" id="exportForm" action="dataexport.php" >
			<input type="hidden" value="" id="hidSql" name="hidSql" />
		</form>
		<div id="divCopyright"></div>
		<br>
		<br></div>
	</body>
</html>
