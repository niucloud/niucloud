
var host = window.location.host;
//if (host.indexOf("localhost") > -1
//|| host.indexOf("192.168") > -1
//|| host.indexOf("127.0.0.1") > -1) {
//    host = "http://32.css.mcm.v5portal.com"; //测试地址
//} else {
//    host = "http://css.web08.net";
//}
//host = "http://css.bmc.v5portal.com/";
//var jsPara = (function () {
//    var obj = {},
//    //因为js是顺序执行的，那么运行到它这里肯定是把自己当做是script的最后一个
//    scripts = document.getElementsByTagName("script"),
//    scriptSrc = scripts[scripts.length - 1].src,
//    //分解scriptSrc
//    paras = scriptSrc.split("?")[1].split("&"); //item=css
//
//    for (var i = 0, len = paras.length; i < len; i++) {
//        var tempPara = paras[i].split("="),
//        name = tempPara[0],
//        value = tempPara[1];
//        var cssList = value.split('|');
//        for (var j = 0; j < cssList.length; j++) {
//            document.writeln("<link rel=\"stylesheet\" type=\"text/css\" href=\"" + host + "/mcm/content/cssv2/" + cssList[j] + ".css\" />");
//        }
//        obj[name] = value;
//    }
//    return obj;
//})();
