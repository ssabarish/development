<?php
// database connection config
$dbHost = "localhost";
$dbUser = "root";
	$dbPwd = "";
	//$dataBase1 = "asteriskcdrdb";
	$dbName = "astercc";
mysql_connect ($dbHost, $dbUser, $dbPwd) or die ('MySQL connect failed. ' . mysql_error());
mysql_select_db($dbName) or die('Cannot select database. ' . mysql_error());
?>