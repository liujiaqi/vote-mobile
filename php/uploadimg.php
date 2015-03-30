<?php
    session_start();
    if(!isset($_SESSION['admin']))die("请先登录！");
    header('Content-Type: text/html; charset=UTF-8');

$inputName='file';//表单文件域name
$attachDir='photo';//上传文件保存路径，结尾不要带/

$dirType = 1;//1:按天存入目录 2:按月存入目录 3:按扩展名存目录  建议使用按天存
$upExt = 'jpg,jpeg,gif,png,bmp';//上传扩展名
$msgType = 2;//返回上传参数的格式：1，只返回url，2，返回参数数组
ini_set('date.timezone','Asia/Shanghai');//时区

$err = "";
$msg = "''";

$localName='';
if(isset($_SERVER['HTTP_CONTENT_DISPOSITION'])&&preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i',$_SERVER['HTTP_CONTENT_DISPOSITION'],$info)){//HTML5上传
	
	$localName=urldecode($info[2]);
	$x = $localName;
		$f = strrpos($x, '.');
		$g = substr($x, $f + 1);
		$tem = date('YmdHis', time()).rand(100, 999).".".strtolower($g);
		$tempPath=$attachDir.'/'.$tem;
		file_put_contents($tempPath,file_get_contents("php://input"));
} else {
	$upfile= $_FILES[$inputName];
	if(!isset($upfile))$err='文件域的name错误';
	elseif(!empty($upfile['error'])){
		switch($upfile['error'])
		{
			case '1':
				$err = '文件大小超过了php.ini定义的upload_max_filesize值';
				break;
			case '2':
				$err = '文件大小超过了HTML定义的MAX_FILE_SIZE值';
				break;
			case '3':
				$err = '文件上传不完全';
				break;
			case '4':
				$err = '无文件上传';
				break;
			case '6':
				$err = '缺少临时文件夹';
				break;
			case '7':
				$err = '写文件失败';
				break;
			case '8':
				$err = '上传被其它扩展中断';
				break;
			case '999':
			default:
				$err = '无有效错误代码';
		}
	}
	elseif(empty($upfile['tmp_name']) || $upfile['tmp_name'] == 'none')$err = '无文件上传';
	else{
		$localName=$upfile['name'];
		$x = $localName;
		$f = strrpos($x, '.');
		$g = substr($x, $f + 1);
		$tem = date('YmdHis', time()).rand(100, 999).".".strtolower($g);
		$tempPath=$attachDir.'/'.$tem;
		move_uploaded_file($upfile['tmp_name'],$tempPath);
		$localName=$upfile['name'];
	}
}
if($err==''){
	$fileInfo=pathinfo($localName);
	$extension=$fileInfo['extension'];
	if(!preg_match('/^('.str_replace(',','|',$upExt).')$/i',$extension))
	{
		$err='上传文件扩展名必需为：'.$upExt;
		@unlink($tempPath);
	}
}

if($err==""){
    echo "<script>parent.up_back('OK','".$tem."');</script>";
}else echo "<script>parent.up_back('ERR','');</script>";
?>