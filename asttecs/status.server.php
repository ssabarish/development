<?php
/*******************************************************************************
* voice.server.php


* Function Desc

* 功能描述

* Function Desc


* Revision 0.01  2007/11/21 12:40:00  last modified by philip
* Desc: page created

********************************************************************************/

require_once ("db_connect.php");
require_once ('status.grid.inc.php');
require_once ('include/xajaxGrid.status.inc.php');
require_once ('include/common.class.php');
require_once ('include/asterevent.class.php');
require_once ("status.common.php");

/**
*  initialize page elements
*
*/

function init(){
	global $locate;

	$objResponse = new xajaxResponse();
	$objResponse->addAssign("divNav","innerHTML",common::generateManageNav($skin));
	$objResponse->addAssign("divCopyright","innerHTML",common::generateCopyright($skin));
	$objResponse->addScript("xajax_showGrid(0,".ROWSXPAGE.",'','','')");
	if ($_SESSION['curuser']['usertype'] == "groupadmin") {
		$row = astercrm::getRecordById($_SESSION['curuser']['groupid'],"voicefiles");
		
		$objResponse->addAssign("spnShortcutMsg","innerHTML", '');

	}
	
	return $objResponse;
}



/**
*  show grid HTML code
*  @param	start		int			record start
*  @param	limit		int			how many records need
*  @param	filter		string		the field need to search
*  @param	content		string		the contect want to match
*  @param	divName		string		which div grid want to be put
*  @param	order		string		data order
*  @return	objResponse	object		xajax response object
*/

function showGrid($start = 0, $limit = 1,$filter = null, $content = null, $order = null, $divName = "grid", $ordering = ""){
	$html .= createGrid($start, $limit,$filter, $content, $order, $divName, $ordering);
	$objResponse = new xajaxResponse();
	$objResponse->addClear("msgZone", "innerHTML");
	$objResponse->addAssign($divName, "innerHTML", $html);

	return $objResponse;
}


/**
*  generate grid HTML code
*  @param	start		int			record start
*  @param	limit		int			how many records need
*  @param	filter		string		the field need to search
*  @param	content		string		the contect want to match
*  @param	divName		string		which div grid want to be put
*  @param	order		string		data order
*  @return	html		string		grid HTML code
*/

function createGrid($start = 0, $limit = 1, $filter = null, $content = null, $order = null, $divName = "grid", $ordering = "", $exportFlag="", $deleteFlag = "",$stype=array()){
	global $locate;
	$_SESSION['ordering'] = $ordering;
	
	if($filter == null or $content == null || (!is_array($content) && $content == 'Array') || (!is_array(filter) && $filter == 'Array')){
		$content = null;
		$filter = null;
		$numRows =& Customer::getNumRows();
		$arreglo =& Customer::getAllRecords($start,$limit,$order);
	}else{
		foreach($content as $value){
			if(trim($value) != ""){  //Search the contents of the value of
				$flag = "1";
				break;
			}
		}
		foreach($filter as $value){
			if(trim($value) != ""){  //Search conditions are the value of
				$flag2 = "1";
				break;
			}
		}
		foreach($stype as $value){
			if(trim($value) != ""){  //Ways to search for value
				$flag3 = "1";
				break;
			}
		}

		if($flag != "1" || $flag2 != "1"){  //	No value
			$order = null;
			$numRows =& Customer::getNumRows();
			$arreglo =& Customer::getAllRecords($start,$limit,$order);
		}elseif($flag3 != 1 ){
			$order = "id";
			$numRows =& Customer::getNumRowsMore($filter, $content,"voicefiles");
			$arreglo =& Customer::getRecordsFilteredMore($start, $limit, $filter, $content, $order,"voicefiles");
		}else{
			$order = "id";
			$numRows =& Customer::getNumRowsMorewithstype($filter, $content,$stype,$table);
			$arreglo =& Customer::getRecordsFilteredMorewithstype($start, $limit, $filter, $content, $stype,$order,$table);
		}
	}
			
	// Select Box: type table.
	$typeFromSearch = array();
	$typeFromSearch[] = 'like';
	$typeFromSearch[] = 'equal';
	$typeFromSearch[] = 'more';
	$typeFromSearch[] = 'less';

	// Selecct Box: Labels showed on searchtype select box.
	$typeFromSearchShowAs = array();
	$typeFromSearchShowAs[] = $locate->Translate("like");
	$typeFromSearchShowAs[] = '=';
	$typeFromSearchShowAs[] = '>';
	$typeFromSearchShowAs[] = '<';

	// Editable zone

	// Databse Table: fields
	$fields = array();
	
	

	$fields[] = 'date';
	$fields[] = 'time';
	$fields[] = 'sourse';
	$fields[] = 'destination';
	$fields[] = 'duration';
	
	// HTML table: Headers showed
	

    // HTML table: hearders attributes
	
	// HTML Table: columns attributes
	$attribsCols = array();
    
	
    $attribsCols[] = 'style="text-align: center"';
	$attribsCols[] = 'style="text-align: center"';
	$attribsCols[] = 'style="text-align: center"';
	$attribsCols[] = 'style="text-align: center"';
	$attribsCols[] = 'style="text-align: center"';

	// HTML Table: If you want ascendent and descendent ordering, set the Header Events.
	$eventHeader = array();
	
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","date","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","time","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","sourse","'.$divName.'","ORDERING");return false;\'';
   $eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","destination","'.$divName.'","ORDERING");return false;\'';
	$eventHeader[]= 'onClick=\'xajax_showGrid(0,'.$limit.',"'.$filter.'","'.$content.'","duration","'.$divName.'","ORDERING");return false;\'';
    
	// Select Box: fields table.
	$fieldsFromSearch = array();
	
	$fieldsFromSearch[] = 'date';
	$fieldsFromSearch[] = 'time';
	$fieldsFromSearch[] = 'sourse';
	$fieldsFromSearch[] = 'destination';
	$fieldsFromSearch[] = 'duration';
	
	// Selecct Box: Labels showed on search select box.
	$fieldsFromSearchShowAs = array();
	$fieldsFromSearchShowAs[] = $locate->Translate("Date");
	$fieldsFromSearchShowAs[] = $locate->Translate("Time");
	$fieldsFromSearchShowAs[] = $locate->Translate("Source");
	$fieldsFromSearchShowAs[] = $locate->Translate("Destination");
	$fieldsFromSearchShowAs[] = $locate->Translate("duration");

	// Create object whit 5 cols and all data arrays set before.
	$table = new ScrollTable(6,$start,$limit,$filter,$numRows,$content,$order);
	if ($_SESSION['curuser']['usertype'] == 'admin' || $_SESSION['curuser']['usertype'] == 'groupadmin')
	{ $table->setHeader('title',$headers,$attribsHeader,$eventHeader,0,0,0); 
	 if( $_SESSION['curuser']['usertype'] == 'groupadmin')
	   {
		$table->setHeader('title',$headers,$attribsHeader,$eventHeader,0,1,1);
		$table->deleteFlag = '1';//对删除标记进行赋值
		$table->exportFlag = '1';//对导出标记进行赋值
		}
	}else{
		$table->setHeader('title',$headers,$attribsHeader,$eventHeader,0,0,0);
	}


	$table->setAttribsCols($attribsCols);
	

	if ($_SESSION['curuser']['usertype'] == 'admin' || $_SESSION['curuser']['usertype'] == 'groupadmin')
		$table->addRowSearchMore("voicefiles",$fieldsFromSearch,$fieldsFromSearchShowAs,$filter,$content,$start,$limit,1,$typeFromSearch,$typeFromSearchShowAs,$stype);
	else
		$table->addRowSearchMore("voicefiles",$fieldsFromSearch,$fieldsFromSearchShowAs,$filter,$content,$start,$limit,0,$typeFromSearch,$typeFromSearchShowAs,$stype);
	$table->addRow("voicefiles",$rowc,1,0,0,$divName,$fields);

	
 	
 	$html = $table->render();
 	
 	return $html;
}


/**
*  generate account add form HTML code
*  @return	html		string		account add HTML code
*/



/**
*  update account record
*  @param	f			array		account record
*  @return	objResponse	object		xajax response object
*/


function playmonitor($id)
   {
	global $config,$locate;
	$objResponse = new xajaxResponse();
	 $sql="SELECT filename FROM voicefiles WHERE id='".$id."'";
	 $result = mysql_query($sql);
	$filen= mysql_fetch_array($result);
	 $name=$filen['filename'];
	$filename = explode("_", $name);
	$datetime = explode("-",$filename['0']);	
	$date1= $datetime['0']; 
	$year=substr($date1,0,4);
	$month=substr($date1,4,2);
	$day=substr($date1,6,2);
	$monthName = date("F", mktime(0, 0, 0, $month, 10)); //echo $monthName;

	 $path="/var/spool/asterisk/monitor/movedvoicefiles/".$year."/".$monthName."/".$day."/".$name; 
	//echo $path; exit;
	$html = Table::Topp($locate->Translate("playmonitor"),"formplaymonitor");
	$html .= '<embed src="graph.php?file='.$path.'" autostart="true" width="350" height="300" name="sound" id="sound" enablejavascript="true">';
	$html .= Table::Footer();
	$objResponse->addAssign("formplaymonitor", "style.visibility", "visible");
	$objResponse->addAssign("formplaymonitor", "innerHTML", $html);	
	return $objResponse->getXML();
}
function playmonitor1($id)
   {global $config,$locate;
	$objResponse = new xajaxResponse();
		 $path=$id; 
		 echo $path;
	 exit;
	
	$html = Table::Topp($locate->Translate("playmonitor"),"formplaymonitor");
	$html .= '<embed src="records.php?file='.$path.'" autostart="true" width="300" height="40" name="sound" id="sound" enablejavascript="true">';
	$html .= Table::Footer();
	$objResponse->addAssign("formplaymonitor", "style.visibility", "visible");
	
	$objResponse->addAssign("formplaymonitor", "innerHTML", $html);	
	return $objResponse->getXML();
}

$xajax->processRequests();
?>
