<?php
    session_start();
	//error_reporting(E_ALL);
	ini_set('display_errors', '1');
	require_once("db.php");
    //$user_ip = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"]; 
    $user_ip = $_SERVER["REMOTE_ADDR"];
    $result = query("select * from parameter");
    $parameter = mysql_fetch_array($result);
    $now = date("Y-m-d H:i:s");
    
    if (isset($_POST['login'])){
        if($now > $parameter['begintime']){
            if($now < $parameter['endtime']){
                $tname = isset($_POST["name"])?$_POST["name"]:"";
                $tpwd = isset($_POST["pwd"])?$_POST["pwd"]:"";
                $sql = "select password,id from user where name = '".$_POST['name']."' and state = 1";
                $result = query($sql);
                if ($row = mysql_fetch_array($result)) {
                    if($row['password'] == md5($tpwd)){
                        $sql = "update user set lastlogin = now(), ip = '".$user_ip."' where name = '" . $tname . "'";
                        query($sql);
                        $_SESSION['uid'] = $row['id'];
                    }
                }
                $errinfo = "用户名或密码错误";
            }else{
                $errinfo = "对不起，投票已经结束";
            }
        }else{
            $errinfo = "对不起，投票还未开始";
        }
    }
    
    // 字符截断
	function limitStringLength($string,$chineseCharacterLength){
		$stt = "";
		$i = 0; $k = 0;
		$string = str_replace("\\\"", "\"", $string);
		while ($i < strlen($string)) {
			if (ord($string[$i]) >= 224) {
				$stt .= substr($string, $i, 3);
				$k += 1;
				$i += 3;
			} else if (ord($string[$i]) >= 192) {
				$stt .= substr($string, $i, 2);
				$k += 1;
				$i += 2;
			} else {
				$stt .= substr($string, $i, 1);
				if (ord($string[$i]) >= ord("A") && ord($string[$i]) <= ord("Z"))
					$k += 0.7;
				else
					$k += 0.5;
				$i += 1;
			}
			if ($k >= $chineseCharacterLength && $i < strlen($string)) {
				$stt .= '...';
				break;
			}
		}
		return $stt;
	}
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, maximum-scale=1.0, user-scalable=no, target-densitydpi=device-dpi ,initial-scale= 0.5"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="Author" content="贱圈 -TiH-liujiaqi" />
    <link href="scripts/css/vote.css" rel="stylesheet">
    <title><?php echo $parameter['title'];?></title>
</head>
<body>
    <header id="index_header"><?php echo $parameter['title'];?></header>
<?php if(isset($_POST['vote'])){
        if(isset($_SESSION['uid'])){
            $uid = intval($_SESSION['uid']);
            $result = query("select count(*) from vote where uid = $uid");
            if ($row = mysql_fetch_array($result)) {
                if($row['count(*)'] == 0){
                    if(count($_POST['cid']) == $parameter['total']){
                        $vals = array();
                        $cids = array();
                        foreach($_POST['cid'] as $cid){
                            $vals[] = "(".$uid.",".intval($cid).", now(), '".$user_ip."')";
                            $cids[]= intval($cid);
                        }
                        $sql1 = "update candidate set poll = poll+1 where id in (".implode(",", $cids).")";
                        $sql2 = "insert into vote values ".implode(",", $vals);
                        if(query($sql1)&&query($sql2)){
                            session_destroy();?>
            <div id="cons_logo"><img width="165" height="165" src="images/cons_co.png"></div>
            <div id="cons_words">您的投票已提交成功</div>
<?php                   }else{?>
            <div id="cons_logo"><img width="165" height="165" src="images/cons_inco.png"></div>
            <div id="cons_words">对不起，系统出现错误</div>
<?php                   }
                    }else{?>
            <div id="cons_logo"><img width="165" height="165" src="images/cons_inco.png"></div>
            <div id="cons_words">对不起，您的投票数目不正确</div>
<?php               }
                }else{session_destroy();?>
            <div id="cons_logo"><img width="165" height="165" src="images/cons_inco.png"></div>
            <div id="cons_words">对不起，不能重复投票</div>
<?php           }
            }
        }else{?>
    <div id="cons_logo"><img width="165" height="165" src="images/cons_inco.png"></div>
    <div id="cons_words">请您登录后再投票</div>
<?php   }?>
    <div style=" margin: 0 auto; width: 240px;">
        <a id="cons_back" onclick="history.go(-1)">返回</a>
    </div>
<?php }else if(isset($_SESSION['uid'])){?>
    <script type="text/javascript">var total = <?php echo $parameter['total'];?>;</script>
    <script src="scripts/js/jquery-1.7.2.min.js"></script>
    <script src="scripts/js/vote.js"></script>
    <section id="list_main">
        <ul>
<?php   $result = query("select * from candidate");
        $first_cand = true;
        while($row = mysql_fetch_array($result)){?>
            <li<?php if($first_cand){ echo ' style="border: 0"';$first_cand=false;}?>>
                <a class="list_main_photo"><img width="96" height="137" src="photo/<?php echo $row['photo'];?>"></a>
                <div class="list_main_short">
                    <div class="list_main_short_header"><?php echo $row['name'];?></div>
                    <div class="list_main_short_body"><strong>【提要】</strong>：<?php echo limitStringLength($row['summary'],40);?><a href="detail.php?id=<?php echo $row['id'];?>" class="list_main_more">查看更多</a></div>
                </div>
                <div class="list_main_btn">
                    <a href="javascript:void(0)" class="list_main_btn_slide vote_select"></a>
                    <input type="hidden" name="cid[]" value="<?php echo $row['id'];?>" />
                </div>
            </li>
<?php   }?>
        </ul>
    </section>
    <footer id="list_footer">
        <form method="post">
            <a class="list_footer_btn" id="list_footer_btn1">您已经选择<span>0</span>/<script>document.write(total);</script>人</a>
            <button type="submit" name="vote" class="list_footer_btn">提交投票</button>
        </form>
    </footer>
<?php } else{?>
    <section id="index_main">
        <header id="login_header"></header>
        <form method="post">
            <div id="login_box-bg" style="height: 238px;">
                <!-- fieldset class="login_box_part" data-role="fieldcontain">
                    <div class="ui-select" style="margin: 0; padding-top: 45px;">
                        <div class="ui-btn" id="login_type">
                            <span id="login_arrow">请选择登陆方式</span>
                            <select name="login_type">
                                <option value="mon">星期一</option>
                                <option value="tue">星期二</option>
                                <option value="wed">星期三</option>
                                <option value="thu">星期四</option>
                            </select>
                        </div>
                    </div>
                </fieldset -->
                <a class="login_box_part">
                    <input type="text" name="name" <?php if(isset($_POST['name'])) echo('value="'.$_POST['name'].'"');?> placeholder="用户名">
                </a>
                <a class="login_box_part" style="border: 0">
                    <input type="password" name="pwd" placeholder="密码">
                </a>
            </div>
            <div id="rmb_pass">
                <!-- input type="checkbox" checked="true" -->
                <!-- a href="javascript:void(0)">记住密码</a -->
                <span><?php if(isset($_POST['login'])) echo $errinfo;?>&nbsp;</span>
                <a class="right" href="javascript:void(0)">注册账户</a>
                <a class="right">/</a>
                <a class="right" href="javascript:void(0)">忘记密码</a>
            </div>
            <button type="submit" name="login" id="login_submit">登陆</button>
        </form>
    </section>
<?php }?>
</body>
</html>