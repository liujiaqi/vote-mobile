<?php
    session_start();
    
    if(isset($_SESSION['admin'])){
    
        require_once("db.php");
        
        if(isset($_POST['method'])){
            switch($_POST['method']){
                case "logout":
                    session_destroy();
                    break;
                    
                case "addcand":
                    $name = poststr('name');
                    $summary = poststr('summary');
                    $description = poststr('description');
                    $photo = poststr('photo');
                    if($name == "") die("候选人姓名不能为空");
                    $sql = "insert into `candidate` (`name`,`summary`,`description`, `photo`)  values('".$name."', '".$summary."', '".$description. "', '".$photo."')";
                    if(!query($sql)) die("添加候选人失败！");
                    else echo "T:".mysql_insert_id();
                    break;
                    
                case "modicand":
                    $cid = postint('cid');
                    $name = poststr('name');
                    $summary = poststr('summary');
                    $description = poststr('description');
                    $photo = poststr('photo');
                    if($name == "") die("候选人姓名不能为空");
                    
                    $sql = "update `candidate` set `name` = '".$name."', `summary` = '".$summary."', `description`='".$description."', `photo`='".$photo."' where id = $cid";
                    
                    if(!query($sql)) die("修改候选人失败！");
                    else echo "T:".$cid;
                    break;
                    
                case "delcand":
                    $cid = postint('cid');
                    if(!query("update `candidate` set state = 0 where id = $cid")) die("删除候选人失败！");
                    else echo "T:";
                    break;
                    
                case "addusr":
                    $name = poststr('uname');
                    $realname = poststr('realname');
                    $pwd = poststr('pwd');
                    if($name == "" || $realname == "" || $pwd =="") die("全不能为空");
                    
                    $result = query("select count(*) from `user` where `name` = '".$name."' and state = 1");
                    $res = mysql_fetch_array($result);
                    if($res['count(*)']) die("用户名已存在");
                    
                    $sql = "insert into `user` (`name`,`realname`,`password`)  values('".$name."', '".$realname."', MD5('".$pwd. "'))";
                    if(!query($sql)) die("添加用户失败！");
                    else echo "T:".mysql_insert_id();
                    break;
                    
                case "modiusr":
                    $uid = postint('uid');
                    $name = poststr('uname');
                    $realname = poststr('realname');
                    if($pwd = poststr('pwd'))
                        $sql = "update `user` set `name` = '".$name."', `realname` = '".$realname."', `password`=MD5('".$pwd."') where id = $uid";
                    else
                        $sql = "update `user` set `name` = '".$name."', `realname` = '".$realname."' where id = $uid";
                    if(!query($sql)) die("修改用户失败！");
                    else echo "T:".$uid;
                    break;
                    
                case "delusr":
                    $uid = postint('uid');
                    if(!query("update `user` set state = 0 where id = $uid")) die("删除用户失败！");
                    else echo "T:";
                    break;

            }
        }
    }
?>