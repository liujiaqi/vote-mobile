<?php
	require_once("db.php");
    $result = query("select * from parameter");
    $parameter = mysql_fetch_array($result);
    
	$id = intval($_GET['id']);
	
	$sql = "SELECT * FROM candidate WHERE id=$id";
	$con = mysql_query($sql);
	$res = mysql_fetch_array($con);
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, maximum-scale=1.0, user-scalable=no, target-densitydpi=device-dpi ,initial-scale= 0.5"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="Author" content="贱圈 -TiH-liujiaqi" />
    <script src="scripts/js/jquery-1.7.2.min.js"></script>
    <link href="scripts/css/vote.css" rel="stylesheet">
    <script src="scripts/js/vote.js" type="text/javascript"></script>
    <title><?php echo $parameter['title'];?></title>
</head>
<body style="background-color: #fbfbfb">
    <header id="index_header"><a href="index.php" id="passage_header">&lt;返回</a>资料介绍</header>
    <section id="passage_header_body">
        <img width="143" height="204" src="photo/<?php echo $res['photo'];?>" id="passage_header_body_photo">
        <div class="passage_main_short_header"><?php echo $res['name'];?></div>
        <span class="passage_main_short_body">
            <strong>【个人简介】</strong><?php echo $res['summary'];?><br>
            <strong>【详细资料】</strong><?php echo $res['description'];?>
        </span>
    </section>
</body>
</html>