<?php
/*******************************************************************************
* voice.grid.inc.php

* @author			Solo Fu <solo.fu@gmail.com>
* @classVersion		1.0
* @date				18 Oct 2007

* Functions List


* Revision 0.01  2007/11/21 13:15:00  last modified by solo
* Desc: page created

********************************************************************************/

require_once 'db_connect.php';
require_once 'status.common.php';
require_once 'include/astercrm.class.php';


class Customer extends astercrm
{

	/**
	*  Inserta un nuevo registro en la tabla.
	*
	*	@param $f	(array)		Arreglo que contiene los datos del formulario pasado.
	*	@return $res	(object) 	Devuelve el objeto con la respuesta de la sentencia SQL ejecutada del INSERT.

	*/
	
	/**
	*  Obtiene todos los registros de la tabla paginados.
	*
	*  	@param $start	(int)	Inicio del rango de la p&aacute;gina de datos en la consulta SQL.
	*	@param $limit	(int)	L&iacute;mite del rango de la p&aacute;gina de datos en la consultal SQL.
	*	@param $order 	(string) Campo por el cual se aplicar&aacute; el orden en la consulta SQL.
	*	@return $res 	(object) Objeto que contiene el arreglo del resultado de la consulta SQL.
	*/
	function &getAllRecords($start, $limit, $order = null, $creby = null){
		global $db;
		
		$sql = "SELECT voicefiles.* FROM voicefiles  WHERE ";

		if ($_SESSION['curuser']['usertype'] == 'admin')
		{
			$sql .= " 1 ";
		}elseif ($_SESSION['curuser']['usertype'] == 'reseller'){
			$sql .= " (voicefiles.resellerid = ".$_SESSION['curuser']['resellerid']." OR voicefiles.resellerid = 0)";
		}else{
			$sql .= " 1";
		}


//		if ($creby != null)
//			$sql .= " WHERE note.creby = '".$_SESSION['curuser']['username']."' ";
			

		if($order == null){
			$sql .= " LIMIT $start, $limit";//.$_SESSION['ordering'];
		}else{
			$sql .= " ORDER BY $order ".$_SESSION['ordering']." LIMIT $start, $limit";
		}

		Customer::events($sql);
		$res =& $db->query($sql);
		//return $res;
	}
	
	/**
	*  Obtiene todos registros de la tabla paginados y aplicando un filtro
	*
	*  @param $start		(int) 		Es el inicio de la p&aacute;gina de datos en la consulta SQL
	*	@param $limit		(int) 		Es el limite de los datos p&aacute;ginados en la consultal SQL.
	*	@param $filter		(string)	Nombre del campo para aplicar el filtro en la consulta SQL
	*	@param $content 	(string)	Contenido a filtrar en la conslta SQL.
	*	@param $order		(string) 	Campo por el cual se aplicar&aacute; el orden en la consulta SQL.
	*	@return $res		(object)	Objeto que contiene el arreglo del resultado de la consulta SQL.
	*/

	function &getRecordsFilteredMore($start, $limit, $filter, $content, $order,$table, $ordering = ""){
		global $db;

		$i=0;
		$joinstr='';
		foreach ($content as $value){
			$value = preg_replace("/'/","\\'",$value);
			$value=trim($value);
			if (strlen($value)!=0 && strlen($filter[$i]) != 0){
				$joinstr.="AND $filter[$i] like '%".$value."%' ";
			}
			$i++;
		}
		
		$sql = "SELECT voicefiles.*  FROM voicefiles    WHERE ";

		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " 1 ";
		}elseif ($_SESSION['curuser']['usertype'] == 'reseller'){
			$sql .= " (voicefiles.resellerid = ".$_SESSION['curuser']['resellerid']." OR voicefiles.resellerid = 0) ";
		}else{
			$sql .= " 1";
		}

		if ($joinstr!=''){
			$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
			$sql .= " AND ".$joinstr." "
					." ORDER BY ".$order
					." ".$_SESSION['ordering']
					." LIMIT $start, $limit $ordering";
		}
		
       Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
	}
	/**
	*  Devuelte el numero de registros de acuerdo a los par&aacute;metros del filtro
	*
	*	@param $filter	(string)	Nombre del campo para aplicar el filtro en la consulta SQL
	*	@param $order	(string)	Campo por el cual se aplicar&aacute; el orden en la consulta SQL.
	*	@return $row['numrows']	(int) 	N&uacute;mero de registros (l&iacute;neas)
	*/
	
	function &getNumRows($filter = null, $content = null){
		global $db;
		
		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " SELECT COUNT(*) FROM voicefiles ";
		}elseif ($_SESSION['curuser']['usertype'] == 'reseller'){
			$sql .= " SELECT COUNT(*) FROM voicefiles  ";
		}else{
			$sql .= " SELECT COUNT(*) FROM voicefiles ";
		}

		Customer::events($sql);
		$res =& $db->getOne($sql);
		//return $res;		
	}

	function &getNumRowsMore($filter = null, $content = null,$table){
		global $db;
		
			$i=0;
			$joinstr='';
			foreach ($content as $value){
				$value = preg_replace("/'/","\\'",$value);
				$value=trim($value);
				if (strlen($value)!=0 && strlen($filter[$i]) != 0){
					$joinstr.="AND $filter[$i] like '%".$value."%' ";
				}
				$i++;
			}
			$sql = "SELECT COUNT(*) AS numRows FROM voicefiles  WHERE";

			if ($_SESSION['curuser']['usertype'] == 'admin'){
				$sql .= " 1 ";
			}elseif ($_SESSION['curuser']['usertype'] == 'reseller'){
				$sql .= " (voicefiles.resellerid = ".$_SESSION['curuser']['resellerid']." OR voicefiles.resellerid = 0) ";
			}else{
				$sql .= " 1 ";
			}

			if ($joinstr!=''){
				$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
				$sql .= " AND ".$joinstr." ";
			}else {
				$sql .= " 1 ";
			}

		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;
	}

	function &getRecordsFilteredMorewithstype($start, $limit, $filter, $content, $stype,$order,$table){
		global $db;

		$joinstr = astercrm::createSqlWithStype($filter,$content,$stype);
		
		$sql = "SELECT voicefiles.* FROM voicefiles   WHERE ";

		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " 1 ";
		}elseif ($_SESSION['curuser']['usertype'] == 'reseller'){
			$sql .= " (voicefiles.resellerid = ".$_SESSION['curuser']['resellerid']." OR voicefiles.resellerid = 0) ";
		}else{
			$sql .= " 1 ";
		}

		if ($joinstr!=''){
			$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
			$sql .= " AND ".$joinstr." "
					." ORDER BY ".$order
					." ".$_SESSION['ordering']
					." LIMIT $start, $limit $ordering";
		}
		

		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	function &getNumRowsMorewithstype($filter, $content,$stype,$table){
		global $db;
		
			$joinstr = astercrm::createSqlWithStype($filter,$content,$stype);

			$sql = "SELECT COUNT(*) AS numRows FROM voicefiles  WHERE";

			if ($_SESSION['curuser']['usertype'] == 'admin'){
				$sql .= " 1 ";
			}elseif ($_SESSION['curuser']['usertype'] == 'reseller'){
				$sql .= " (voicefiles.resellerid = ".$_SESSION['curuser']['resellerid']." OR voicefiles.resellerid = 0) ";
			}else{
                  $sql .= " 1 ";				
			}

			if ($joinstr!=''){
				$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
				$sql .= " AND ".$joinstr." ";
			}else {
				$sql .= " 1 ";
			}

		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;
	}

	
	/**
	*  Imprime la forma para editar un nuevo registro sobre el DIV identificado por "formDiv".
	*
	*	@param $id		(int)		Identificador del registro a ser editado.
	*	@return $html	(string) Devuelve una cadena de caracteres que contiene la forma con los datos 
	*									a extraidos de la base de datos para ser editados 
	*/
	
	function formEdit($id){
		global $locate;
		$rate =& Customer::getRecordByID($id,'voicefiles');
		
		$reselleroptions = '';
		$reseller = astercrm::getAll('resellergroup');

		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$reselleroptions .= '<select id="resellerid" name="resellerid" onchange="setGroup();">';
			$reselleroptions .= '<option value="0"></option>';
			while	($reseller->fetchInto($row)){
				if ($row['id'] == $rate['resellerid']){
					$reselleroptions .= "<OPTION value='".$row['id']."' selected>".$row['resellername']."</OPTION>";
				}else{
					$reselleroptions .= "<OPTION value='".$row['id']."' >".$row['resellername']."</OPTION>";
				}
			}
			$reselleroptions .= '</select>';
		}else{
			while	($reseller->fetchInto($row)){
				if ($row['id'] == $rate['resellerid']){
					$reselleroptions .= $row['resellername'].'<input type="hidden" value="'.$row['id'].'" name="resellerid" id="resellerid">';
					break;
				}
			}
		}

		$group = astercrm::getAll('accountgroup','resellerid',$rate['resellerid']);
		if ($_SESSION['curuser']['usertype'] == 'admin' || $_SESSION['curuser']['usertype'] == 'reseller'){
			$groupoptions .= '<select id="groupid" name="groupid">';
			$groupoptions .= "<OPTION value='0'></OPTION>";
			while	($group->fetchInto($row)){
				if ($row['id'] == $rate['groupid'])
				{
					$groupoptions .= "<OPTION value='".$row['id']."' selected>".$row['groupname']."</OPTION>";
				}else{
					$groupoptions .= "<OPTION value='".$row['id']."' >".$row['groupname']."</OPTION>";
				}
			}
			$groupoptions .= '</select>';
		}else{
			while	($group->fetchInto($row))
			{
				if ($row['id'] == $rate['groupid'])
				{   
					$groupoptions .= $row['groupname'].'<input type="hidden" value="'.$row['id'].'" name="groupid" id="groupid">';
					break;
				}
			}
		}
      
		$html = '
			<!-- No edit the next line -->
			<form method="post" name="f" id="f">
			
			<table border="1" width="100%" class="adminlist">
				<tr>
					<td nowrap align="left">'.$locate->Translate("prefix").'</td>
					<td align="left"><input type="hidden" id="id" name="id" value="'. $rate['id'].'"><input type="hidden" id="dialprefix" name="dialprefix" size="25" maxlength="30" value="'.$rate['dialprefix'].'" >'.$rate['dialprefix'].'&nbsp;<span id="spanShowBuyRate" name="spanShowBuyRate"></span></td>
				</tr>
					<input type="hidden" id="numlen" name="numlen" size="10" maxlength="10" value="'.$rate['numlen'].'">
				<tr>
					<td nowrap align="left">'.$locate->Translate("Destination").'</td>
					<td align="left"><input type="text" id="destination" name="destination" size="25" maxlength="30" value="'.$rate['destination'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Connect charge(incl. VAT)").'</td>
					<td align="left"><input type="text" id="connectcharge" name="connectcharge" size="20" maxlength="20" value="'.astercc::creditDigits($rate['connectcharge']).'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Init block").'</td>
					<td align="left"><input type="text" id="initblock" name="initblock" size="25" maxlength="100" value="'.$rate['initblock'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Buying Rate(excl. VAT)").'</td><input type="hidden" id="rateinitial" name="rateinitial" size="25" maxlength="30" value="'.$rate['buyingrate'].'">	<td align="left">'.$rate['buyingrate'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Margin").'</td>
					<td align="left"><input type="text" id="margin" name="margin" size="25" maxlength="30" onChange=mym("'.$taxres.'");  value="'.$rate['margin'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Selling (incl. VAT)").'</td>
					<td align="left"><input type="text" id="selling" name="selling" size="25" maxlength="30" onChange=mys("'.$taxres.'"); value="'.number_format(round($rate['rateinitial'],2),2).'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Billing block").'</td>
					<td align="left"><input type="text" id="billingblock" name="billingblock" size="25" maxlength="30" onChange=my("'.$taxres.'"); value="'.$rate['billingblock'].'"></td>
				</tr>
					
					<tr>
					<td colspan="2" align="center">
						<button id="submitButton" onClick=\'xajax_update(xajax.getFormValues("f"));return false;\'>'.$locate->Translate("Continue").'</button>
					</td>
				</tr>

			 </table>
			';

			

		$html .= '
				</form>
				*'.$locate->Translate("obligatory_fields").'
				';

		return $html;
	}

	function shortUpdateGrid($groupid,$resellerid){
		global $locate;
		$html = '<table border="0" width="99%" style="line-height: 30px;" class="adminlist"><tbody><tr><th class="title" ><b>'.$locate->Translate("Shortcut update rate").'</b></th></tr><tbody></table>
				<table border="0" width="99%" style="line-height: 25px; padding: 0px;" class="adminlist" ><tbody>';
		$ratelist = astercc::searchRateForShortUpdate($groupid,$resellerid);
		$flag = 0;
		$class="row0";
		foreach($ratelist as $rate_row){
			$flag++;
			if($flag%2 == 0) $tr .= '<td style="cursor: pointer;" width="10%">&nbsp;&nbsp;</td>';
			
			$tr .= '<td style="cursor: pointer;" width="10%">'.$rate_row['mdialprefix'].'</td><td style="cursor: pointer;" width="10%">'.$rate_row['mdestination'].'</td><td style="cursor: pointer;" width="10%">'.$rate_row['crateinitial'].'</td><td style="cursor: pointer;" width="10%"><input type="text" value="'.$rate_row['mrateinitial'].'" size="10" id="'.$rate_row['mid'].'-mrateinitial" onKeyUp="filedFilter(this,\'numeric\');"></td><td style="cursor: pointer;" width="10%"><input type="button" value="'.$locate->Translate("Update").'" onclick="shortcutUpdateSave(\''.$rate_row['mid'].'\');"></td>';
			
			if($flag%2 != 0){
				$tr = '<tr class="'.$class.'">'.$tr;
				if($class == 'row1') 
					$class = 'row0';
				else
					$class = 'row1';
			}else{
				$tr = $tr.'</tr>';
				$html .= $tr;
				$tr = '';				
			}
		}
		
		if($flag%2 != 0) $html .= $tr.'<td></td><td colspan="5"></td></tr>';
		$html .= '</tbody></table>';
		return $html;
	}
}
?>
