
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

var toupimg, toupinp;
function up_photo(btn){
    $('#file').click();
    toupimg = $(btn).parent().find("img");
    toupinp = $(btn).parent().find("input[name='photo']");
}
//<script>parent.up_back('OK','201501010000.png');</script>
function up_back(info, src){
    if(info == "OK"){
        toupimg.attr("src", "photo/"+src);
        toupinp.val(src);
    }else{
        console.log(info);
    }
}

function up_cand(btn){
    var usr_item = $(btn).button("loading").parent().parent();
    var usrdata = usr_item.serialize();
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
                    console.log(resp); 
                }
            },
            error:function (resp){ 
                $(btn).button("error").removeClass("btn-primary").addClass("btn-danger");
                console.log(resp); 
            }
    });
}

function del_cand(btn){
    if(confirm("确定删除该候选人？")){
        var usr_item = $(btn).parent().parent();
        usr_item.find("input[name='method']").val("delcand");
        var usrdata = usr_item.serialize();
        $.ajax({
                type: "POST",
                async: false,
                url: "admin_ajax.php",
                data: usrdata,
                success: function (resp) {
                    if(resp.indexOf("T:")==0){
                        usr_item.parent().parent().remove();
                    }
                    else{
                        console.log(resp); 
                    }
                },
                error:function (resp){ 
                    console.log(resp); 
                }
        });
    }
}

function up_usr(btn){
    var usr_item = $(btn).button("loading").parent().parent();
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
                    console.log(resp); 
                }
            },
            error:function (resp){ 
                $(btn).button("error").removeClass("btn-primary").addClass("btn-danger");
                console.log(resp); 
            }
    });
}

function del_usr(btn){
    if(confirm("确定删除该用户？")){
        var usr_item = $(btn).parent().parent();
        usr_item.find("input[name='method']").val("delusr");
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
                        console.log(resp); 
                    }
                },
                error:function (resp){ 
                    console.log(resp); 
                }
        });
    }
}
