<?php
/*******************************************************************************
********************************************************************************/

require_once('voice.common.php');




?>

<html>
	<head>
	<title>Voicefiles</title>
		<?php $xajax->printJavascript('include/'); ?>
		<meta http-equiv="Content-Language" content="utf-8" />
		<SCRIPT LANGUAGE="JavaScript">
		<!--

			function init(){
				xajax_init();
				dragresize.apply(document);
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

		function updateCustomerMultiple(){
			if (confirm("<?echo $locate->Translate("are you sure to update this value");?>"))
			{
				xajax_updateCustomerMultiple(document.getElementById("margin").value);
				
				xajax_updateCustomerMultiple(document.getElementById("selling").value);
				xajax_updateCustomerMultiple(document.getElementById("tax").value);
				xajax_updateCustomerMultiple(document.getElementById("billing").value);
			}
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
		<script type="text/javascript" src="datetimepicker_css.js"></script>
		<LINK href="skin/default/css/style.css" type=text/css rel=stylesheet>
		<LINK href="skin/default/css/dragresize.css" type=text/css rel=stylesheet>
		
	</head>
	<body onload="init();" id="customerrate">
	

	
		<div style="border:1px solid;"><div id="divNav"></div><br>
		<table  width="100%" border="0" style="background: #F9F9F9; padding: 1px;">
			<tr> <!-- <tr><td  valign="top" align="center" width="80%"><img src="images/logotitle.jpg" align="center" width="80%" />  </td></tr>
</tr>--><tr>
				<td style="padding: 0px;">
					
			
			</div>
			<div id="formDiv"  class="formDiv drsElement" 
				style="left: 450px; top: 50px;width:500px;"></div>
			<div id="grid" name="grid" align="center"> </div>
			<div id="msgZone" name="msgZone" align="left"> </div>
			<div id="formplaymonitor"  class="formDiv drsElement" 
			style="left: 450px; top: 300px;width: 350px""></div>
					
				</td>
			</tr>
		</table>

		<form name="exportForm" id="exportForm" action="dataexport.php" >
			<input type="hidden" value="" id="hidSql" name="hidSql" />
		</form>
		<div id="divCopyright"></div></div>
	</body>
</html>
