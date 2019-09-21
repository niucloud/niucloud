var form,laytpl,util;
layui.use(['form','layedit', 'util', 'laytpl'], function () {
    form = layui.form,
    util = layui.util;
    laytpl = layui.laytpl;
	var repeat_flag = false;//防重复标识
    var editor;

    //按钮事件
    util.event('lay-util', {
        next_step: function (e) {

            category_id = $(e).attr('category');
            category_name = $(e).parents('.layui-colla-item').children('.layui-colla-title').prop('firstChild').nodeValue+ '/ '+$(e).text();
            stepChange(1);
        }
        , last_step: function () {
            stepChange(0);
        }
    })

    /**
     * 切换步骤
     */
    function stepChange(sort){
        $(".nc-step .nc-step-tab").removeClass("nc-step-active");
        $(".nc-step .nc-step-tab:eq("+ sort +")").addClass("nc-step-active");
        $(".nc-step-content .nc-step-item").removeClass("layui-show");
        $(".nc-step-content .nc-step-item:eq("+ sort +")").addClass("layui-show");

        if(sort == 1){

            $.ajax({
                type: "get",
                url: nc.url("sns://sitehome/info/infoAttribute"),
                data:  {
                    'category_id' : category_id,
                    'category_name' : category_name
                },
                dataType: "JSON",
                success: function (res) {

                    $('.info-attribute').html(res);
                    editor = new Editor("content",{});
                    console.log(editor);
                    getAreaList(0, 1, 1);
                    form.render();

                }
            });
        }
    }

    //监听省市县区地址的变动
    form.on('select(province)', function (obj) {
        $("input[name='address']").val('');
        getAreaList(obj.value, 2);//重新渲染地址
        refreshFrom();
    });

    form.on('select(city)', function (obj) {
        $("input[name='address']").val('');
        getAreaList(obj.value, 3);//重新渲染地址
        refreshFrom();
    });

    /*if(tpl_data.province_id){
        getAreaList(0, 1);
    }else{
        getAreaList(0, 1, 1);
        getAreaList(tpl_data.province_id, 2, 1);
    }*/

    //提交数据
    form.on('submit(btnInfo)', function (data) {

        if(!data.field.city){
            layer.msg( "请选择圈子");
            return false;
        }
    
        if (!data.field.linkman) {
            layer.msg( "联系人不能为空");
            return false;
        }
    
        if (!data.field.contact) {
            layer.msg( "联系电话不能为空");
            return false;
        }
    
        if (!data.field.price) {
            layer.msg( "价钱不能为空");
            return false;
        }
    
        if (!data.field.title) {
            layer.msg( "标题名称不能为空");
            return false;
        }

        data.field.content = editor.getContent();
        data.field.category_id = category_id;


	
        if(repeat_flag) return;
		repeat_flag = true;
        $.ajax({
            type: "post",
            url: nc.url(url),
            data: data.field,
            dataType: "JSON",
            success: function (data) {
                if (data.code == 0) {
                     window.location.href = nc.url("sns://sitehome/info/infolist");
                } else {
					repeat_flag = false;
                    layer.msg(data.message);
                }
            }
        });

        return false;
    });
})

//设置图片
function singleMultipleImageUploadSuccess(data, name){
    var info_id = $(".info_id").val();
    //渲染相册
    var album_tpl = $("#album_html").html();

    laytpl(album_tpl).render(data, function (html) {
        $("#t_customerInfo").html(html);
        loadImgMagnify();
    });
}

//删除图片
$('body').on("click", ".delfile", function () {
    var index = $(this).parent().children('.file_id').val();
    $('.img_'+index).remove();
});

/** 
 * 获取地区列表
 * @param pid
 * @param level
 */
function getAreaList(pid, level, is_first_load = 0){
    if(level <= 5){
        $.ajax({
            type : "get",
            url : nc.url("sitehome/manager/getAreaList"),
            data : {
                'level' : level,
                'pid' : pid
            },
            dataType : "JSON",
            success : function(res) {

                // console.log(res);return;

                if(res.code == 0){
                    var obj = {1:'province', 2:'city', 3:'district', 4:'subdistrict'};

                    if(is_first_load == 0) removeSelectedData(level);
                    $.each(res.data, function(name, value) {

                        var sele = "selected='selected'";

                        if(value.id == tpl_data.city || value.id == tpl_data.province_id){
                            var html = "<option value='"+value.id+"'"+sele+">"+value.name+"</option>";
                        }else{
                            var html = "<option value='"+value.id+"'>"+value.name+"</option>";
                        }

                        if(level == 1){
                            $("select[name="+ obj[level] +"]").append(html);
                        }else{
                            $("select[name="+ obj[level] +"]").append(html);
                        }
                    });

                    // if(is_first_load) $("select[name="+ obj[level] +"]").val(tpl_data[obj[level] + '_id']);

                    form.render();
                    
                }else{
                    layer.msg(res.message);
                }

            }
        });
    }
}

/**
 * 重新渲染表单
 */
function refreshFrom(){
    form.render();
}

/**
 * 清除之前的地区数据
 */
function removeSelectedData(level){
    level = Number(level);
    if(level <= 1) $("select[name=province] option:gt(0)").remove();
    if(level <= 2) $("select[name=city] option:gt(0)").remove();
}