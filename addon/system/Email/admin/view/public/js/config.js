layui.use('form', function () {
	var form = layui.form;
	var repeat_flag = false;//防重复标识
	form.on('submit(*)', function (data) {
		var field = data.field;
		if (repeat_flag) return;
		repeat_flag = true;
		$.ajax({
			type: "post",
			url: nc.url("email://admin/config/config"),
			data: field,
			dataType: "JSON",
			success: function (res) {
				repeat_flag = false;
				layer.msg(res.message);
			}
		});
	});
	
	form.verify({
		username: function (value, item) {
			if (value == '') {
				return '请设置SMTP 身份验证用户名';
			}
			if (/(^\_)|(\__)|(\_+$)/.test(value)) {
				return '用户名首尾不能出现下划线\'_\'';
			}
			if (/^\d+\d+\d$/.test(value)) {
				return '用户名不能全为数字';
			}
		},
		server: function (value, item) {
			if (value == '') {
				return '请设置邮件服务器地址';
			}
		},
		port: function (value, item) {
			if (value == '') {
				return '请设置SMTP端口';
			}
		},
		password: function (value, item) {
			if (value == '') {
				return '请设置SMTP身份验证密码';
			}
		}
	});
});