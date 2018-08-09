<?php
/*******************************************************************************
********************************************************************************/

require_once('manager_login.common.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		 
      <title>*astTECS VOICELOGGER</title>
      <met charset="utf-8">
      
      <style type="text/css">
body
    {
        width: 80%;
        background: #GGG;   
    }

.header
{
    margin-left:85px;
    width: 90%;
    height: 100px;
    text-align: center;
    line-height:70px; ;
    border-radius: 100px;
    box-shadow: 0px 0px 20px purple;
}

.header h1
{
    font-size: 55px;
    font-family:arialblack;
}

.login
{
    width: 60%;
    height: 250px;
    margin-top: 20px;
    margin-left: 220px;
    border-radius: 20px;
    box-shadow: 0px 0px 10px purple;
    font-family: serif;
}

.c1
{

    margin-left: 180px;
    margin-top: 40px;
}
.left1
{
   margin-left:-200px;
}
button
{
    margin-right: 310px;
}

a
{
    text-decoration: none;
}

input[type=text] 
{
  padding: 5px;
  border: 3px  #999;
  border-radius: 20px;
  box-shadow: 0px 0px 2px purple;

}

input[type=password] 
{
  padding: 5px;
  border: 3px  #999;
  border-radius: 20px;
  box-shadow: 0px 0px 2px purple;
}

input[type=submit] 
{
  width:100px;
  margin-left: 120px;
  padding: 5px;
  border: 3px  #999;
  border-radius: 20px;
  box-shadow: 0px 0px 2px purple;
  cursor:pointer;
}

input[type=test] 
{
  border: 1px dotted #999;
  border-radius: 0;
  -webkit-appearance: none;
}

</style>
		<meta http-equiv="Content-Language" content="utf-8" />
		<?php $xajax->printJavascript('include/'); ?>
		<script type="text/javascript">
		/**
		*  login function, launched when user click login button
		*
		*  	@param null
		*	@return false
		*/
		function loginSignup()
		{
			xajax.$('loginButton').disabled=true;
			xajax.$('loginButton').value=xajax.$('onclickMsg').value;
			xajax_processForm(xajax.getFormValues("loginForm"));
			return false;
		}

		/**
		*  init function, launched after page load
		*
		*  	@param null
		*	@return false
		*/
		function init(){
			xajax_init(xajax.getFormValues("loginForm"));
			return false;
		}

		function setlanguage(){
			xajax_setLang(xajax.getFormValues("loginForm"));			
			return false;
		}
		</script>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<LINK href="skin/default/css/style.css" type=text/css rel=stylesheet>

	</head>
	<body onload="init();" style="margin-top: 80px;">
	
	<div class="header">
            <h1><font color="purple" face="arial"><i>*astTECS</i> </font></h1>
        </div>
 	<div class="login">
          <div class="c1">

	 <div align="center">
	 		<div id="formDiv">
			<form id="loginForm" action="javascript:void(null);" onsubmit="loginSignup();">
		  	<div class="login_in">
			<!--<div id="titleDiv"></div>-->
			<div class="left1">
			<table width="385" height="143" border="0" cellpadding="0" cellspacing="0">
			  <tr>
				<th width="100" height="58" scope="col">&nbsp;</th>
				<th width="100" valign="bottom" scope="col"><div name="usernameDiv" id="usernameDiv" align="left"></div></th>
				<th width="201" valign="bottom" scope="col"><div align="left">
				  <input name="username" type="text" id="username" size="50" style="width:150px;height:14px" />
			    </div></th>
			  </tr>
			 <br>
			  <tr>
				<td height="49">&nbsp;</td>
				<th><div name="passwordDiv" id="passwordDiv" align="left"></div></th>
				<td><div align="left">
				  <input type="password" name="password" id="password" size="50" style="width:150px;height:14px" />
			    </div></td> 
			  </tr>
			  <br>
			  <tr>
				<td height="36" colspan="2">&nbsp;</td>
				<td><div name="locateDiv" id="locateDiv">
				<input id="loginButton" name="loginButton" type="submit" value=""/>
				<input id="onclickMsg" name="onclickMsg" type="hidden" value=""/>
			    </div>
				</div></td>
			  </tr>
			  </table>
			</div>
			<div class="right"></div><div id="outputDiv"></div>
		  </div></form></div>
	    </div></div></div><div id="divCopyright"></div>
	</body>
</html>
