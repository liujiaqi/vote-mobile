<?php
    session_start();
    if(!isset($_SESSION['admin'])) die("请您先登录！");
    include("phpqrcode.php");
    QRcode::png(dirname('http://'.$_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"]).'/index.php?id='.$_GET['id'],false,'M',12,2);
?>