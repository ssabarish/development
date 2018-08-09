<?php
/*******************************************************************************
* account.grid.inc.php
* account操作类
* Customer class

* @author			Solo Fu <philip.fu@gmail.com>
* @classVersion		1.0
* @date				18 Oct 2007

* Functions List

	getAllRecords				获取所有记录
	getRecordsFiltered			获取记录集
	getNumRows					获取记录集条数
	formAdd						生成添加account表单的HTML
	formEdit					生成编辑account表单的HTML
	新增getRecordsFilteredMore  用于获得多条件搜索记录集
	新增getNumRowsMore          用于获得多条件搜索记录条数

* Revision 0.0456  2007/10/30 13:15:00  last modified by philip
* Desc: add channel field 

* Revision 0.045  2007/10/18 13:15:00  last modified by philip
* Desc: page created

********************************************************************************/

require_once 'db_connect.php';
require_once 'account.common.php';
require_once 'include/astercrm.class.php';


class Customer extends astercrm
{

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
		
		$sql = "SELECT account.* FROM account  ";

		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " ";
		}elseif ($_SESSION['curuser']['usertype'] == 'reseller'){
			$sql .= " WHERE account.resellerid = ".$_SESSION['curuser']['resellerid']." ";
		}else{
			$sql .= " WHERE groupid = ".$_SESSION['curuser']['groupid']." ";
		}

		if($order == null){
			$sql .= " LIMIT $start, $limit";//.$_SESSION['ordering'];
		}else{
			$sql .= " ORDER BY $order ".$_SESSION['ordering']." LIMIT $start, $limit";
		}

		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
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

		$sql = "SELECT account.id as id, username, password, usertype, account.addtime as addtime FROM account  WHERE ";

		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " 1 ";
		}elseif ($_SESSION['curuser']['usertype'] == 'reseller'){
			$sql .= " account.resellerid = ".$_SESSION['curuser']['resellerid']." ";
		}else{
			$sql .= " groupid = ".$_SESSION['curuser']['groupid']." ";
		}

		if ($joinstr!=''){
			$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
			$sql .= " AND ".$joinstr."  ";
		}

		$sql .= " ORDER BY ".$order
					." ".$_SESSION['ordering']
					." LIMIT $start, $limit $ordering";

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
			$sql .= " SELECT COUNT(*) FROM account ";
		}elseif ($_SESSION['curuser']['usertype'] == 'reseller'){
			$sql .= " SELECT COUNT(*) FROM account WHERE resellerid = ".$_SESSION['curuser']['resellerid']." ";
		}else{
			$sql .= " SELECT COUNT(*) FROM account WHERE groupid = ".$_SESSION['curuser']['groupid']." ";
		}

		
		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;		
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

			$sql = "SELECT COUNT(*) FROM account  WHERE ";

			if ($_SESSION['curuser']['usertype'] == 'admin'){
				$sql .= " ";
			}elseif ($_SESSION['curuser']['usertype'] == 'reseller'){
				$sql .= " account.resellerid = ".$_SESSION['curuser']['resellerid']." AND";
			}else{
				$sql .= " groupid = ".$_SESSION['curuser']['groupid']." AND";
			}

			if ($joinstr!=''){
				$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
				$sql .= " ".$joinstr;
			}else {
				$sql .= " 1";
			}
		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;
	}

	function &getRecordsFilteredMorewithstype($start, $limit, $filter, $content, $stype,$order,$table){
		global $db;

		$joinstr = astercrm::createSqlWithStype($filter,$content,$stype);

		$sql = "SELECT account.id as id, username, password, usertype, account.addtime as addtime FROM account  WHERE ";

		if ($_SESSION['curuser']['usertype'] == 'admin'){
			$sql .= " 1 ";
		}elseif ($_SESSION['curuser']['usertype'] == 'reseller'){
			$sql .= " account.resellerid = ".$_SESSION['curuser']['resellerid']." ";
		}else{
			$sql .= " groupid = ".$_SESSION['curuser']['groupid']." ";
		}

		if ($joinstr!=''){
			$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
			$sql .= " AND ".$joinstr."  ";
		}

		$sql .= " ORDER BY ".$order
					." ".$_SESSION['ordering']
					." LIMIT $start, $limit $ordering";

		Customer::events($sql);
		$res =& $db->query($sql);
		return $res;
	}

	function &getNumRowsMorewithstype($filter, $content,$stype,$table){
		global $db;
		
			$joinstr = astercrm::createSqlWithStype($filter,$content,$stype);

			$sql = "SELECT COUNT(*) FROM account  WHERE ";

			if ($_SESSION['curuser']['usertype'] == 'admin'){
				$sql .= " ";
			}elseif ($_SESSION['curuser']['usertype'] == 'reseller'){
				$sql .= " account.resellerid = ".$_SESSION['curuser']['resellerid']." AND";
			}else{
				$sql .= " groupid = ".$_SESSION['curuser']['groupid']." AND";
			}

			if ($joinstr!=''){
				$joinstr=ltrim($joinstr,'AND'); //去掉最左边的AND
				$sql .= " ".$joinstr;
			}else {
				$sql .= " 1";
			}
		Customer::events($sql);
		$res =& $db->getOne($sql);
		return $res;
	}

	function formAdd(){
		global $locate;
			$html = '
			<!-- No edit the next line -->
			<form method="post" name="f" id="f">
			
			<table border="1" width="100%" class="adminlist">
				<tr>
					<td nowrap align="left">'.$locate->Translate("username").'</td>
					<td align="left"><input type="text" id="username" name="username" size="25" maxlength="30"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("password").'</td>
					<td align="left"><input type="text" id="password" name="password" size="25" maxlength="30"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("usertype").'</td>
					<td align="left">
					<select id="usertype" name="usertype">
						<option value=""></option>';

						if ($_SESSION['curuser']['usertype'] == 'admin'){
							$html .= '<option value="admin">'.$locate->Translate("Admin").'</option>';
							
						}

						if ($_SESSION['curuser']['usertype'] == 'admin' || $_SESSION['curuser']['usertype'] == 'reseller'){
							$html .= '<option value="groupadmin">'.$locate->Translate("Group Admin").'</option>';
						}

			$html .= '
						<option value="operator">'.$locate->Translate("Operator").'</option>
					</select></td>
				</tr>
				<!-- mypart <tr>
					<td nowrap align="left">'.$locate->Translate("Reseller").'</td>
					<td align="left">'
						.$reselleroptions.
					'</td>
				</tr>
				-->
				<!--<tr>
					<td nowrap align="left">'.$locate->Translate("Group").'</td>
					<td align="left">'
						.$groupoptions.
					'</td>
				</tr>
-->
				<tr>
					<td colspan="2" align="center"><button id="submitButton" onClick=\'xajax_save(xajax.getFormValues("f"));return false;\'>'.$locate->Translate("Continue").'</button></td>
				</tr>

			 </table>
			';

		$html .='
			</form>
			'.$locate->Translate("obligatory fields").'
			';
		
		return $html;
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
		$account =& Customer::getRecordByID($id,'account');

		$html = '
			<!-- No edit the next line -->
			<form method="post" name="f" id="f">
			
			<table border="1" width="100%" class="adminlist">
				<tr>
					<td nowrap align="left">'.$locate->Translate("username").'</td>
					<td align="left"><input type="hidden" id="id" name="id" value="'. $account['id'].'"><input type="text" id="username" name="username" size="25" maxlength="30" value="'.$account['username'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("password").'</td>
					<td align="left"><input type="text" id="password" name="password" size="25" maxlength="30" value="'.$account['password'].'"></td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("usertype").'</td>
					<td align="left">
					<select id="usertype" name="usertype">
						<option value="" ';
				if($account['usertype'] == ''){
					$html .= ' selected ';
				}
				$html .= '></option>';

				if ($_SESSION['curuser']['usertype'] == 'admin'){
					$html .= '<option value="admin"';
					if($account['usertype'] == 'admin'){
						$html .= ' selected ';
					}
					$html .=' >'.$locate->Translate("Admin").'</option>';
					
				}

				if ($_SESSION['curuser']['usertype'] == 'admin' || $_SESSION['curuser']['usertype'] == 'reseller'){
					$html .= '<option value="groupadmin"';
					if($account['usertype'] == 'groupadmin'){
						$html .= ' selected ';
					}
					$html .=' >'.$locate->Translate("Group Admin").'</option>';
				}

				$html .= ' <option value="operator"';
					if($account['usertype'] == 'operator'){
					$html .= ' selected ';
				}
				$html .= '>'.$locate->Translate("Operator").'</option>';
			$html .='</select>
				</tr>
		<!-- mypart		<tr>
					<td nowrap align="left">'.$locate->Translate("Reseller").'</td>
					<td align="left">'
						.$reselleroptions.
					'</td>
				</tr> -->
				
				<tr>
					<td colspan="2" align="center"><button id="submitButton" onClick=\'xajax_update(xajax.getFormValues("f"));return false;\'>'.$locate->Translate("Continue").'</button></td>
				</tr>

			 </table>
			';

			

		$html .= '
				</form>
				'.$locate->Translate("obligatory fields").'
				';

		return $html;
	}

	/**
	*  Imprime la forma para editar un nuevo registro sobre el DIV identificado por "formDiv".
	*
	*	@param $id		(int)		Identificador del registro a ser editado.
	*	@return $html	(string) Devuelve una cadena de caracteres que contiene la forma con los datos 
	*									a extraidos de la base de datos para ser editados 
	*/
	
	function showAccountDetail($id){
		global $locate;
		$account =& Customer::getRecordByID($id,'account');
		$html = '
			
			<table border="1" width="100%" class="adminlist">
				<tr>
					<td nowrap align="left">'.$locate->Translate("username").'</td>
					<td align="left">'.$account['username'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("password").'</td>
					<td align="left">'.$account['password'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("usertype").'</td>
					<td align="left">'.$account['usertype'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("extensions").'</td>
					<td align="left">'.$account['extensions'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("account_code").'</td>
					<td align="left">'.$account['accountcode'].'</td>
				</tr>
				<tr>
					<td nowrap align="left">'.$locate->Translate("Callback").'</td>
					<td align="left">'.$account['callback'].'</td>
				</tr>
			 </table>
			';

		return $html;
	}

}
?>
