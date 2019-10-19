layui.use(['form'], function () {
	var form = layui.form;
	var repeat_flag = false;//防重复标识
	
	form.on('submit(*)', function (data) {
		var val = data.field;
		var name = $("#name").val();
		if (repeat_flag) return;
		repeat_flag = true;
		$.ajax({
			type: "post",
			url: nc.url("admin/system/auth"),
			data: {
				'val': JSON.stringify(val),
				'name': name,
			},
			dataType: "JSON",
			success: function (res) {
				layer.msg(res.message);
				if (res.code == 0) {
					location.reload();
				} else {
					repeat_flag = false;
				}
			}
		});
		return false;
	});
	
	form.verify({
		app_secret: function (value, item) {
			if (value == '') {
				return '请输入授权码秘钥';
			}
		},
		app_key: function (value, item) {
			if (value == '') {
				return '请输入授权码';
			}
		}
	});
	
});
$('input').change(function () {
	$('.btn-already').html('立即绑定');
});