<?php
    session_start();
    if(!isset($_SESSION['admin'])) die("请您先登录！");
    
    require_once("db.php");
    
    if(isset($_POST['method'])){
        switch($_POST['method']){
            case "logout":
                session_destroy();
                break;
                
            case "flush":
                $vid = postint('vid');
                if(!query("update `vote` set state = 0 where vid=$vid")) die("清除投票数据失败！");
                else echo "T:";
                break;
                
            case "delvote":
                $vid = postint('vid');
                if(!query("update `parameter` set state = 0 where id=$vid")) die("删除投票失败！");
                else echo "T:";
                break;
                
            case "addcand":
                $name = poststr('name');
                $summary = poststr('summary');
                $rank = postint('rank');
                $photo = poststr('photo');
                $vid = postint('vid');
                if($name == "") die("候选人姓名不能为空");
                $sql = "insert into `candidate` (`name`,`summary`, `photo`,`rank`, `vid`)  values('".$name."', '".$summary."', '".$photo."', $rank, $vid)";
                if(!query($sql)) die("添加候选人失败！");
                else echo "T:".mysql_insert_id();
                break;
                
            case "modicand":
                $cid = postint('cid');
                $name = poststr('name');
                $summary = poststr('summary');
                $rank = postint('rank');
                $photo = poststr('photo');
                if($name == "") die("候选人姓名不能为空");
                
                $sql = "update `candidate` set `name` = '".$name."', `summary` = '".$summary."', `photo`='".$photo."', `rank` = $rank where id = $cid";
                
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
                $vid = postint('vid');
                if($name == "" || $realname == "" || $pwd =="") die("全不能为空");
                
                $result = query("select count(*) from `view_user` where `name` = '".$name."' and vid = $vid");
                $res = mysql_fetch_array($result);
                if($res['count(*)']) die("用户名已存在");
                
                $sql = "insert into `user` (`name`,`realname`,`password`)  values('".$name."', '".$realname."', MD5('".$pwd. "'))";
                if(!query($sql)) die("添加用户失败1！");
                $uid = mysql_insert_id();
                if(!query("insert into v_u (vid, uid) values($vid, $uid)")) die("添加用户失败2！");
                else echo "T:".$uid;
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
                
            case "rmusr":
                $vid = postint('vid');
                $uid = postint('uid');
                if(!query("delete from `v_u` where vid=$vid and uid=$uid")) die("移除用户失败！");
                else echo "T:";
                break;
                
            case "selusr":
                $vid = postint('vid');
                $uid = postint('uid');
                if(query("insert into `v_u` (vid, uid) values($vid, $uid)")) echo "T:";
                else die("选择用户失败！");
                break;
                
            case "delusr":
                $uid = postint('uid');
                if(!query("update `user` set state = 0 where id = $uid")) die("删除用户失败！");
                else echo "T:";
                break;
                
            case "clrusr":
                $uid = postint('uid');
                if(!query("update `vote` set state = 0 where uid = $uid")) die("清除用户数据失败！");
                else echo "T:";
                break;

        }
    }
?>
