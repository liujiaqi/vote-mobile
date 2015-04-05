
function logout(){
    window.location="logout.php";
}

function add_cand(){
    $(".cand-list").prepend($(".cand-template").clone().removeClass("cand-template").css("display","block"));
}

function remove_item(btn){
    $(btn).parent().parent().parent().remove();
}

function modi_updatebtn(obj){
    $(obj).parent().parent().parent().parent().find(".save_btn").button("reset").removeClass("btn-success").removeClass("btn-danger").addClass("btn-primary");
}

function add_user(){
    $(".user-list").prepend($(".user-template").clone().removeClass("user-template").css("display","block"));
}

var upimgbtn;
function up_photo(btn){
    upimgbtn = $(btn).button("loading").removeClass("btn-default").removeClass("btn-error").removeClass("btn-success").addClass("btn-info");
    $('#file').click();
}
//<script>parent.up_back('OK','201501010000.png');</script>
function up_back(info, src){
    if(info == "OK"){
        upimgbtn.button("success").popover('destroy').removeClass("btn-default").removeClass("btn-error").removeClass("btn-info").addClass("btn-success");
        upimgbtn.parent().find("img").attr("src", "photo/"+src);
        upimgbtn.parent().find("input[name='photo']").val(src);
    }else{
        upimgbtn.button("error").removeClass("btn-default").removeClass("btn-success").removeClass("btn-info").addClass("btn-danger");
        $(upimgbtn).popover({ 'placement': 'bottom', 'content': info}).popover('show');
        console.log(info);
    }
}

function up_cand(btn){
    var usr_item = $(btn).button("loading").popover('destroy').parent().parent();
    var usrdata = usr_item.find("form").serialize();
    $.ajax({
            type: "POST",
            async: false,
            url: "admin_ajax.php",
            data: usrdata,
            success: function (resp) {
                if(resp.indexOf("T:")==0){
                    usr_item.find("input[name='cid']").val(resp.substring(2, resp.length));
                    usr_item.find("input[name='method']").val("modicand");
                    $(btn).next().attr("onclick","del_cand(this)").html("删除");
                    $(btn).button("success").removeClass("btn-primary").removeClass("btn-danger").addClass("btn-success");
                }
                else{
                    $(btn).button("error").removeClass("btn-primary").addClass("btn-danger");
                    $(btn).popover({ 'placement': 'top', 'content': resp}).popover('show');
                    console.log(resp); 
                }
            },
            error:function (resp){
                $(btn).button("error").removeClass("btn-primary").addClass("btn-danger");
                $(btn).popover({ 'placement': 'top', 'content': resp}).popover('show');
                console.log(resp); 
            }
    });
}

function del_cand(btn){
    if(confirm("确定删除该候选人？")){
        var usr_item = $(btn).popover('destroy').parent().parent();
        usr_item.find("input[name='method']").val("delcand");
        var usrdata = usr_item.find("form").serialize();
        $.ajax({
                type: "POST",
                async: false,
                url: "admin_ajax.php",
                data: usrdata,
                success: function (resp) {
                    if(resp.indexOf("T:")==0){
                        usr_item.parent().remove();
                    }
                    else{
                        $(btn).popover({ 'placement': 'top', 'content': resp}).popover('show');
                        console.log(resp); 
                    }
                },
                error:function (resp){
                    $(btn).popover({ 'placement': 'top', 'content': resp}).popover('show');
                    console.log(resp); 
                }
        });
    }
}

function up_usr(btn){
    var usr_item = $(btn).button("loading").popover('destroy').parent().parent();
    var usrdata = usr_item.find("form").serialize();
    $.ajax({
            type: "POST",
            async: false,
            url: "admin_ajax.php",
            data: usrdata,
            success: function (resp) {
                if(resp.indexOf("T:")==0){
                    usr_item.find("input[name='uid']").val(resp.substring(2, resp.length));
                    usr_item.find("input[name='uname']").attr("readonly","readonly");
                    usr_item.find("input[name='method']").val("modiusr");
                    $(btn).next().attr("onclick","del_usr(this)").html("删除");
                    $(btn).button("success").removeClass("btn-primary").removeClass("btn-danger").addClass("btn-success");
                }
                else{
                    $(btn).button("error").removeClass("btn-primary").addClass("btn-danger");
                    $(btn).popover({ 'placement': 'top', 'content': resp}).popover('show');
                    console.log(resp); 
                }
            },
            error:function (resp){
                $(btn).button("error").removeClass("btn-primary").addClass("btn-danger");
                $(btn).popover({ 'placement': 'top', 'content': resp}).popover('show');
                console.log(resp); 
            }
    });
}

function rm_usr(btn){
    if(confirm("确定移除该用户？")){
        var usr_item = $(btn).popover('destroy').parent().parent();
        usr_item.find("input[name='method']").val("rmusr");
        var usrdata = usr_item.find("form").serialize();
        $.ajax({
                type: "POST",
                async: false,
                url: "admin_ajax.php",
                data: usrdata,
                success: function (resp) {
                    if(resp.indexOf("T:")==0){
                        usr_item.parent().remove();
                    }
                    else{
                        $(btn).popover({ 'placement': 'top', 'content': resp}).popover('show');
                        console.log(resp); 
                    }
                },
                error:function (resp){
                    $(btn).popover({ 'placement': 'top', 'content': resp}).popover('show');
                    console.log(resp); 
                }
        });
    }
}

function sel_usr(btn, vid, uid){
    $.ajax({
            type: "POST",
            async: false,
            url: "admin_ajax.php",
            data: "method=selusr&vid="+vid+"&uid="+uid,
            success: function (resp) {
                if(resp.indexOf("T:")==0){
                    usr_item = $(".user-template").clone().removeClass("user-template").css("display","block");
                    usr = $(btn).html().replace(')','').split('(');
                    usr_item.find("input[name='uid']").val(""+uid);
                    usr_item.find("input[name='uname']").val(usr[0]);
                    usr_item.find("input[name='realname']").val(usr[1]);
                    usr_item.find("input[name='uname']").attr("readonly","readonly");
                    usr_item.find("input[name='method']").val("modiusr");
                    usr_item.find("button").eq(1).attr("onclick","del_usr(this)").html("删除");
                    $(".user-list").prepend(usr_item);
                    $(btn).parent().remove();
                }
                else{
                    alert("对不起，删除失败");
                    console.log(resp); 
                }
            },
            error:function (resp){
                alert("对不起，删除失败");
                console.log(resp); 
            }
    });
}

function del_usr(btn, uid){
    if(confirm("！！！该操作将从系统中删除此用户，请确认此用户不被需要参与任何一个投票！！！\r\n\r\n确定删除该用户？")){
        $.ajax({
                type: "POST",
                async: false,
                url: "admin_ajax.php",
                data: "method=delusr&uid="+uid,
                success: function (resp) {
                    if(resp.indexOf("T:")==0){
                        $(btn).parent().remove();
                    }
                    else{
                        alert("对不起，删除失败");
                        console.log(resp); 
                    }
                },
                error:function (resp){
                    alert("对不起，删除失败");
                    console.log(resp); 
                }
        });
    }
}

function clr_usr(btn){
    if(confirm("确定清除该用户的投票信息？")){
        var usr_item = $(btn).button("loading").parent().parent();
        usr_item.find("input[name='method']").val("clrusr");
        var usrdata = usr_item.find("form").serialize();
        $.ajax({
                type: "POST",
                async: false,
                url: "admin_ajax.php",
                data: usrdata,
                success: function (resp) {
                    if(resp.indexOf("T:")==0){
                        $(btn).button("success").removeClass("btn-danger").addClass("btn-success");
                    }
                    else{
                        $(btn).button("error").removeClass("btn-success").addClass("btn-danger");
                        console.log(resp); 
                    }
                },
                error:function (resp){ 
                    $(btn).button("error").removeClass("btn-success").addClass("btn-danger");
                    console.log(resp); 
                }
        });
    }
}


function flush(vid){
    if(confirm("确定清除所有投票信息？")){
        $.ajax({
                type: "POST",
                async: false,
                url: "admin_ajax.php",
                data: "method=flush&vid="+vid,
                success: function (resp) {
                    if(resp.indexOf("T:")==0){
                        alert("清除成功！");
                    }
                    else{
                        alert("清除失败！");
                        console.log(resp); 
                    }
                },
                error:function (resp){ 
                    alert("清除失败！");
                    console.log(resp); 
                }
        });
    }
}

function del_vote(btn, vid){
    if(confirm("确定删除该投票？")){
        var usr_item = $(btn).parent().parent();
        $.ajax({
                type: "POST",
                async: false,
                url: "admin_ajax.php",
                data: "method=delvote&vid="+vid,
                success: function (resp) {
                    if(resp.indexOf("T:")==0){
                        $(btn).parent().parent().remove();
                    }
                    else{
                        alert(resp);
                        console.log(resp); 
                    }
                },
                error:function (resp){
                    alert(resp);
                    console.log(resp); 
                }
        });
    }
}
