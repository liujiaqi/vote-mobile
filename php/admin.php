<?php
    $vperpage = 15;
    $cperpage = 5;
    $uperpage = 10;
    
    session_start();
	//error_reporting(E_ALL);
	ini_set('display_errors', '1');
    if(isset($_POST["login"])){
        if($_POST["name"] == "admin" && $_POST["pwd"]=="0nline"){
            $_SESSION['admin'] = "admin";
        }
    }
    if(isset($_GET['logout'])){
        unset($_SESSION['admin']);
        header("Location: admin.php");
        exit;
    }
    
    if(isset($_SESSION['admin'])){
        require_once("db.php");
        if(isset($_GET['id'])){
            $vid = intval($_GET['id']);
            if($vid == 0){
                if(!query("insert into parameter values()")) die("对不起，服务器出现问题！");
                header("Location: admin.php?id=".mysql_insert_id());
                exit;
            }
            $cpage = intval($_GET['cpage']);
            if (($cpage==NULL) || ($cpage < 1)) $cpage = 1;
            $res = mysql_query("select count(*) from candidate where vid = $vid and state = 1");
            $row = mysql_fetch_array($res);
            $total = $row['count(*)'];
            $ctotle_page = ceil($total/$cperpage);
            if ($cpage > $ctotle_page) $cpage = $ctotle_page;
            $cbegin = $cperpage * ($cpage - 1);
            
            $upage = intval($_GET['upage']);
            if (($upage==NULL) || ($upage < 1)) $upage = 1;
            $res = mysql_query("select count(*) from view_user where vid = $vid");
            $row = mysql_fetch_array($res);
            $total = $row['count(*)'];
            $utotle_page = ceil($total/$uperpage);
            if ($upage > $utotle_page) $upage = $utotle_page;
            $ubegin = $uperpage * ($upage - 1);
            
            if(isset($_POST['param'])){
                //die("$vid");
                $title = poststr('title');
                $total = postint('total');
                $begintime = poststr('begintime');
                $endtime = poststr('endtime');
                $sql = "update parameter set title='".$title."', total=$total, begintime='".$begintime."', endtime='".$endtime."' where id = $vid";
                //die($sql);
                if(!query($sql)) die("更新参数失败！");
            }
            $result = query("select * from parameter where id = $vid");
            $parameter = mysql_fetch_array($result);
        }
        else{
            $vpage = intval($_GET['page']);
            if (($vpage==NULL) || ($vpage < 1)) $vpage = 1;
            $res = mysql_query("select count(*) from parameter where state = 1");
            $row = mysql_fetch_array($res);
            $total = $row['count(*)'];
            $vtotle_page = ceil($total/$vperpage);
            if ($vpage > $vtotle_page) $vpage = $vtotle_page;
            $vbegin = $vperpage * ($vpage - 1);
        }
    }
    
    function url($cpage,$upage){
        global $vid, $ctotle_page, $utotle_page;
        if($cpage < 1) $cpage = 1;
        if ($cpage > $ctotle_page) $cpage = $ctotle_page;
        if($upage < 1) $upage = 1;
        if ($upage > $utotle_page) $upage = $utotle_page;
        return $_SERVER['PHP_SELF']."?id=$vid&cpage=$cpage&upage=$upage";
    }
    
?>
<!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <meta name="description" content="">
    <meta name="author" content="-TiH-liujiaqi from SDU">

    <title>投票管理系统</title>

    <script type="text/javascript" src="scripts/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="scripts/js/bootstrap.min.js" charset="UTF-8"></script>
    <script type="text/javascript" src="scripts/js/admin.js" charset="UTF-8"></script>
    <link href="scripts/css/bootstrap.min.css" rel="stylesheet">
    <link href="scripts/css/bootstrap-datetimepicker.css" rel="stylesheet" media="screen">
    <link href="scripts/css/admin.css" rel="stylesheet" media="screen">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="scripts/js/html5shiv.min.js"></script>
      <script src="scripts/js/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
<?php  if(isset($_SESSION['admin'])){
                if(isset($_GET['id'])){
?>
    <nav class="navbar navbar-fixed-bottom">
        <div class="container">
            <form class="navbar-form" method="post">
                <label for="vote-name">投票名称</label>
                <input type="text" id="vote-name" name="title" class="form-control" placeholder="投票名称" <?php if(isset($parameter['title'])) echo 'value="'.$parameter['title'].'"'; ?> required autofocus>
                <label for="total-per">每人投票数</label>
                <input type="number" id="total-per" name="total" class="form-control" placeholder="每人投票数" <?php if(isset($parameter['total'])) echo 'value="'.$parameter['total'].'"'; ?> required>
                <label for="begin-time">开始时间</label>
                <input type="text" id="begin-time" name="begintime" class="form-control form_datetime" <?php if(isset($parameter['begintime'])) echo 'value="'.$parameter['begintime'].'"'; ?> >
                <label for="end-time">结束时间</label>
                <input type="text" id="end-time" name="endtime" class="form-control form_datetime" <?php if(isset($parameter['endtime'])) echo 'value="'.$parameter['endtime'].'"'; ?> >
                <a class="btn btn-sm btn-info" href="export.php?id=<?php echo $parameter['id'];?>" target="_blank">导出结果</a>
                <button class="btn btn-sm btn-success" name="param" type="submit">保存</button>
                <a class="btn btn-sm btn-warning" href="admin.php">取消</a>
                <button class="btn btn-sm btn-danger" type="button" onclick="flush(<?php echo $parameter['id'];?>)">清空</button>
            </form>
        </div>
    </nav>
    
    <div class="container">
    <div class="page-header"><h1>投票管理系统<small>Powered by SDU Online</small></h1></div>
      <div class="panel panel-default">
            <div class="panel-heading clearfix">
              <h3 class="panel-title pull-left">候选人</h3>
              <button class="btn btn-xs btn-primary pull-right" type="button" onclick="add_cand()"><span class="glyphicon glyphicon-plus"></span></button>
            </div>
            <form action="uploadimg.php" id="hidfor" method="post" enctype="multipart/form-data" target="hidfr" style="display:none;">
                <input id="file" name="file"  type="file" onchange="$('#hidfor').submit();" >
            </form>
            <iframe name="hidfr" style="display:none;"></iframe>
            <div class="panel-body">
            
                <div class="list-group cand-list">
                
                    <div class="list-group-item cand-template" style="display:none;">
                        <div class="row">
                          <form>
                            <div class="col-lg-2">
                                <input type="hidden" name="method" value="addcand">
                                <input type="hidden" name="vid" value="<?php echo $vid;?>" >
                                <input type="hidden" name="cid">
                                <img class="img-thumbnail" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDIwMCAyMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjxkZWZzLz48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI0VFRUVFRSIvPjxnPjx0ZXh0IHg9IjczLjUiIHk9IjEwMCIgc3R5bGU9ImZpbGw6I0FBQUFBQTtmb250LXdlaWdodDpib2xkO2ZvbnQtZmFtaWx5OkFyaWFsLCBIZWx2ZXRpY2EsIE9wZW4gU2Fucywgc2Fucy1zZXJpZiwgbW9ub3NwYWNlO2ZvbnQtc2l6ZToxMHB0O2RvbWluYW50LWJhc2VsaW5lOmNlbnRyYWwiPjIwMHgyMDA8L3RleHQ+PC9nPjwvc3ZnPg==" data-holder-rendered="true">
                                <input type="hidden" name="photo" value="" onchange="modi_updatebtn(this)">
                                <button class="btn btn-sm btn-default" autocomplete="off" data-loading-text="上传中..." data-success-text="上传成功" data-error-text="上传失败" type="button" onclick="up_photo(this);">上传</button>
                            </div>
                            <div class="col-lg-3">
                                <input type="text" class="form-control cand-name" name="name" placeholder="姓名" onchange="modi_updatebtn(this)" required>
                                <textarea class="form-control" name="summary" rows="5" placeholder="简介" onchange="modi_updatebtn(this)"></textarea>
                            </div>
                            <div class="col-lg-5">
                                <textarea class="form-control" name="description" rows="7" placeholder="描述" onchange="modi_updatebtn(this)"></textarea>
                            </div>
                          </form>
                            <div class="col-lg-2 col-candbtn">
                                <button type="button" data-loading-text="保存中..." data-success-text="保存成功" data-error-text="保存失败" class="btn btn-primary save_btn" autocomplete="off" onclick="up_cand(this)">保存</button>
                                <button class="btn btn-danger" type="button" onclick="remove_item(this)">取消</button>
                            </div>
                        </div>
                    </div>
                

<?php  if($cbegin>=0){
                $result = query("select * from candidate where vid=$vid and state = 1 limit $cbegin, $cperpage");
                while($row = mysql_fetch_array($result)){?>
                    <div class="list-group-item">
                        <div class="row">
                          <form>
                            <div class="col-lg-2">
                                <input type="hidden" name="method" value="modicand">
                                <input type="hidden" name="cid" value="<?php echo $row['id'];?>" >
                                <img class="img-thumbnail"  src="photo/<?php echo $row['photo'];?>" data-holder-rendered="true">
                                <input type="hidden" name="photo" value="<?php echo $row['photo'];?>" onchange="modi_updatebtn(this)">
                                <button class="btn btn-sm btn-default" autocomplete="off" data-loading-text="上传中..." data-success-text="上传成功" data-error-text="上传失败" type="button" onclick="up_photo(this);">上传</button>
                            </div>
                            <div class="col-lg-3">
                                <input type="text" class="form-control cand-name" value="<?php echo $row['name'];?>" placeholder="姓名" name="name" onchange="modi_updatebtn(this)" required>
                                <textarea class="form-control" name="summary" rows="5" placeholder="简介" onchange="modi_updatebtn(this)"><?php echo $row['summary'];?></textarea>
                            </div>
                            <div class="col-lg-5">
                                <textarea class="form-control" name="description" rows="7" placeholder="描述" onchange="modi_updatebtn(this)"><?php echo $row['description'];?></textarea>
                            </div>
                          </form>
                            <div class="col-lg-2 col-candbtn">
                                <button type="button" data-loading-text="保存中..." data-success-text="保存成功" data-error-text="保存失败" class="btn btn-primary save_btn" autocomplete="off" onclick="up_cand(this)">保存</button>
                                <button class="btn btn-danger" type="button" onclick="del_cand(this)">删除</button>
                            </div>
                        </div>
                    </div>
<?php       }
            }?>


                    <ul class="pagination">
<?php  $btnb =$ctotle_page>5 ? $ctotle_page-$cpage>2 ? $cpage>3 ? $cpage-2 : 1 : $ctotle_page-4 : 1;
            if($cpage>1) echo '<li><a href="'.url($cpage-1, $upage).'">&laquo;</a></li>';
            else echo '<li class="disabled"><a>&laquo;</a></li>';
            for($i = $btnb; $i<=$btnb+4 && $i<=$ctotle_page ; $i++){
                echo $i==$cpage ? '<li class="active"><a>'.$i.'</a></li>' : '<li><a href="'.url($i, $upage).'">'.$i.'</a></li>';
            }
            if($cpage<$ctotle_page) echo '<li><a href="'.url($cpage+1, $cpage).'">&raquo;</a></li>';
            else echo '<li class="disabled"><a>&raquo;</a></li>';
?>
                    </ul>
                    
                </div>
            </div>
      </div>
      
      
      <div class="panel panel-default" id="user-panel">
            <div class="panel-heading clearfix">
              <h3 class="panel-title pull-left">投票人</h3>
              <button class="btn btn-xs btn-primary pull-right" type="button" onclick="add_user()"><span class="glyphicon glyphicon-plus"></span></button>
              <button class="btn btn-xs btn-primary pull-right" type="button" data-toggle="collapse" data-target="#existusers" >添加已有用户</button>
            </div>
            <div class="panel-body">
            
                <div class="panel panel-default">
                  <div class="panel-heading" role="tab">
                    <h4 class="panel-title">
                      <a data-toggle="collapse" href="#existusers">其他投票中已存在的用户</a>
                    </h4>
                  </div>
                  <div id="existusers" class="panel-collapse collapse" role="tabpanel">
                    <div class="panel-body">
<?php          $result = query("select * from `user` where state=1 and `user`.id not in (select uid from v_u where vid=$vid)");
                while($row = mysql_fetch_array($result)){?>
                    <span class="extusr"><a onclick="sel_usr(this, <?php echo "$vid, ".$row['id'];?>)"><?php echo $row['name'];?>(<?php echo $row['realname'];?>)</a></span>
<?php           }?>
                    </div>
                  </div>
                </div>
                <div class="list-group user-list">

                    <div class="list-group-item user-template" style="display:none;">
                        <div class="row">
                            <div class="col-lg-8 form-inline">
                              <form>
                                <input type="hidden" name="method" value="addusr" >
                                <input type="hidden" name="vid" value="<?php echo $vid;?>" >
                                <input type="hidden" name="uid" >
                                <label>用户名
                                <input type="text" class="form-control userinfo" name="uname" placeholder="用户名" onchange="modi_updatebtn(this)"></label>
                                <label>真实姓名
                                <input type="text" class="form-control userinfo" name="realname" placeholder="真实姓名" onchange="modi_updatebtn(this)"></label>
                                <label>密码
                                <input type="password" class="form-control userinfo" name="pwd" placeholder="密码" onchange="modi_updatebtn(this)"></label>
                              </form>
                            </div>
                            <div class="col-lg-4">
                                <button type="button" data-loading-text="保存中..." data-success-text="保存成功" data-error-text="保存失败" class="btn btn-primary save_btn" autocomplete="off" onclick="up_usr(this)">保存</button>
                                <button class="btn btn-danger" type="button" onclick="remove_item(this)">取消</button>
                            </div>
                        </div>
                    </div>

<?php   if($ubegin>=0){
                 $result = query("select * from view_user where vid=$vid limit $ubegin, $uperpage");
                 while($row = mysql_fetch_array($result)){?>
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col-lg-8 form-inline">
                              <form>
                                <input type="hidden" name="method" value="modiusr" >
                                <input type="hidden" name="vid" value="<?php echo $vid;?>" >
                                <input type="hidden" name="uid" value="<?php echo $row['id'];?>" >
                                <label>用户名
                                <input type="text" name="uname" class="form-control userinfo" placeholder="用户名" value="<?php echo $row['name'];?>" onchange="modi_updatebtn(this)" readonly></label>
                                <label>真实姓名
                                <input type="text" name="realname" class="form-control userinfo" placeholder="真实姓名" value="<?php echo $row['realname'];?>" onchange="modi_updatebtn(this)"></label>
                                <label>密码
                                <input type="password" name="pwd" class="form-control userinfo" placeholder="密码" onchange="modi_updatebtn(this)" ></label>
                              </form>
                            </div>
                            <div class="col-lg-4">
                                <button type="button" data-loading-text="保存中..." data-success-text="保存成功" data-error-text="保存失败" class="btn btn-primary save_btn" autocomplete="off" onclick="up_usr(this)">保存</button>
                                <button class="btn btn-danger" type="button" onclick="rm_usr(this)">移除</button>
                                <button type="button" data-loading-text="清除中..." data-success-text="清除成功" data-error-text="清除失败" class="btn btn-danger" autocomplete="off" onclick="clr_usr(this)">清除投票数据</button>
                            </div>
                        </div>
                    </div>
<?php       }
            }?>
            
                    <ul class="pagination">
<?php  $btnb =$utotle_page>5 ? $utotle_page-$upage>2 ? $upage>3 ? $upage-2 : 1 : $utotle_page-4 : 1;
            if($upage>1) echo '<li><a href="'.url($cpage, $upage-1).'#user-panel">&laquo;</a></li>';
            else echo '<li class="disabled"><a>&laquo;</a></li>';
            for($i = $btnb; $i<=$btnb+4 && $i<=$utotle_page ; $i++){
                echo $i==$upage ? '<li class="active"><a>'.$i.'</a></li>' : '<li class="active"><a href="'.url($cpage, $i).'#user-panel">'.$i.'</a></li>';
            }
            if($upage<$utotle_page) echo '<li><a href="'.url($cpage,$upage+1).'#user-panel">&raquo;</a></li>';
            else echo '<li class="disabled"><a>&raquo;</a></li>';
?>
                    </ul>
                </div>
            </div>
      </div>
      
        <div style="text-align:center;">Copyright &copy; 2015 www.online.sdu.edu.cn All Rights Reserved.</div>
    </div>

    <script type="text/javascript" src="scripts/js/bootstrap-datetimepicker.min.js" charset="UTF-8"></script>
    <script type="text/javascript" src="scripts/js/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
    <script type="text/javascript">
        $('.form_datetime').datetimepicker({
            language:  'zh-CN',
            pickerPosition: 'top-right',
            weekStart: 1,
            todayBtn:  1,
            todayHighlight: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            showMeridian: 1
        });
    </script>
<?php       }else{?>
    <div class="container">
    <div class="page-header"><h1>投票管理系统<small>Powered by SDU Online</small><a class="btn btn-sm  btn-info pull-right" href="admin.php?logout=1">退出系统</a></h1></div>
      <div class="panel panel-default" id="accordion">
            <div class="panel-heading">
              <h3 class="panel-title" style="display:inline;">投票</h3><a class="btn btn-xs btn-primary pull-right" type="button" href="admin.php?id=0"><span class="glyphicon glyphicon-plus"></span></a>
            </div>
            
<?php      if($vbegin>=0){
                    $res = query("select * from parameter where state = 1 limit $vbegin, $vperpage");
                    while($row = mysql_fetch_array($res)){
                    $vid = $row['id'];
                    $result2 = query("select (select count(*) from view_vote  where vid =$vid) as nvote, (select count(*) from candidate where vid = $vid and state =1) as ncandi, (select count(distinct uid) from view_vote where vid =$vid) as nvoteu, (select count(*) from `view_user` where vid =$vid)  as nuser from dual");
                    $row2 = mysql_fetch_array($result2);
                    ?>
                  <div class="panel panel-default">
                    <div class="panel-heading clearfix" role="tab">
                      <h4 class="panel-title pull-left">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse_<?php echo $row['id'];?>">
                          <?php echo $row['title'];?>
                        </a>
                      </h4>
                      <button class="btn btn-sm btn-danger pull-right" type="button" onclick="del_vote(this, <?php echo $row['id'];?>)">删除</button>
                      <button class="btn btn-sm btn-danger pull-right" type="button" onclick="flush(<?php echo $row['id'];?>)">清除结果</button>
                      <a class="btn btn-sm btn-info pull-right" href="export.php?id=<?php echo $row['id'];?>" target="_blank">导出结果</a>
                      <a class="btn btn-sm btn-success pull-right" href="admin.php?id=<?php echo $row['id'];?>">修改</a>
                    </div>
                    <div id="collapse_<?php echo $row['id'];?>" class="panel-collapse collapse" role="tabpanel">
                      <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-10">
                                <table class="table table-bordered">
                                    <tr>
                                        <td class="bg-info"><b>开始时间</b></td>
                                        <td><?php echo $row['begintime'];?></td>
                                        <td class="bg-info"><b>结束时间</b></td>
                                        <td><?php echo $row['endtime'];?></td>
                                    </tr>
                                    <tr>
                                        <td class="bg-info"><b>候选人总数</b></td>
                                        <td><?php echo $row2['ncandi'];?></td>
                                        <td class="bg-info"><b>投票人数</b></td>
                                        <td><?php echo $row2['nvoteu'];?>/<?php echo $row2['nuser'];?></td>
                                    </tr>
                                    <tr>
                                        <td class="bg-info"><b>每人投票数</b></td>
                                        <td><?php echo $row['total'];?></td>
                                        <td class="bg-info"><b>投票总数</b></td>
                                        <td><?php echo $row2['nvote'];?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-lg-2">
                                <img class="img-thumbnail" src="qr.php?id=<?php echo $row['id'];?>">
                            </div>
                            </div>
                        </div>
                      </div>
                  </div>
<?php               }
                }?>

                    <div class="panel-body">
                        <ul class="pagination">
<?php  $btnb =$vtotle_page>5 ? $vtotle_page-$vpage>2 ? $vpage>3 ? $vpage-2 : 1 : $vtotle_page-4 : 1;
            if($vpage>1) echo '<li><a href="admin.php?page='.($vpage-1).'">&laquo;</a></li>';
            else echo '<li class="disabled"><a>&laquo;</a></li>';
            for($i = $btnb; $i<=$btnb+4 && $i<=$vtotle_page ; $i++){
                echo $i==$vpage ?  '<li class="active"><a>'.$i.'</a></li>' : '<li><a href="admin.php?page='.$i.'">'.$i.'</a></li>';
            }
            if($vpage<$vtotle_page) echo '<li><a href="admin.php?page='.($vpage+1).'">&raquo;</a></li>';
            else echo '<li class="disabled"><a>&raquo;</a></li>';
?>
                        </ul>
                    </div>
                    
                </div>
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4 class="panel-title">投票人</h4>
                  </div>
                  <div class="panel-collapse">
                    <div class="panel-body">
<?php          $result = query("select * from `user` where state = 1");
                while($row = mysql_fetch_array($result)){?>
                    <span class="extusr"><a><?php echo $row['name'];?>(<?php echo $row['realname'];?>)</a><a class="glyphicon glyphicon-minus-sign usr-rm-btn" onclick="del_usr(this, <?php echo $row['id'];?>)"></a></span>
<?php           }?>
                    </div>
                  </div>
                </div>
            </div>

<?php   }
            }else{?>
    <form class="form-signin" method="post">
        <h2 class="form-signin-heading">投票管理后台</h2>
        <?php if(isset($_POST['login'])) echo '<div class="alert alert-danger" role="alert">用户名或密码错误</div>';?>
        <label for="name" class="sr-only">用户名</label>
        <input type="text" id="name" class="form-control" name="name" <?php if(isset($_POST['name'])) echo('value="'.$_POST['name'].'"');?>  placeholder="用户名" required autofocus>
        <label for="pwd" class="sr-only">密码</label>
        <input type="password" id="pwd" class="form-control" name="pwd" placeholder="密码" required>
        <button class="btn btn-lg btn-primary btn-block" name="login" type="submit">登陆</button>
    </form>
    <div style="position:absolute; bottom:20px; right:20px;">Copyright &copy; 2015 www.online.sdu.edu.cn All Rights Reserved.</div>
<?php }?>
  </body>
</html>
