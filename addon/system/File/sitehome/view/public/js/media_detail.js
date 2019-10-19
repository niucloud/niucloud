/**
 * 弹出视频信息
 * @param path
 * @param path_name
 * @param size
 * @param time
 */
function mediaDetail(path, path_name, size, time){
    var data = {path:path, file_name:path_name, size:size,create_time:time};
    layui.use(['laytpl'], function() {
        var laytpl = layui.laytpl;
        var tpl_html = $("#media_detail_html").html();
        laytpl(tpl_html).render(data, function (html) {
            layer.open({
                type: 1
                , title: false //不显示标题栏
                , closeBtn: 1
                , area: ['700px','480px']
                , shade: 0.8
                , id: 'LAY_layuipro' //设定一个id，防止重复弹出
                , btnAlign: 'c'
                , moveType: 1 //拖拽模式，0或者1
                , content: html
                , success: function (layero) {

                }
            });
        })
    })
}
