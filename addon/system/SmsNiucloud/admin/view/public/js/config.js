layui.use('form', function () {
	var form = layui.form;
	
	form.verify({
		CorpID: function (value) {
			if (value.length == 0) {
				return '请输入账号';
			}
		},
		Pwd: function (value) {
			if (value.length == 0) {
				return '请输入密码';
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
		var status = data.field.switch == undefined ? 0 : 1;
		if (repeat_flag) return;
		repeat_flag = true;
		$.ajax({
			type: "post",
			url: nc.url("smsniucloud://admin/index/config"),
			data: {
				value: JSON.stringify(data.field),
				status: status
			},
			dataType: "JSON",
			success: function (res) {
				layer.msg(res.message);
				if (res.code == 0) {
					location.reload();
				}else{
					repeat_flag = false;
				}
			}
		});
	})
});