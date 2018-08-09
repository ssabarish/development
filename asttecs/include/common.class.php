<?php


header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: post-check=0, pre-check=0',false);
header('Pragma: no-cache');
session_cache_limiter('public, no-store');

session_set_cookie_params(0);
if (!session_id()) session_start();
setcookie('PHPSESSID', session_id());


require_once ('localization.class.php');

if ($_SESSION['curuser']['country'] != '' ){
	$GLOBALS['locate_common']=new Localization($_SESSION['curuser']['country'],$_SESSION['curuser']['language'],'common.class');
}else{
	$GLOBALS['locate_common']=new Localization('en','US','common.class');
}

class Common{

	function generateCopyright($skin){
		global $locate_common;

		$html .='
				<hr style="clear:both;border: 0px;width: 80%;">
				<div align="center">
					<table class="copyright" id="tblCopyright">
					<tr>
						@2014 astVlogger version 3.0.0
						<a target="_blank" href="http://www.asttecs.com">*astTECS</a>
					</tr>
					</table>
				</dvi>
				';
		return $html;
	}

	function generateManageNav($skin){
		global $locate_common;

		$html .= '<div id="pagewidth" >';
		/*$html .= '<div class="">';
		$html .= $locate_common->Translate("Username").' : '.$_SESSION['curuser']['username'];
		$html .= '&nbsp;&nbsp;'.$locate_common->Translate("User Type").' : '.$_SESSION['curuser']['usertype'];
		$html .= '</div>';*/
		$html .= '<div style="font-weight:bold;font-size:40px;color:purple;padding:15px 0 0 0;font-family:Arial Black;"><i>* astTECS</i> <label style="color:#B0B0B0 ;"> | </label><label style="font-size:35px;font-weight:bold;color:black;font-family:Arial;">Voice Logger</label></div>
			  ';
		$html .= '<div id="header">
			  <ul><div style="margin-left:20px;">';

		$aryMenu = array();
		$aryMenu['report'] = array("link"=>"status.php","title"=> $locate_common->Translate("status"));
		$aryMenu['customerrate'] = array("link"=>"voicefiles.php","title"=> $locate_common->Translate("Voice files"));
		$aryMenu['clid'] = array("link"=>"backup.php","title"=> $locate_common->Translate("download"));
		//$aryMenu['home'] = array("link"=>"archive.php","title"=> $locate_common->Translate("archive"));

		$aryMenu['account'] = array("link"=>"account.php","title"=> $locate_common->Translate("Account"));
		$aryMenu['accountgroup'] = array("link"=>"accountgroup.php","title"=> $locate_common->Translate("Account Group"));
		$aryMenu['resellergroup'] = array("link"=>"accountgroup.php","title"=> $locate_common->Translate("Account Group"));
		$aryMenu['home'] = array("link"=>"/asterisk-stat/cdr.php","title"=> $locate_common->Translate("CDR"));

			

		if ($_SESSION['curuser']['usertype'] == 'admin')
		{
			$aryCurMenu =array('report','customerrate','clid','account');
			$html .= common::generateNavMenu($aryMenu,$aryCurMenu);
		}
		elseif($_SESSION['curuser']['usertype'] == 'groupadmin')
		{
			$aryCurMenu = array('report','customerrate','clid');
			$html .= common::generateNavMenu($aryMenu,$aryCurMenu);
		}
		else
		{ // operator
			$aryCurMenu = array('report','customerrate');
			$html .= common::generateNavMenu($aryMenu,$aryCurMenu);
		}

		if($_SESSION['curuser']['usertype'] == 'clid')
		{
			$html .= '<li><a href="login.php" onclick="if (confirm(\''.$locate_common->Translate("Are you sure to exit").'?\')){}else{return false;}">'.$locate_common->Translate("Logout").'</li>';
		}else{
			$html .= '<li><a href="manager_login.php" onclick="if (confirm(\''.$locate_common->Translate("Are you sure to exit").'?\')){}else{return false;}">'.$locate_common->Translate("Logout").'</li>';

		}
		
		$html .= '</div></ul></div>';
		$html .= '</div>';
		return $html;
	}

	function generateNavMenu($aryMenu,$aryCurMenu = ''){
		$html = '';
		if ($aryCurMenu == ""){
			foreach ($aryMenu as $key=>$val){
				$html  .= '<li><a title="'.$aryMenu[$key]['title'].'" href="'.$aryMenu[$key]['link'].'" class="'.$key.'">'.$aryMenu[$key]['title'].'</a></li>';
			}
		}else{
			foreach ($aryCurMenu as $key){
				$html  .= '<li><a title="'.$aryMenu[$key]['title'].'" href="'.$aryMenu[$key]['link'].'" class="'.$key.'">'.$aryMenu[$key]['title'].'</a></li>';
			}
		}
		//echo $html;exit;
		return $html;
	}

//	生成显示一个数组内容的HTML代码
	function generateTabelHtml($aDyadicArray,$thArray = null){
		if (!is_Array($aDyadicArray))
			return '';
		$html .= "<table class='myTable'>";
		$myArray = array_shift($aDyadicArray);
		foreach ($myArray as $field)
		{
			$html .= "<th>";
			$html .= $field;
			$html .= "</th>";
		}

		foreach ($aDyadicArray as $myArray)
		{
			$html .="<tr>";
			foreach ($myArray as $field)
			{
				$html .= "<td>";
				$html .= $field;
				$html .= "</td>";
			}
			$html .="</tr>";
		}
		$html .= "</table>";
		return $html;
	}

    function read_ini_file($f, &$r)
    {
        $null = "";
        $r=$null;
        $first_char = "";
        $sec=$null;
        $comment_chars=";#";
        $num_comments = "0";
        $num_newline = "0";

        //Read to end of file with the newlines still attached into $f
        $f = @file($f);
        if ($f === false) 
        {
            return -2;
        }
        // Process all lines from 0 to count($f)
        for ($i=0; $i<@count($f); $i++)
        {
            $w=@trim($f[$i]);
            $first_char = @substr($w,0,1);
            if ($w)
            {
                if ((@substr($w,0,1)=="[") and (@substr($w,-1,1))=="]") 
                {
                    $sec=@substr($w,1,@strlen($w)-2);
                    $num_comments = 0;
                    $num_newline = 0;
                }
                else if ((stristr($comment_chars, $first_char) == true)) 
                {
                    $r[$sec]["Comment_".$num_comments]=$w;
                    $num_comments = $num_comments +1;
                }                
                else {
                    // Look for the = char to allow us to split the section into key and value
                    $w=@explode("=",$w);
                    $k=@trim($w[0]);
                    unset($w[0]);
                    $v=@trim(@implode("=",$w));
                    // look for the new lines
                    if ((@substr($v,0,1)=="\"") and (@substr($v,-1,1)=="\"")) 
                    {
                        $v=@substr($v,1,@strlen($v)-2);
                    }
                    
                    $r[$sec][$k]=$v;
                    
                }
            }
            else {
                $r[$sec]["Newline_".$num_newline]=$w;
                $num_newline = $num_newline +1;
            }
        }
        return 1;
    }

    function beginsWith( $str, $sub ) {
        return ( substr( $str, 0, strlen( $sub ) ) === $sub );
    } 

	
    function write_ini_file($path, $assoc_arr) 
    {
        $content = "";
        foreach ($assoc_arr as $key=>$elem) 
        {
            if (is_array($elem)) 
            {
                if ($key != '') 
                {
                    $content .= "[".$key."]\r\n";                    
                }
                
                foreach ($elem as $key2=>$elem2) 
                {
                    if (Common::beginsWith($key2,'Comment_') == 1 && Common::beginsWith($elem2,';')) 
                    {
                        $content .= $elem2."\r\n";
                    }
                    else if (Common::beginsWith($key2,'Newline_') == 1 && ($elem2 == '')) 
                    {
                        $content .= $elem2."\r\n";
                    }
                    else 
                    {
                        $content .= $key2." = ".$elem2."\r\n";
                    }
                }
            }
            else 
            {
                $content .= $key." = ".$elem."\r\n";
            }
        }

        if (!$handle = fopen($path, 'w')) 
        {
            return -2;
        }
        if (!fwrite($handle, $content)) 
        {
            return -2;
        }
        fclose($handle);
        return 1;
    }

}
?>
