<?php
	ini_set('display_errors',1);
	
	$this_hostName = '';
	$this_userName = '';
	$this_password = '';
	$this_databaseName = '';
	
	$tconnection = mysql_connect($this_hostName,$this_userName,$this_password) or die('数据库连接失败');
	mysql_select_db($this_databaseName, $tconnection) or die('数据库选择失败');
	mysql_query("set names utf8", $tconnection);
	
	function query($sqlStatement){
		global $tconnection;
		if($query = mysql_query($sqlStatement)) {
			return $query;
		}
	}
?>