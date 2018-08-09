<?php
/*******************************************************************************
* astercc.class.php
* asterisk events class, read datas from table: curcdr
**/

class astercc extends PEAR
{
	function generateResellerFile(){
		global $db,$config;
		if ($config['system']['sipfile'] == '')	return;

		$content = '';
		$filename = $config['system']['sipfile'];
		
		$query = "SELECT id FROM resellergroup ";
		$reseller_list = $db->query($query);

		while	($reseller_list->fetchInto($reseller)){
			$content .= "#include ".$filename."_".$reseller['id'].".conf\n";
		}

		$filename = $filename.".conf";

		$fp=fopen($filename,"w");
		if (!$fp){
			print "file: $filename open failed, please check the file permission";
			exit;
		}
		fwrite($fp,$content);
	}

	function generatePeersFile($resellerid){
		global $db,$config;

		if ($config['system']['sipfile'] == '')	return;

		$query = "SELECT id FROM accountgroup WHERE resellerid = $resellerid";
		$group_list = $db->query($query);
		$content = '';
		while	($group_list->fetchInto($group)){

			$query = "SELECT * FROM clid WHERE groupid = ".$group['id']." ORDER BY clid ASC";
			$clid_list = $db->query($query);

			while	($clid_list->fetchInto($row)){
				$content .= "[".$row['clid']."]\n";
				foreach ($config['sipbuddy'] as  $key=>$value){
					if ($key != '' && $value != '')
						$content .= "$key = $value\n";
				}
				$content .= "secret = ".$row['pin']."\n";
				$content .= "callerid = \"".$row['clid']."\" <".$row['clid'].">\n\n";
			}
		}

		$filename = $config['system']['sipfile']."_$resellerid.conf";

		$fp=fopen($filename,"w");
		if (!$fp){
			print "file: $filename open failed, please check the file permission";
			exit;
		}
		fwrite($fp,$content);
	}

	function checkPeerStatus($groupid,$peers){
		$curChans =& astercc::getCurChan($groupid);
		$status =  array();
		while ($curChans->fetchInto($list)) {
			// 检查src或者dst是否在peers里
			if (astercc::array_exist($list['src'], $peers) || astercc::array_exist($list['dst'], $peers)){
				$status[$list['src']] = $list;
				$status[$list['src']]['direction'] = 'outbound';
			}else{
				// 使用srcchan作为src
				if (ereg("\/(.*)-", $list['srcchan'], $myAry) ){
					$status[$myAry[1]] = $list;
					$status[$myAry[1]]['direction'] = 'outbound';
				}
			}
			$status[$list['dst']] = $list;
			$status[$list['dst']]['direction'] = 'inbound';
		}
		return $status;
	}

	function array_exist($value,$ary){
		foreach ($ary as $val){
			if ($val == $value){
				return true;
			}
		}
		return false;
	}

	function creditDigits($credit)
	{
		return number_format($credit,2);
	}

	function setCreditLimit($channel,$creditlimit){
		global $db;
		$query = "UPDATE curcdr SET creditlimit = '$creditlimit' WHERE srcchan = '$channel' ";
		//astercc::events($query);
		$res = $db->query($query);
		return $db->affectedRows();
	}

	function setStatus($clid,$status){
		global $db;
		$query = "UPDATE clid SET status = $status, addtime = now()  WHERE clid = '$clid'";
		//astercc::events($query);
		$res = $db->query($query);
		//print_r($res);
		//print $query;
		//exit;
		return $db->affectedRows();
	}

	function getCallback($groupid){
		global $db;
		$query = "SELECT * FROM curcdr WHERE groupid = $groupid AND LEFT(srcchan,6) = 'Local/'";
		//astercc::events($query);
		$res = $db->query($query);
		return $res;
	}

	function insertNewCallback($f){
		global $db;
		$sql= "INSERT INTO callback SET "
				."lega='".$f['lega']."', "
				."legb='".$f['legb']."', "
				."credit='".$f['credit']."',"
				."groupid='".$f['groupid']."', "
				."addtime= now() ";
		$res =& $db->query($sql);
		//astercc::events($query);
		return $res;
	}

	function getCurChan($groupid){
		global $db;
		$query = "SELECT * FROM curcdr WHERE groupid = $groupid";
		#$condition = '';
		#foreach ($peers as $peer){
		#	$condition .= " src = '".$peer."' OR";
		#}
		#$query .= substr($condition,0,-2); // delete the last "AND"
		//astercc::events($query);
		$res = $db->query($query);
		return $res;
	}

	function getAll($table,$field = '', $value = ''){
		global $db;
		if (trim($field) != '' && trim($value) != ''){
			$query = "SELECT * FROM $table WHERE $field = '$value' ";
		}else{
			$query = "SELECT * FROM $table ";
		}
		echo $query;
		exit;
		//astercc::events($query);
		$res = $db->query($query);
		return $res;
	}

	function getCurLocalChan($chan, $groupid){
		global $db;
		$query = "SELECT * FROM curcdr WHERE srcchan LIKE '$chan%' AND groupid = $groupid ORDER BY starttime ASC";
//		print $query;
//		exit;
	//	astercc::events($query);
		$res = $db->query($query);
		return $res;
	}

	function events($event = null)
	{
		if(LOG_ENABLED){
		//	$now = date("Y-M-d H:i:s");
   		//$fd = fopen (FILE_LOG,'a');
		//	$log = $now." ".$_SERVER["REMOTE_ADDR"] ." - $event \n";
	   	//fwrite($fd,$log);
   		//fclose($fd);
		}
	}

	function searchRate($dst,$groupid, $resellerid, $tbl = 'myrate',$type)
	{
		global $db;
		$dst = trim($dst);
		if ($groupid == '' || $groupid == '-1') 
		{
			#print "invalid identity";
			return;
		}
		
		if($type == "prefix")
		{
		$sql = "SELECT * FROM $tbl WHERE groupid =$groupid AND resellerid =$resellerid AND dialprefix=$dst " ;
	   
	
			$rates = & $db->getRow($sql);
            // echo $rates['id']; exit;
			if($rates['id'] != '')
			{ // echo "hi"; exit;
				return $rates;
				
			}else
			{  $c = strlen($dst);
				 while($c != 0 )
				 {
				$sql = "SELECT * FROM $tbl WHERE groupid =$groupid AND resellerid =$resellerid AND dialprefix='".trim(substr($dst,0,$c))."' " ;
                 $res = mysql_query($sql) ;
				 $nr =  mysql_num_rows($res);
				 if($nr != 0)
                            {$rates = & $db->getRow($sql);
                           // echo $rates['id']; exit;
                                 return $rates;
                            	break;
                            }
					  $c = $c - 1;
                           			   
			    }
			    }
			}
		
			
		
		else
		{

			$sql = "SELECT * FROM $tbl WHERE groupid = $groupid AND resellerid = $resellerid AND destination = '".$dst."'";
			$rates = & $db->getRow($sql);

			if($rates['id'] != '')
			{ 
				return $rates;
				
			}
			elseif($groupid != 0 && $resellerid != 0)
			{
				return astercc::searchRate($dst,"0","0",$tbl,'dest');
			//	echo "1122hi"; exit;
			}
			else
			{
				return;
			}
		}
	}



function setBilled($id,$payment,$costomerid,$discount)
{
		global $db,$config;
		// move the record from mycdr to historycdr
		// get the record from mycdr
		$sql = "SELECT * FROM mycdr WHERE id = $id ";
		//astercc::events($sql);
		$cdr = &$db->getRow($sql);
		// tax
		$queryres = "SELECT mytax.amount FROM mytax WHERE groupid = '".$cdr['groupid']."'";
				$resultres = mysql_query($queryres);
				$rowres = mysql_fetch_array($resultres);
				$tax = number_format($rowres['amount'],2);
				$calstax= 1+ ($tax/100);
		    
		$dest = $cdr['dst'];
		 //Getting the context of the country
                                        $queryc = "select context from accountgroup where id='".$cdr['groupid']."' and resellerid='".$cdr['resellerid']."'";
                                        $resc = mysql_query($queryc);
                                        $rowc = mysql_fetch_array($resc);
                                        $context = $rowc['context'];
                                        if($context[0] == "0")
                                        {
                                                if($context[1] == "0")
                                                {
                                                        //do nothing
                                                }
                                                else
                                                {
                                                        $context = "0".$context;
                                                }
                                        }
                                        else
                                        {
                                                $context = "00".$context;
                                        }


                                        //check whether international number or not
                                        if ($dest[0] == 0)
                                        {
                                                if($dest[1] == 0)
                                                {
                                                        //do nothing
                                                }
                                                else
                                                {
                                                        $dest = substr($dest,1); //cut first '0'
                                                        $dest = $context .$dest;
                                                }
                                        }
                                        else
                                        {
                                                $dest = $context.$dest;
                                        }
		    
		    
		    // callshop rate 

		$c = strlen($dest);
	while($c != 0 )
	{	
                            $queryphi = " select dialprefix,rateinitial,destination,billingblock,connectcharge,initblock from callshoprate where groupid='".$cdr['groupid']."' and resellerid = '".$cdr['resellerid']."' and dialprefix='".trim(substr($dest,0,$c))."'";  
					
                            $res = mysql_query($queryphi) ;
							$nr =  mysql_num_rows($res);
					
                            if($nr != 0)
                                {
                               	$row = mysql_fetch_array($res);
                               	$selling = $row['rateinitial'];
                        	    $callbuy=$row['rateinitial'];
                        	    $itecspre=$row['dialprefix'];
								$sellingpersec = $selling/60;
								//echo $itecspre;
								//exit;
								
								$billingBlockR = $row['billingblock'];
								$rateperb = $sellingpersec * $row['billingblock'];
							    $destination = $row['destination'];
								$connectcharge = $row['connectcharge'];
								
								$initblock = $row['initblock'];
							   	break;
                                }
                                $c = $c - 1;

                  }
					                                       		    
				$sellPulse =floor($cdr['billsec'] / $row['billingblock']);	//pulse rate for the we calculated
					$ctest = $cdr['billsec']%$row['billingblock'];
					if($ctest != 0)
					{
						$sellPulse = $sellPulse + 1;
					}
			     
								
					$pselling = $rateperb * $sellPulse;				
					
					
					//calculate if billing seconds not equal to zero
					
					if($cdr['billsec'] != 0)
                        {
                                             
							if($cdr['billsec'] >= $initblock)
								{ 
									if ($initblock!=0)
										{
											$pselling = $pselling + $connectcharge;
										}
								}
						
						}
						$grandprice1 =$pselling;
                	 $grandprice12=round(($grandprice1* $calstax),3);
                                      
                  
					
					
					$selling = "";
					$pselling ="";
					$destination="";
					$row['rateinitial']=0;
					$row['billingblock']=0;
					$connectcharge = 0;
					$initblock = 0;
								
              $grandprice1 = number_format($grandprice1,2);
           
			        
            		
		
		
		//ends here
//calculating coustomer rate 	

// special calulation

 $itecsphi = " SELECT rateinitial,destination,billingblock,connectcharge,initblock from myrate where groupid='".$cdr['groupid']."' and resellerid = '".$cdr['resellerid']."' and dialprefix='".$itecspre."'";  
	                      $resphi = mysql_query($itecsphi) ;
							$nr =  mysql_num_rows($resphi);
					
                           if($nr != 0)
                                {
                               	$rowite = mysql_fetch_array($resphi);
                               	 $billsec=$cdr['billsec'];
                               	 $rateinitial = $rowite['rateinitial'];
	                           $destinat=$rowite['destination'];
		                      $initblock=$rowite['initblock'];
		                      $billingblock = 	$rowite['billingblock'];
		                     $selling =$rowite['rateinitial'];
                        	 $sellingpersec = $selling/60;
							 $billingBlockR =$rowite['billingblock'];
								$rateperb = $sellingpersec * $rowite['billingblock'];
							    $destination =$rowite['destination'];
								$connectcharge =	$rowite['connectcharge'];
								$initblock = 	$rowite['initblock'];
								
							}
								
								
					
//ends here special calculation


else
{

	
		if (!is_array($cdr['memo']))
		{
			$memo = split("\n",$cdr['memo'],4);
			if ( $memo[0] != '')
			{
				foreach ($memo as $val)
				{
					$tmp = split(":",$val);
					$rate[$tmp[0]] = $tmp[1];
				}
			}
		}
		
		
		
   		                    $billsec=$cdr['billsec'];
	                        $rateinitial = $rate['rateinitial'];
	                        $destinat=$cdr['destination'];
		                      $initblock	 = $rate['initblock'];
		                      $billingblock = $rate['billingblock'];
		                     $selling = $rate['rateinitial'];
                        	 $sellingpersec = $selling/60;
							 $billingBlockR = $rate['billingblock'];
								$rateperb = $sellingpersec * $rate['billingblock'];
							    $destination = $rate['destination'];
								$connectcharge = $rate['connectcharge'];
								$initblock = $rate['initblock'];
					
					}
							
			        $sellPulse =floor($billsec / $billingBlockR);	
					$ctest = $billsec%$billingBlockR;
					if($ctest != 0)
					{
						$sellPulse = $sellPulse + 1;
					}
			     
								
					$pselling = $rateperb * $sellPulse;				
					
					
					//calculate if billing seconds not equal to zero
					if($billsec  != 0)
                                        {
                                             
						if($billsec >= $initblock)
						{ 
						if($initblock!=0)
						{
							$pselling = $pselling + $connectcharge;
						}
						}
						
						
						$grandprice +=$pselling;
                	
                                                
                                        }
				
					
					
            
           
				$grandtotal = $grandprice;
				$grandtotal = round($grandtotal,2);
				
			

		//ends here
		       
			
			
			
			 
			

		//insert the record to historycdr
		if($config['system']['useHistoryCdr'] == 1)
		{ if($cdr['billsec']!=0)
		   {
			$sql = "INSERT INTO historycdr SET calldate = '".$cdr['calldate']."', src = '".$cdr['src']."', `dst` = '".$dest."', `channel` = '".$cdr['channel']."', `dstchannel` = '".$cdr['dstchannel']."',`didnumber` = '".$cdr['didnumber']."', `duration` = '".$cdr['duration']."', `billsec` = '".$cdr['billsec']."', `disposition` = '".$cdr['disposition']."', `accountcode` = '".$cdr['accountcode']."', `userfield` = 'BILLED', `srcuid` = '".$cdr['srcuid']."', `dstuid` = '".$cdr['dstuid']."', `calltype` = '".$cdr['calltype']."', `credit` = '".$grandtotal."', `callshopcredit` = '".$grandprice12."', `resellercredit` = '".$cdr['resellercredit']."', `groupid` = '".$cdr['groupid']."', `resellerid` = '".$cdr['resellerid']."', `userid` = '".$cdr['userid']."', `destination` = '".$destinat."', `memo` = '".$cdr['memo']."',buyingrate='".$callbuy."',sellingrate='".round($rateinitial,2)."',billingblock='".$billingblock."',connectcharge='".$connectcharge."',initalblock='".$initblock."',tax='".$tax."',customerid = $costomerid, discount = $discount ,payment='".$payment."'";

		}

		}else {
			$sql = "UPDATE mycdr SET userfield = 'BILLED' ,customerid = $costomerid, discount = $discount , payment='".$payment."' WHERE id = $id ";
		}
		//echo $sql;exit;
		//astercc::events($sql);
		$cdr = &$db->query($sql);

		// remove the record from mycdr
		if($config['system']['useHistoryCdr'] == 1){
			$sql ="DELETE FROM mycdr WHERE id =$id ";
			//astercc::events($sql);
			$res = $db->query($sql);
		}
		return $credit;
}

	function readUnbilled($peer,$leg = null,$groupid){
		global $db;

		if ($leg == null){
			$query = "SELECT * FROM mycdr WHERE (src = '$peer' OR dst = '$peer') AND userfield = 'UNBILLED' AND groupid = $groupid ORDER BY calldate";
		}else{
			/*
			$query = 'SELECT * FROM cdr WHERE 
				src = "'.$peer.' AND dst="'.$leg.'"" 
				AND userfield = "UNBILLED" ORDER BY calldate';
				*/
			$query = "SELECT * FROM mycdr WHERE channel LIKE 'Local/$peer%' AND src = '$leg' AND userfield = 'UNBILLED' AND groupid = $groupid ORDER BY calldate";
			//	print $query;
			//	exit;
		}
		//astercc::events($query);
		$res = $db->query($query);
		return $res;
	}

function readAll($resellerid, $groupid, $peer, $sdate = null , $edate = null){
	global $db,$config;
	/*
	if ($peer == 'callback'){
		if ($_SESSION['curuser']['usertype'] == 'admin')
			$query = "SELECT * FROM mycdr WHERE LEFT(channel,6) = 'Local/' ";
		else if ( $_SESSION['curuser']['usertype'] == 'reseller' )
			$query = "SELECT * FROM mycdr WHERE resellerid = ".$_SESSION['curuser']['resellerid']." AND LEFT(channel,6) = 'Local/' ";
		else
			$query = "SELECT * FROM mycdr WHERE groupid = ".$_SESSION['curuser']['groupid']." AND LEFT(channel,6) = 'Local/' ";
	}else{
		if ($_SESSION['curuser']['usertype'] == 'admin')
			$query = "SELECT * FROM mycdr WHERE src LIKE '$peer%' ";
		else if ( $_SESSION['curuser']['usertype'] == 'reseller' )
			$query = "SELECT * FROM mycdr WHERE src LIKE '$peer%' AND resellerid = ".$_SESSION['curuser']['resellerid']." ";
		else
			$query = "SELECT * FROM mycdr WHERE src LIKE '$peer%' AND groupid = ".$_SESSION['curuser']['groupid']." ";
	}
	*/
	if($config['system']['useHistoryCdr'] == 1)
	{
		$query = "SELECT * FROM historycdr WHERE 1 ";
	}else{
		$query = "SELECT * FROM mycdr WHERE 1 ";
	}

		if ($resellerid != '' and $resellerid != '0'){
			$query .= " AND resellerid = $resellerid ";
		}else{
			$query .= " AND resellerid != -1 ";
		}

		if ($groupid != '' and $groupid != '0'){
			$query .= " AND groupid = $groupid ";
		}else{
			$query .= " AND groupid != -1 ";
		}

		if ($peer != '' and $peer != '0'){
			if ($peer == "-1"){
				$query .= " AND LEFT(channel,6) = 'Local/' ";
			}else{
				$query .= " AND (src = '$peer' OR dst = '$peer') ";
			}
		}

		if ($sdate != null){
			$query .= " AND calldate >= '$sdate' ";
		}

	if ($edate != null){
		$query .= " AND calldate <= '$edate' ";
	}

	$query .= " ORDER BY calldate";
	//print $query;exit;
	//astercc::events($query);
	$res = $db->query($query);
	return $res;
}
	function readReport($resellerid, $groupid, $booth, $sdate, $edate, $groupby = '',$orderby='',$limit=''){
		global $db,$config;
		$table = 'mycdr';
		if($config['system']['useHistoryCdr'] == 1)
		{
			$table = 'historycdr';
		}

		if ($groupby == ""){
			$query = "SELECT count(*) as recordNum, sum(billsec) as seconds, sum(credit) as credit, sum(callshopcredit) as callshopcredit, sum(resellercredit) as resellercredit FROM $table WHERE calldate >= '$sdate' AND  calldate <= '$edate' ";
		}else{
			$query = "SELECT count(*) as recordNum, sum(billsec) as seconds, sum(credit) as credit, sum(callshopcredit) as callshopcredit, sum(resellercredit) as resellercredit, $groupby FROM $table WHERE calldate >= '$sdate' AND  calldate <= '$edate' ";
		}
		
		if ( ($groupid == '' || $groupid == 0) && ($_SESSION['curuser']['usertype'] == 'groupadmin' || $_SESSION['curuser']['usertype'] == 'operator')){
			$groupid = $_SESSION['curuser']['groupid'];
		}

		if ( ($resellerid == '' || $resellerid == 0) && $_SESSION['curuser']['usertype'] == 'reseller' ){
			$resellerid = $_SESSION['curuser']['resellerid'];
		}

		if ($resellerid != 0 && $resellerid != '')
			$query .= " AND resellerid = $resellerid ";
		else
			$query .= " AND resellerid != -1 ";

		if ($groupid != 0 && $groupid != '')
			$query .= " AND groupid = $groupid ";
		else
			$query .= " AND groupid != -1 ";

		if ($booth != 0 && $booth != ''){
			if ($booth == '-1'){
				$query .= " AND LEFT(channel,6) = 'Local/' ";
			}else{
				$query .= " AND src = '$booth' OR dst = '$booth'";
			}
		}		
		#exit;
		if ($groupby != ""){
			$query .= " GROUP BY $groupby";
		}
		if ($orderby != ""){
			$query .= " ORDER BY $orderby desc";
		}
		if ($limit == "limit"){
			$query .= " limit 0,10 ";
		}

		//astercc::events($query);
		$res = $db->query($query);
		return $res;
	}

	function readAnsweredNum($resellerid, $groupid, $booth, $sdate, $edate){
		global $db,$config;
		$table = 'mycdr';
		if($config['system']['useHistoryCdr'] == 1){
			$table = 'historycdr';
		}

		$query = "SELECT count(*) as answeredNum FROM $table WHERE disposition = 'ANSWERED' AND calldate >= '$sdate' AND  calldate <= '$edate' ";
				
		if ( ($groupid == '' || $groupid == 0) && ($_SESSION['curuser']['usertype'] == 'groupadmin' || $_SESSION['curuser']['usertype'] == 'operator')){
			$groupid = $_SESSION['curuser']['groupid'];
		}

		if ( ($resellerid == '' || $resellerid == 0) && $_SESSION['curuser']['usertype'] == 'reseller' ){
			$resellerid = $_SESSION['curuser']['resellerid'];
		}

		if ($resellerid != 0 && $resellerid != '')
			$query .= " AND resellerid = $resellerid ";
		else
			$query .= " AND resellerid != -1 ";

		if ($groupid != 0 && $groupid != '')
			$query .= " AND groupid = $groupid ";
		else
			$query .= " AND groupid != -1 ";

		if ($booth != 0 && $booth != ''){
			if ($booth == '-1'){
				$query .= " AND LEFT(channel,6) = 'Local/' ";
			}else{
				$query .= " AND src = '$booth' OR dst = '$booth'";
			}
		}		
		#exit;
		//print $query;exit;
	//	astercc::events($query);
		$res = $db->getOne($query);
		return $res;
	}

	function readAmount($id,$peer = null, $sdate = null, $edate = null, $field = 'credit'){
		global $db;
		$curYear = Date("Y");
		$curMonth = Date("m");

		if ($sdate == null){
			//$sdate = "$curYear-$curMonth-01 00:00:00";
		}

		if ($edate == null){
			//$edate = "$curYear-$curMonth-31 23:59:59";
		}

		if ($field == 'resellercredit'){
			if ($peer == null)
				$query = "SELECT SUM($field) FROM historycdr WHERE resellerid = $id ";
			else
				$query = "SELECT SUM($field) FROM historycdr WHERE resellerid = $id ";
		}else{
			if ($peer == null)
				$query = "SELECT SUM($field) FROM historycdr WHERE groupid = $id ";
			else
				$query = "SELECT SUM($field) FROM historycdr WHERE groupid = $id ";
		}

		if ($sdate != null)
			$query .= " AND calldate >= '$sdate' ";

		if ($edate != null)
			$query .= " AND calldate <= '$edate' ";
	//	astercc::events($query);
		$one = $db->getOne($query);
		return $one;
	}


	function readRateDesc($memo) //readRateDescchec
	{
		global $locate;
		if (!is_array($memo))
		{
			$memo = split("\n",$memo,4);
			if ( $memo[0] != '')
			{
				foreach ($memo as $val)
				{
					$tmp = split(":",$val);
					$rate[$tmp[0]] = $tmp[1];
				}
			}else
			{
				return "";
			}
		}
		else
		{
			$rate = $memo;
		}
		if ($rate['initblock'] != 0)
		{
			$desc .= number_format($rate['connectcharge'],2). ' '.$locate->Translate("After").' ' . $rate['initblock'] . ' '.$locate->Translate("seconds").' <br/>';
		}
		if ($rate['billingblock'] != 0)
		{
            $taxsel= $rate['rateinitial']  ;
         $taxsel=$taxsel/60;
           $taxsel=$taxsel* $rate['billingblock'];
    	$taxsel=number_format(round($taxsel,2),2);
	    $desc .= $taxsel. ' '.$locate->Translate("per").' ' . $rate['billingblock'] . ' '.$locate->Translate("seconds");

	
		}
		return $desc;
	}


function readRateDescchec($memo) //readRateDescchec
	{
		global $locate;
		if (!is_array($memo))
		{
			$memo = split("\n",$memo,4);
			if ( $memo[0] != '')
			{
				foreach ($memo as $val)
				{
					$tmp = split(":",$val);
					$rate[$tmp[0]] = $tmp[1];
				}
			}else
			{
				return "";
			}
		}
		else
		{
			$rate = $memo;
		}
		if ($rate['initblock'] != 0)
		{
			$desc .= number_format($rate['connectcharge'],2). ' '.$locate->Translate("After").' ' . $rate['initblock'] . ' '.$locate->Translate("seconds").' <br/>';
		}
		if ($rate['billingblock'] != 0)
		{
            $taxsel= $rate['rateinitial']  ;
         $taxsel=$taxsel/60;
           $taxsel=$taxsel* $rate['billingblock'];
    	$taxsel=number_format(round($taxsel,2),2);
	    $desc .= $taxsel. ' '.$locate->Translate("per").' ' . $rate['billingblock'] . ' '.$locate->Translate("seconds");

	
		}
		return $desc;
	}





	function readField($table,$field,$identity,$value)
	{
		global $db;
		if (is_numeric($value))
			$query = "SELECT $field FROM $table WHERE $identity = $value";
		else
			$query = "SELECT $field FROM $table WHERE $identity = '$value'";
		$one = $db->getOne($query);
		return $one;
	}

	function readRecord($table,$identity,$value){
		global $db;
		if (is_numeric($value))
			$query = "SELECT * FROM $table WHERE $identity = $value";
		else
			$query = "SELECT * FROM $table WHERE $identity = '$value'";
		$row = $db->getRow($query);
		return $row;
	}

	function calculatePrice($billsec,$rate)
	{      

		$destination = $rate['destination'];
		$rateinitial = $rate['rateinitial'];
		$initblock	 = $rate['initblock'];
		$billingblock = $rate['billingblock'];
		echo $destination.$rateinitial;
		exit;
		                     $selling = $rate['rateinitial'];
                        	 $sellingpersec = $selling/60;
							 $billingBlockR = $rate['billingblock'];
								$rateperb = $sellingpersec * $rate['billingblock'];
							    $destination = $rate['destination'];
								$connectcharge = $rate['connectcharge'];
								$initblock = $rate['initblock'];
							
		
	 	           $sellPulse =floor($billsec / $rate['billingblock']);	
					$ctest = $billsec%$rate['billingblock'];
					if($ctest != 0)
					{
						$sellPulse = $sellPulse + 1;
					}
			     
								
					$pselling = $rateperb * $sellPulse;				
					
					
					//calculate if billing seconds not equal to zero
					if($billsec  != 0)
                                        {
                                             
						if($billsec >= $initblock)
						{ 
						if($initblock!=0)
						{
							$pselling = $pselling + $connectcharge;
						}
						}
						
						
						$grandprice +=$pselling;
                	
                                                
                                        }
				
					
					
              //$grandprice = number_format($grandprice,3);
           
				$grandtotal = $grandprice;
				$grandtotal = round($grandtotal,2);
				$grandtotal = number_format($grandtotal,2);
			
            
	                       
            
		
		
		
		
		
		
		
/*
		if ($billsec > 0 )
		{
	
			$price += $rate['connectcharge'];
			//$billsec -= $rate['initblock'];
	

			if ($billsec > 0)
			{
				if ($rate['billingblock'] != 0)
				{
					if ($billsec % $rate['billingblock'] != 0 )
						$billblock = intval($billsec / $rate['billingblock']) + 1;
					else
						$billblock = intval($billsec / $rate['billingblock']);
				}else
				{
				}
					
				$price += $billblock * ($rate['billingblock'] * $rate['rateinitial']/60);
			}
		}
*/


//return 123;

		return $grandtotal;
	}

	function calculateLimitSec($creditLimit,$rate)
	{

		$destination = $rate['destination'];
		$rateinitial = $rate['rateinitial'];
		$initblock	 = $rate['initblock'];
		$billingblock = $rate['billingblock'];
		
		if ($billsec > 0 )
		{
	
			//$price += $rate['connectcharge'];
		//	$billsec = $rate['initblock'];
	

			if ($billsec > 0)
			{
				if ($rate['billingblock'] != 0)
				{
					if ($billsec % $rate['billingblock'] != 0 )
						$billblock = intval($billsec / $rate['billingblock']) + 1;
					else
						$billblock = intval($billsec / $rate['billingblock']);
				}else{
				}
					
				$price += $billblock * ($rate['billingblock'] * $rate['rateinitial']/60);
			}
		}
        
		return $price;
	}
	
	function readReportPie($resellerid, $groupid, $booth, $sdate, $edate, $groupby = '',$orderby=''){
		global $db,$config;
		$table = 'mycdr';
		if($config['system']['useHistoryCdr'] == 1){
			$table = 'historycdr';
		}
       if ($resellerid == 0 || $resellerid == ''){
			$query = "SELECT count(*) as recordNum, sum(billsec) as seconds, sum(credit) as credit, sum(callshopcredit) as callshopcredit, sum(resellercredit) as resellercredit, resellerid as gid  FROM $table WHERE calldate >= '$sdate' AND  calldate <= '$edate' ";
		}
		else{
			if ($groupid == 0 || $groupid == ''){
				$query = "SELECT count(*) as recordNum, sum(billsec) as seconds, sum(credit) as credit, sum(callshopcredit) as callshopcredit, sum(resellercredit) as resellercredit, groupid as gid FROM $table WHERE calldate >= '$sdate' AND  calldate <= '$edate' ";
				}else{
				$query = "SELECT count(*) as recordNum, sum(billsec) as seconds, sum(credit) as credit, sum(callshopcredit) as callshopcredit, sum(resellercredit) as resellercredit, src as gid FROM $table WHERE calldate >= '$sdate' AND  calldate <= '$edate' ";	
				}
		}
				
		if ( ($groupid == '' || $groupid == 0) && ($_SESSION['curuser']['usertype'] == 'groupadmin' || $_SESSION['curuser']['usertype'] == 'operator')){
			$groupid = $_SESSION['curuser']['groupid'];
		}

		if ( ($resellerid == '' || $resellerid == 0) && $_SESSION['curuser']['usertype'] == 'reseller' ){
			$resellerid = $_SESSION['curuser']['resellerid'];
		}

		if ($resellerid != 0 && $resellerid != '')
			$query .= " AND resellerid = $resellerid ";
		else
			$query .= " AND resellerid != -1 ";

		if ($groupid != 0 && $groupid != '')
			$query .= " AND groupid = $groupid ";
		else
			$query .= " AND groupid != -1 ";

		if ($booth != 0 && $booth != ''){
			if ($booth == '-1'){
				$query .= " AND LEFT(channel,6) = 'Local/' ";
			}else{
				$query .= " AND src = '$booth' OR dst = '$booth'";
			}
		}		
	
		if ($resellerid == 0 || $resellerid == ''){
			$query .= " group by resellerid ";
		}
		else{
			if ($groupid == 0 || $groupid == ''){
			$query .= " group by groupid ";
			}else{
			$query .= " group by src ";	
			}
		}
		
		if ($orderby != ""){
			$query .= " ORDER BY $orderby desc";
		}
		
		//astercc::events($query);
		$res = $db->query($query);
		return $res;
	}

	function searchRateForShortUpdate($groupid, $resellerid){
		global $db;

		$sql = "SELECT myrate.id as mid,myrate.dialprefix as mdialprefix,myrate.destination as mdestination,myrate.connectcharge as mconnectcharge,myrate.initblock as minitblock,myrate.rateinitial as mrateinitial,myrate.rateinitial as mrateinitial,myrate.billingblock as mbillingblock,myrate.groupid as mgroupid,myrate.resellerid as mresellerid,callshoprate.id as cid,callshoprate.dialprefix as cdialprefix,callshoprate.connectcharge as cconnectcharge,callshoprate.initblock as cinitblock,callshoprate.rateinitial as crateinitial,callshoprate.rateinitial as crateinitial,callshoprate.billingblock as cbillingblock,callshoprate.groupid as cgroupid,callshoprate.resellerid as cresellerid FROM myrate LEFT JOIN callshoprate ON myrate.dialprefix = callshoprate.dialprefix WHERE callshoprate.dialprefix != '' AND myrate.dialprefix != 'default' ";
		
		//astercc::events($sql);
		$rates = & $db->query($sql);
		
		$allprefix = array();
		$ratelist = array();
		while ($rates->fetchInto($list)) {
			
			if(in_array($list['mdialprefix'],$allprefix)){

				if($list['cgroupid'] != $gruopid && $list['cresellerid'] != $resellerid){
					echo $list['cresellerid'].$groupid;
					continue;
				}else{
					foreach($allprefix as $key => $value){
						if($list['mdialprefix'] == $value){
							$curkey = $key;
							break;
						}
					}
					if($ratelist[$curkey]['cresellerid'] == $resellerid){
						if($ratelist[$curkey]['cgroupid'] == $gruopid){
							continue;
						}
					}
					$ratelist[$curkey] = $list;
					continue;	
				}
			}
			$ratelist[] = $list;
			$allprefix[] = $list['mdialprefix'];
		}
		return $ratelist;
	}
}
?>
