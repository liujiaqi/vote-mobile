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
    
    function poststr($key){
        return mysql_real_escape_string(HTMLSpecialChars(isset($_POST[$key]) ? $_POST[$key] : ""));
    }
    function postint($key){
        return intval(isset($_POST[$key]) ? $_POST[$key] : 0);
    }
    
    function user_realip() {
        if ($ip = getenv('HTTP_CLIENT_IP'))
            return $ip;
        if ($ip = getenv('HTTP_X_FORWARDED_FOR'))
            return $ip;
        if ($ip = getenv('REMOTE_ADDR'))
            return $ip;
        return $HTTP_SERVER_VARS['REMOTE_ADDR'];
    }
    
?>