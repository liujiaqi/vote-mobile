var btn;
$(document).ready(function(){
    $(".list_main_btn").toggle(function(){
            $(this).find("a").animate({"marginLeft":"0"},800,'swing');
            $(this).find("a").removeClass("vote_select");
            $(this).find("a").addClass("vote_selected");
            btn = this;
            number();
        },
        function(){
            $(this).find("a").animate({"marginLeft":"30%"},800,'swing');
            $(this).find("a").removeClass("vote_selected");
            $(this).find("a").addClass("vote_select");
            number();
        }
    );
});
function number(){
    var num = $("#list_main").find(".vote_selected").length;
    var _num
    $("#list_footer_btn1").find("span").html(num);
    if(num < 2){
        $("#list_footer_btn1").css({"display":"block"});
    }
    if(num == 2){
        $("#list_footer_btn1").css({"display":"none"});
    }
    if(num > 2){
        $(btn).click();
        num = num - 1;
        $("#list_footer_btn1").find("span").html(num);
        setTimeout("bar()",200);
        setTimeout("foo()",400);
        setTimeout("bar()",600);
        setTimeout("foo()",800);
    }
}
function foo(){$(btn).css({'border-color':'#2cacdb'})}
function bar(){$(btn).css({"border-color":"#ff2146"})};