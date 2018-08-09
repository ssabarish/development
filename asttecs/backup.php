<?php

require_once('backup.common.php');
 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>Voicfiles Download</title>
		<?php $xajax->printJavascript('include/'); ?>
		<meta http-equiv="Content-Language" content="utf-8" />
		
		<SCRIPT LANGUAGE="JavaScript">
		<!--

			function init(){
				xajax_init();
				dragresize.apply(document);
			}

		
		
		//-->
		</SCRIPT>
		
		<script type="text/javascript">
		function validateForm() {
    var x = document.forms["frmSchedule"]["sdate"].value;
    if (x == null || x == "") {
        alert("Please Fill The Required fields");
        return false;
    }
	 var x = document.forms["frmSchedule"]["tdate"].value;
    if (x == null || x == "") {
        alert("Please Fill The Required fields");
        return false;
    }


//window.opener.location.reload();
document.getElementById('loadingmask').style.display='block';

}

	</script>
		<script type="text/javascript" src="jsDatePick.min.1.3.js"></script>
		<script language="JavaScript" type="text/javascript" src="date-picker.js">
		<script type="text/javascript" src="js/dragresize.js"></script>
		<script type="text/javascript" src="js/dragresizeInit.js"></script>
		<script type="text/javascript" src="js/astercrm.js"></script>
		<script type="text/javascript" src="js/jquery.1.4.2.js"></script>


		<link rel="stylesheet" type="text/css" media="all" href="jsDatePick_ltr.min.css" />
		<LINK href="skin/default/css/style.css" type=text/css rel=stylesheet>
		<LINK href="skin/default/css/dragresize.css" type="text/css" rel="stylesheet">
		
		<style type="text/css">
		input[type=button] 
		{
  			width:100px;
  			margin-left: 120px;
  			padding: 5px;
  			border: 3px  #999;
  			
  			
		}
		.header
		{
   			margin-left:45px;
    			width: 90%;
   			height: 100px;
    			text-align: center;
    			line-height:70px; ;
    			
		}

		.header h1
		{
   			font-size: 20px;
    			font-family:arialblack;
		}
		.space
		{
			font-size:14px;
			margin-left:10px;
			font-weight:bold;
			color:darkgreen;
		}


		</style>
		
	</head>
	<body onload="init();" id="clid"><!-- <input type="text" size="12" id="inputField" />-->

	
		<div style="border:1px solid;"><div id="divNav"></div><br>
		<table width="100%" border="0" style="background: #F9F9F9; padding: 0px;">
		 <tr>
			<td style="padding: 0px;">
				<div style="border-radius: 10px;border:2px solid #ddd; font-family: serif;height: 300px;margin-left: 220px;margin-top: 10px;width: 60%;">
						<form action="zipfiles.php" method="POST" name="frmSchedule" id="frmSchedule" onsubmit="return validateForm();">
						<div class="header"><h1><font color="purple" >VOICEFILES DOWNLOAD</font></h1></div>
						<div style="margin-top:2px;margin-left:200px;">
  						<b><label style="font-size:18px;">From Date &nbsp;&nbsp;</label></b><input type="text" name="sdate" id="sdate" size="25" maxlength="50" style="padding: 5px;
  						border: 3px  #999;
  						border-radius: 20px;
  						box-shadow: 0px 0px 2px purple;
						">
  						<?php echo '&nbsp;';
                    				$test = '<span id="calImg">';
                   				$test.= "<a href=\"javascript:show_calendar('sdate')\" ";
                    				$test.= 'title="Calendar">';
                    				$test.= "<img src='images/cal.gif'";
                    				$test.= 'alt="Calendar" name="cal2" width="16" height="16" border="0" align="middle" id="cal2"></a></span>';
                    				echo $test;
         					?>
						<br>
						<br>
						<b><label style="font-size:18px;">To Date &nbsp;&nbsp;</label></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="tdate" id="tdate" size="25" maxlength="50" style="padding: 5px;
  						border: 3px  #999;
  						border-radius: 20px;
  						box-shadow: 0px 0px 2px purple;
						">
  						<?php echo '&nbsp;';
                    				$test = '<span id="calImg">';
                   				$test.= "<a href=\"javascript:show_calendar('tdate')\" ";
                    				$test.= 'title="Calendar">';
                    				$test.= "<img src='images/cal.gif'";
                    				$test.= 'alt="Calendar" name="cal2" width="16" height="16" border="0" align="middle" id="cal2"></a></span>';
                    				echo $test;
         					?>

						</div>
  						<br/><br/>
						<!--<input type="button" name="submit" value="Download" onclick="check()" style="margin-left:200px;width:100px;margin-left: 280px;padding: 5px;border: 3px  #999;border-radius: 20px;box-shadow: 0px 0px 2px purple;cursor:pointer;">-->
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
  						<input type="submit" name="submit" value="Zip The File"  style="margin-left:200px;width:100px;margin-left: 280px;padding: 5px;border: 3px  #999;border-radius: 20px;box-shadow: 0px 0px 2px purple;cursor:pointer;">
						<br>
						<div>
						<div id="loadingmask" style="display: none;">
						<div class="loader" id="loading-mask-loader"><label style="font-size:14px;margin-left:100px;">Processing...</label><img src="images/loading-spinner.gif" alt=""/></div>
						</div>
						<?php $t=$_REQUEST['value']; 
							if($t=='0')	
							{?>
							<label class="space"> No Record Found !</label>
							<?php }
							
							if($t=='1')								
							{?>
							<label class="space"> Successfully Created !      </label>
							<a href="download.php?f=Voicefiles.zip"><input type="button" name="submit" value="DOWNLOAD FILE"  style="margin-left:20px;width:120px;padding: 5px;border: 3px  #999;border-radius: 20px;box-shadow: 0px 0px 2px purple;cursor:pointer;">
</a>
							
							<?php }
							?>

						</div>

   
						</form>

				</div>
			</td>
			
		
		
		</tr>
		
	</table>
		<br>
		<div id="divCopyright"></div></div>
	</body>
</html>
