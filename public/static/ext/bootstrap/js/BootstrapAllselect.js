// JavaScript Document
/*全选*/
$(".mod-operate-detail").css("display", "none"); //加载的时候先让div隐藏
$(".mod-operate .ico20").css("display", "none"); //加载的时候先让小三角隐藏
$(function () {
    //    $("#ckall").click(function () {
    //        $("input[name='sub']").prop("checked", this.checked);
    //    });
    //    $("input[name='sub']").click(function () {
    //        var $subs = $("input[name='sub']");
    //        $("#ckall").prop("checked", $subs.length == $subs.filter(":checked").length ? true : false);
    //    });
    //点击功能开始
    //   $(".mod-operate li>.a-btn").click(function () {
    //        if ($(this).siblings("i").css("display") == "block") {//判断条件：如果小三角是展开的就执行：
    //            $(".mod-operate-detail:eq(" + $(this).attr('data') + ")").css("display", "none");
    //            $(this).siblings().css("display", "none");
    //        } else if ($(this).siblings("i").css("display") == undefined) {
    //            $(".mod-operate-detail").css("display", "none");
    //            $(".mod-operate .ico20").css("display", "none");
    //        }
    //        else {
    //            $(".mod-operate-detail").css("display", "none");
    //            $(".mod-operate .ico20").css("display", "none");
    //            $(".mod-operate-detail:eq(" + $(this).attr('data') + ")").css("display", "block");
    //            $(this).siblings().css("display", "block");
    //        }
    //   });
});

function CheckAll(event) {

    event = event ? event : window.event;
    var eventSrc = event.srcElement ? event.srcElement : event.target;
    $("input[name='sub']").prop("checked", $(eventSrc).attr("checked") == "checked" ? "checked" : false);
    var $subs = $("input[name='sub']");
    $subs.parents("tr").removeClass("current");
    $("input[name='sub']:checked").parents("tr").addClass("current");
}
function CheckThis() {
    var $subs = $("input[name='sub']");
    $("#ckall").prop("checked", $subs.length == $subs.filter(":checked").length ? true : false);
    $subs.parents("tr").removeClass("current");
    $("input[name='sub']:checked").parents("tr").addClass("current");

}



function LayerCheckAll(event) {

    event = event ? event : window.event;
    var eventSrc = event.srcElement ? event.srcElement : event.target;
    $("input[name='layersub']").prop("checked", $(eventSrc).attr("checked") == "checked" ? "checked" : false);
    var $layersub = $("input[name='layersub']");
    $layersub.parents("tr").removeClass("current");
    $("input[name='layersub']:checked").parents("tr").addClass("current");
}
function LayerCheckThis() {
    var $layersub = $("input[name='layersub']");
    $("#layerckall").prop("checked", $layersub.length == $layersub.filter(":checked").length ? true : false);
    $layersub.parents("tr").removeClass("current");
    $("input[name='layersub']:checked").parents("tr").addClass("current");

}