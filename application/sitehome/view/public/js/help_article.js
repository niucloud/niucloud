layui.use(['form'], function () {
	var form = layui.form;
	var repeat_flag = false;//防重复标识
	form.render();
	form.verify({
		title: function (value) {
			if (value.length < 1) {
				return '帮助标题不能为空';
			}
		},
		class_id: function (value) {
			if (value < 1) {
				return '请选择类型';
			}
		}
	});
	
	form.on('submit(btnArticle)', function (data) {
		var content = editor.getContent();
		if (content.length = 0) {
			layer.msg("请输入帮助内容", {icon: 5});
			return;
		}
		if (content.length > 65535) {
			layer.msg("内容太长", {icon: 5});
			return;
		}
		
		data.field.content = content;
		
		if (repeat_flag) return;
		repeat_flag = true;
		
		if (data.field.id > 0) {
			var ajax_url = 'sitehome/help/edithelparticle';
		} else {
			var ajax_url = 'sitehome/help/addhelparticle';
		}
		$.ajax({
			type: "post",
			url: nc.url(ajax_url),
			data: data.field,
			dataType: "JSON",
			success: function (data) {
				layer.msg(data.message);
				if (data.code == 0) {
					location.href = nc.url("sitehome/help/index");
				} else {
					repeat_flag = false;
				}
			}
		});
		
		return false;
	});
	
});