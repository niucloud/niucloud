layui.use(['form'], function () {
    var form = layui.form;
	var repeat_flag = false;//防重复标识

    form.render();
    form.verify({
        attribute_name: function (value) {
            if (value.length < 1) {
                return '标题不能为空';
            }
        }
    });

    form.on('submit(btnAttribute)', function (data) {
        if (repeat_flag) return;
		repeat_flag = true;

        var args = '';
        switch (data.field.input_type) {
            case 'text':
                args+='text_length:'+ data.field.text_length;
                args+='|text_default_value:'+ data.field.text_default_value;
                break;
            case 'textarea':
                args+='textarea_default_value:'+ data.field.textarea_default_value;
                args+='|textarea_width:'+ data.field.textarea_width;
                args+='|textarea_height:'+ data.field.textarea_height;
                break;
            case 'editor':
                args+='editor_height:'+ data.field.editor_height;
                args+='|editor_default_value:'+ data.field.editor_default_value;
                break;
            case 'select':
                args+='select_option:'+ data.field.select_option;
                args+='|select_default_value:'+ data.field.select_default_value;
                break;
            case 'radio':
                args+='radio_option:'+ data.field.radio_option;
                args+='|radio_default_value:'+ data.field.radio_default_value;
                break;
            case 'checkbox':
                args+='checkbox_option:'+ data.field.checkbox_option;
                args+='|checkbox_default_value:'+ data.field.checkbox_default_value;
                break;
            case 'img':
                args+='img_allow_image_type:'+ data.field.img_allow_image_type;
                args+='|img_open_image_mark:'+ data.field.img_open_image_mark;
                args+='|img_width:'+ data.field.img_width;
                args+='|img_height:'+ data.field.img_height;
                break;
            case 'imgs':
                args+='imgs_allow_image_type:'+ data.field.imgs_allow_image_type;
                args+='|imgs_open_image_mark:'+ data.field.imgs_open_image_mark;
                args+='|imgs_width:'+ data.field.imgs_width;
                args+='|imgs_height:'+ data.field.imgs_height;
                break;
            case 'file':
                args+='file_allow_file_type:'+ data.field.file_allow_file_type;
                break;
            case 'files':
                args+='files_allow_file_type:'+ data.field.files_allow_file_type;
                break;
            case 'number':
                args+='number_min:'+ data.field.number_min;
                args+='|number_max:'+ data.field.number_max;
                args+='|number_decimal_places:'+ data.field.number_decimal_places;
                args+='|number_default_value:'+ data.field.number_default_value;
                break;
            case 'time':
                args+='time_style:'+ data.field.time_style;
                break;
            case 'map':
                break;
            case 'area':
                break;
        }

        data.field.input_args = args;
        var category_id = data.field.category_id;

        $.ajax({
            type: "post",
            url: nc.url(attribute_add_edit_url),
            data: data.field,
            dataType: "JSON",
            success: function (data) {
                if (data.code == 0) {
                    window.location.href = nc.url("sns://sitehome/info/categorymanage?category_id="+category_id);
                } else {
					repeat_flag = false;
                }
            }
        });

        return false;
    });

    //选择类型
    form.on('select(input_type)', function(data){
        $('[data-name ^= "args_"]').hide();
        $('[data-name = "args_'+data.value+'"]').show();
    });

    //选择验证正则
    form.on('select(reg_type)', function(data){

        $('[name="reg"]').val(data.value);
    })
});

/**
 * 附件回调事件
 */
function uploadAttachmentSuccess(res, name) {
    if (name == "article") {
        $("input[name='attachment_path']").val(res.data.path);
    }
}

/**
 * 单图回调事件
 */
function singleImageUploadSuccess(res, name) {
    if (name == "title_img") {
        $(".title_img").html('<div class="upload-img-box has-choose-image"><div><img src="' + nc.img(res.path) + '"></div><span onclick="uploadSingletitle_img();">修改</span></div>');
        $("input[name='title_img']").val(res.path);
    }
}