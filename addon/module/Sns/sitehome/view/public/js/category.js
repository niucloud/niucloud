layui.use('form', function () {
    var form = layui.form;

    //选择分类隐藏与显示分类
    form.on('select(parent)', function (data) {

        if(data.value > 0){
            $('.layui-form-item').show();
        }else{
            $('.dis').hide();
        }
    });
    
    form.verify({
        name: function (value) {
            if (value.length == 0) {
                return '请输入名称';
            }
        },
        sort: function (value) {
			if (value.length == 0) {
				return '请输入排序号';
			}
			if (isNaN(value) || trim(value) === "") {
				return '排序号必须是数字';
			}
		}
    });
	
	var repeat_flag = false;//防重复标识
    form.on('submit(saveSms)', function (data) {

        if (repeat_flag) return;
		repeat_flag = true;
		field = data.field;

        $.ajax({
            type: "post",
            url: nc.url(category_url_update),
            dataType: "JSON",
            data: field,
            success: function (res) {
                if (res.code != 0) {
					repeat_flag = false;
                }

                location.href = nc.url("sns://sitehome/info/category",{});

                /*if(field.parent > 0){
                    location.href = nc.url("sns://sitehome/info/childrencategory",{parent:field.parent});
                }else{
                    location.href = nc.url("sns://sitehome/info/category",{});
                }*/
            }
        });
    });
});

/**
 * 单图回调事件
 */
function singleImageUploadSuccess(res, name) {
    if (name == "icon_img") {
        $(".icon_img").html('<div class="upload-img-box has-choose-image"><div><img src="' + nc.img(res.path) + '" layer-src="' + nc.img(res.path) + '"></div><span onclick="uploadSingleicon_img();">修改</span></div>');
        $("input[name='icon']").val(res.path);
        layer.photos({
            photos: '#layer-photos',
            anim: 5
        });
    }
}

//去空格
function trim(str){
    return str.replace(/(^\s*)|(\s*$)/g, "");
}