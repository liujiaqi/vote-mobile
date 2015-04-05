var btn;
$(document).ready(function(){
    $(".list_main_btn").toggle(function(){
            $(this).find("a").animate({"marginLeft":"0"},800,'swing');
            $(this).find("a").removeClass("vote_select");
            $(this).find("a").addClass("vote_selected");
            $("form").prepend($(this).find("input").clone());
            btn = this;
            number();
        },
        function(){
            $(this).find("a").animate({"marginLeft":"30%"},800,'swing');
            $(this).find("a").removeClass("vote_selected");
            $(this).find("a").addClass("vote_select");
            $("form").find("input[value="+$(this).find("input").val()+"]").remove();
            number();
        }
    );
});
function number(){
    var num = $("#list_main").find(".vote_selected").length;
    var _num
    $("#list_footer_btn1").find("span").html(num);
    if(num < total){
        $("#list_footer_btn1").css({"display":"block"});
    }
    if(num == total){
        $("#list_footer_btn1").css({"display":"none"});
    }
    if(num > total){
        $(btn).click();
        $("#list_footer_btn1").find("span").html(num);
        setTimeout("bar()",200);
        setTimeout("foo()",400);
        setTimeout("bar()",600);
        setTimeout("foo()",800);
    }
}
function foo(){$(btn).css({'border-color':'#2cacdb'})}
function bar(){$(btn).css({"border-color":"#ff2146"})};
$(document).ready(function () {
    var viewport = document.querySelector("meta[name=viewport]");
    var winWidths = $(window).width();
    var densityDpi = 640 / winWidths;
    densityDpi = densityDpi > 1 ? 300 * 640 * densityDpi / 640 : densityDpi;
    if (isWeixin()) {
        viewport.setAttribute('content', 'width=640, user-scalable=no,  maximum-scale=1, target-densityDpi='+densityDpi);
    } else {
        viewport.setAttribute('content', 'width=640, user-scalable=no,  maximum-scale=1');
        window.setTimeout(function () {
            viewport.setAttribute('content', 'width=640, user-scalable=no,  maximum-scale=1');
        }, 1000);
    }
    function isWeixin() {
        var ua = navigator.userAgent.toLowerCase();
        if (ua.match(/MicroMessenger/i) == "micromessenger") {
            return true;
        } else {
            return false;
        }
    }
})