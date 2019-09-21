layui.use('form', function () {
	var form = layui.form;
	form.verify({
		app_key: function (value) {
			if (value.length == 0) {
				return '请输入APP_KEY';
			}
		},
		secret_key: function (value) {
			if (value.length == 0) {
				return '请输入SECRET_KEY';
			}
		},
		signature: function (value) {
			if (value.length == 0) {
				return '请输入短信内容签名';
			}
		},
	});
	
	var repeat_flag = false;//防重复标识
	form.on('submit(saveSms)', function (data) {
		if (repeat_flag) return;
		repeat_flag = true;
		var field = data.field;
		$.ajax({
			type: "post",
			url: nc.url("smsaliyun://admin/index/config"),
			dataType: "JSON",
			data: field,
			success: function (res) {
				layer.msg(res.message);
				if (res.code != 0) {
					repeat_flag = false;
				}
			}
		});
	});
});