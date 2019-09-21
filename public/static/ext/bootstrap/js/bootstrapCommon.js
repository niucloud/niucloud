﻿function SetPriceFormat(price) {
    var r = /\d+/g;
    if (r.test(price)) {
        var reg = /\./g;
        if (!reg.test(price)) {
            price = price + ".00";
            return price;
        } else {
            return price;
        }
    }
}

/*$(function () {
    /*$("#pageDiv").mouseover(function () {
        if (!$(this).is(":animated")) {
            $(this).animate({ "margin-bottom": "0px" }, 300);
        }
    }).mouseleave(function () {
        if (!$(this).is(":animated")) {
            $(this).animate({ "margin-bottom": "-35px" }, 300);
        }
    })
})*/

//$(function () {
//    var scroll_top = WhichBro();
//    var tipsWidth = $(".Loading").css("width").replace("px", "");
//    var windowWidth = $(window).width();
//    if (scroll_top < 41) {
//        $(".Loading").css({ "position": "fixed", "top": "41px", "left": (windowWidth - tipsWidth) / 2 + "px", "z-index": "99999", "width": "auto", "min-width": "350px", "height": "37px", "line-height": "37px" });
//    } else {
//        $(".Loading").css({ "position": "fixed", "top": "0px", "left": (windowWidth - tipsWidth) / 2 + "px", "z-index": "99999", "width": "auto", "min-width": "350px", "height": "37px", "line-height": "37px" });
//    }
//    $(window).scroll(function () {
//        var scroll_top = WhichBro();
//        if (scroll_top < 41) {
//            $(".Loading").css({ "position": "fixed", "top": (41 - document.documentElement.scrollTop) + "px" });
//        } else {
//            $(".Loading").css({ "position": "fixed", "top": "0px" });
//        }
//    })

//    $(window).resize(function () {
//        var tipsWidth = $(".Loading").css("width").replace("px", "");
//        var windowWidth = $(window).width();
//        scroll_top = WhichBro();
//        $(".Loading").css({ "position": "fixed", "left": (windowWidth - tipsWidth) / 2 + "px" });
//    })

//    $(".Loading i").click(function () {
//        $(".Loading").css("display", "none");
//    })
//})
//function NotVisible() {
//    $(".Loading").css("display", "none");
//}

function RedirectTo(url) {
    setTimeout("window.location.href='" + url + "'", 10000);
}

function RedirectToUrlByTime(url, time) {
    setTimeout("window.location.href='" + url + "'", time);
}

function WhichBro() {
    var scroll_top;
    if (document.documentElement && document.documentElement.scrollTop) {
        scroll_top = document.documentElement.scrollTop;
    }
    else if (document.body) {
        scroll_top = document.body.scrollTop; /*某些情况下Chrome不认document.documentElement.scrollTop则对于Chrome的处理。*/
    }
    return scroll_top;
}

var isDelTrue = false;
function confirmThis(event, tips) {
    event = event ? event : window.event;
    var eventSrc = event.srcElement ? event.srcElement : event.target;
    if (isDelTrue == false) {
        var confirmDiv = $("#dvConfirm").html();
        var flag = 0;
        art.dialog({
            okVal: "确定",
            ok: function () {
                isDelTrue = true;
                $(eventSrc).click();
            },
            cancel: true,
            lock: true,
            background: 'none', // 背景色
            opacity: 0.4, // 透明度
            content: confirmDiv,
            drag: false,
            resize: false

        });
        $("#confirmMessage").text(tips);
    } else {
        isDelTrue = false;
        return true;
    }
}


function change() {
    scroll_top = WhichBro();
    $(".Loading").css({ "display": "block" });
    var tipsWidth = $(".Loading")[0].clientWidth;
    tipsWidth = tipsWidth < 350 ? 350 : tipsWidth;
    tipsWidth = tipsWidth > 1027 ? 1027 : tipsWidth;
    var windowWidth = $(window).width();
    $(".Loading").css({ "left": (windowWidth - tipsWidth) / 2 + "px" });
    setTimeout('NotVisible()', 5000);

    //tips全站公用提示信息最多70个字，最长1027像素
    var tips = $("#operateTip").text();
    var tipsLength = tips.length;
    if (tipsLength > 70) {
        tips = tips.substring(0, 70) + "...";
        $("#operateTip").text(tips);
    }
}

//function displayNone() {
//    $(".Loading").css("display", "none");
//}

function GetUrlAppCode() {
    var url = window.location.href;
    var parameter = url.substring(url.indexOf('?') + 1);
    parameter = parameter.split('&');
    var reg = /appCode=/g;
    var appCode = "";
    for (var i = 0; i < parameter.length; i++) {
        reg.lastIndex = 0;
        if (reg.test(parameter[i])) {
            appCode = parameter[i].replace("appCode=", "").replace("#", "");
            break;
        }
    }
    return appCode;
}

function GetUrlMenuCode() {
    var url = window.location.href;
    var parameter = url.substring(url.indexOf('?') + 1);
    parameter = parameter.split('&');
    var reg = /MenuCode=/g;
    var menuCode = "";
    for (var i = 0; i < parameter.length; i++) {
        reg.lastIndex = 0;
        if (reg.test(parameter[i])) {
            menuCode = parameter[i].replace("MenuCode=", "").split('#')[0];
            break;
        }
    }
    return menuCode;
}

function TrimSpace(obj) {
    return obj.replace(/^\s*/g, "").replace(/\s*$/g, "");
}

//function alignCenter(ID) {
//    var divWidth = $("#" + ID + "").width();
//    var windowWidth = $(window).width();
//    $("#" + ID + "").css({"left": (windowWidth - divWidth) / 2 + "px" });
//}
// 对Date的扩展，将 Date 转化为指定格式的String 
// 月(M)、日(d)、小时(h)、分(m)、秒(s)、季度(q) 可以用 1-2 个占位符， 
// 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字) 
// 例子： 
// (new Date()).Format("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423 
// (new Date()).Format("yyyy-M-d h:m:s.S")      ==> 2006-7-2 8:9:4.18 
Date.prototype.Format = function (fmt) { //author: meizz 
    var o = {
        "M+": this.getMonth() + 1,                 //月份 
        "d+": this.getDate(),                    //日 
        "H+": this.getHours(),                   //小时 
        "m+": this.getMinutes(),                 //分 
        "s+": this.getSeconds(),                 //秒 
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度 
        "S": this.getMilliseconds()             //毫秒 
    };
    if (/(y+)/.test(fmt))
        fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o)
        if (new RegExp("(" + k + ")").test(fmt))
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
    return fmt;
}




function popupOperate(popDiv, title, id) {
    var dialog = document.getElementById(popDiv);

    art.dialog({
        id: id,
        title: title,
        lock: true,
        background: '#000', // 背景色
        opacity: 0.4, // 透明度
        content: dialog,
        drag: false,
        resize: false
    });
    // $(".aui_footer").parent().remove();
};

function popupClose(id) {
    art.dialog.list[id].close();
    //  art.dialog.get(id).close();
}


//全局提示
function Show(text, type) {

    if (type == "success") {
        $("#success").html("");
        $("#success").append('<button data-dismiss="alert" class="close" type="button">×</button>' + text + '！');
        $("#success").show();
    }
    else if (type == "error") {
        $("#error").html("");
        $("#error").append('<button data-dismiss="alert" class="close" type="button">×</button>' + text + '！');
        $("#error").show();
    }
    else if (type == "prompt") {
        $("#prompt").html("");
        $("#prompt").append('<button data-dismiss="alert" class="close" type="button">×</button>' + text + '！');
        $("#prompt").show();
    }
    setTimeout("hide()", 5000);
}

function hide() {
    $(".alert").hide()
}