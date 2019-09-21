layui.use(['form'], function () {
	var form = layui.form;
	var repeat_flag = false;//防重复标识
	form.verify({
		title: function (value) {
			if (value.length < 1) {
				return '标题不能为空';
			}
		}
		, category_id: function (value) {
			if (value < 1) {
				return '请选择分类';
			}
		}
	});
	
	form.on('submit(btnArticle)', function (data) {
		
		var field = data.field;
		var content = editor.getContent();
		if (content.length > 65535) {
			layer.msg("文章内容太长");
			return false;
		}
		field.content = content;
		
		if (repeat_flag) return;
		repeat_flag = true;
		
		if (field.article_id > 0) {
			var ajax_url = 'article://sitehome/article/editArticle';
		} else {
			var ajax_url = 'article://sitehome/article/addArticle';
		}
		$.ajax({
			type: "post",
			url: nc.url(ajax_url),
			data: field,
			dataType: "JSON",
			success: function (data) {
				layer.msg(data.message);
				if (data.code == 0) {
					window.location.href = nc.url("article://sitehome/article/articleList");
				} else {
					repeat_flag = false;
				}
				
			}
		});
		
		return false;
	});
	
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
		$(".title_img").html('<div class="upload-img-box has-choose-image"><div><img src="' + nc.img(res.path) + '" layer-src="' + nc.img(res.path) + '"></div><span onclick="uploadSingletitle_img();">修改</span></div>');
		$("input[name='title_img']").val(res.path);
	}
}