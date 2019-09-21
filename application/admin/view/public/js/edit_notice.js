var editor = new Editor("editor", {});
layui.use(['form'], function (data) {
	var form = layui.form;
	var repeat_flag = false;//防重复标识
	form.verify({
		numberSort: function (value) {
			if (parseInt(value) > 255) {
				return '排序规则不规范请重新输入';
			}
		},
		requiredselect: function (value) {
			if (value == '') {
				return '请选择分类';
			}
		},
	});
	
	form.on('submit(formSave)', function (data) {
		var notice_id = $('#notice_id').val();
		var field = data.field;
		field.content = editor.getContent();
		if (field.content == "") {
			layer.msg('请输入公告内容', {icon: 5});
			return;
		}
		if (repeat_flag) return;
		repeat_flag = true;
		var url = nc.url('admin/Config/addNotice');
		if (notice_id != "") url = nc.url('admin/Config/editNotice');
		$.ajax({
			type: "post",
			url: url,
			data: {
				'data': field,
				'notice_id': notice_id
			},
			dataType: "JSON",
			success: function (res) {
				layer.msg(res.message);
				location.href = nc.url("admin/config/notice");
			}
		});
	});
});