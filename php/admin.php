<?php
    session_start();
	//error_reporting(E_ALL);
	ini_set('display_errors', '1');
    if(isset($_POST["login"])){
        if($_POST["name"] == "admin" && $_POST["pwd"]=="0nline"){
            $_SESSION['admin'] = "admin";
        }
    }
    
    if(isset($_SESSION['admin'])){
        require_once("db.php");
        
        if(isset($_POST['param'])){
            $title = mysql_real_escape_string(HTMLSpecialChars(isset($_POST['title']) ? $_POST['title'] : ""));
            $total = intval(isset($_POST['total']) ? $_POST['total'] : 0);
            $begintime = mysql_real_escape_string(isset($_POST['begintime']) ? $_POST['begintime'] : "");
            $endtime = mysql_real_escape_string(isset($_POST['endtime']) ? $_POST['endtime']:"");
            
            $sql = "update parameter set title='".$title."', total=$total, begintime='".$begintime."', endtime='".$endtime."' where id = 1";
            //die($sql);
            if(!query($sql)) die("更新参数失败！");
        }
        $result = query("select * from parameter");
        $parameter = mysql_fetch_array($result);
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
<?php  if(isset($_SESSION['admin'])){?>
  
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
                <button class="btn btn-sm btn-success" name="param" type="submit">保存</button>
                <button class="btn btn-sm btn-danger" type="button" onclick="logout()">取消</button>
            </form>
        </div>
    </nav>
    
    <div class="container">
    <div class="page-header"><h1>投票管理系统<small>Powered by SDU Online</small></h1></div>
      <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">候选人<button class="btn btn-xs btn-primary" type="button" style="float:right;" onclick="add_cand()">+</button></h3>
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
                            <div class="col-lg-2 col-candbtn">
                                <button type="button" data-loading-text="保存中..." data-success-text="保存成功" data-error-text="保存失败" class="btn btn-primary save_btn" autocomplete="off" onclick="up_cand(this)">保存</button>
                                <button class="btn btn-danger" type="button" onclick="remove_item(this)">取消</button>
                            </div>
                          </form>
                        </div>
                    </div>
                

<?php   $result = query("select * from candidate where state = 1");
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
                            <div class="col-lg-2 col-candbtn">
                                <button type="button" data-loading-text="保存中..." data-success-text="保存成功" data-error-text="保存失败" class="btn btn-primary save_btn" autocomplete="off" onclick="up_cand(this)">保存</button>
                                <button class="btn btn-danger" type="button" onclick="del_cand(this)">删除</button>
                            </div>
                          </form>
                        </div>
                    </div>
<?php   }?>


                    <ul class="pagination">
                      <li class="disabled"><a href="#">&laquo;</a></li>
                      <li class="active"><a href="#">1</a></li>
                      <li class="disabled"><a href="#">2</a></li>
                      <li class="disabled"><a href="#">3</a></li>
                      <li class="disabled"><a href="#">4</a></li>
                      <li class="disabled"><a href="#">5</a></li>
                      <li class="disabled"><a href="#">&raquo;</a></li>
                    </ul>
                    
                </div>
            </div>
      </div>
      
      
      <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">投票人<button class="btn btn-xs btn-primary" type="button" style="float:right;" onclick="add_user()">+</button></h3>
            </div>
            <div class="panel-body">
            
                <div class="list-group user-list">

                    <div class="list-group-item user-template" style="display:none;">
                        <div class="row">
                            <div class="col-lg-1"></div>
                            <div class="col-lg-9 form-inline">
                              <form>
                                <input type="hidden" name="method" value="addusr" >
                                <input type="hidden" name="uid" >
                                <label>用户名
                                <input type="text" class="form-control userinfo" name="uname" placeholder="用户名" onchange="modi_updatebtn(this)"></label>
                                <label>真实姓名
                                <input type="text" class="form-control userinfo" name="realname" placeholder="真实姓名" onchange="modi_updatebtn(this)"></label>
                                <label>密码
                                <input type="password" class="form-control userinfo" name="pwd" placeholder="密码" onchange="modi_updatebtn(this)"></label>
                              </form>
                            </div>
                            <div class="col-lg-2">
                                <button type="button" data-loading-text="保存中..." data-success-text="保存成功" data-error-text="保存失败" class="btn btn-primary save_btn" autocomplete="off" onclick="up_usr(this)">保存</button>
                                <button class="btn btn-danger" type="button" onclick="remove_item(this)">取消</button>
                            </div>
                        </div>
                    </div>

<?php   $result = query("select * from user where state = 1 and id <>1");
             while($row = mysql_fetch_array($result)){?>
                    <div class="list-group-item">
                        <div class="row">
                            <div class="col-lg-1"></div>
                            <div class="col-lg-9 form-inline">
                              <form>
                                <input type="hidden" name="method" value="modiusr" >
                                <input type="hidden" name="uid" value="<?php echo $row['id'];?>" >
                                <label>用户名
                                <input type="text" name="uname" class="form-control userinfo" placeholder="用户名" value="<?php echo $row['name'];?>" onchange="modi_updatebtn(this)" readonly></label>
                                <label>真实姓名
                                <input type="text" name="realname" class="form-control userinfo" placeholder="真实姓名" value="<?php echo $row['realname'];?>" onchange="modi_updatebtn(this)"></label>
                                <label>密码
                                <input type="password" name="pwd" class="form-control userinfo" placeholder="密码" onchange="modi_updatebtn(this)" ></label>
                              </form>
                            </div>
                            <div class="col-lg-2">
                                <button type="button" data-loading-text="保存中..." data-success-text="保存成功" data-error-text="保存失败" class="btn btn-primary save_btn" autocomplete="off" onclick="up_usr(this)">保存</button>
                                <button class="btn btn-danger" type="button" onclick="del_usr(this)">删除</button>
                            </div>
                        </div>
                    </div>
<?php   }?>
            
                    <ul class="pagination">
                      <li class="disabled"><a href="#">&laquo;</a></li>
                      <li class="active"><a href="#">1</a></li>
                      <li class="disabled"><a href="#">2</a></li>
                      <li class="disabled"><a href="#">3</a></li>
                      <li class="disabled"><a href="#">4</a></li>
                      <li class="disabled"><a href="#">5</a></li>
                      <li class="disabled"><a href="#">&raquo;</a></li>
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
<?php }else{?>
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
