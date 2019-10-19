layui.use('form', function () {
	var form = layui.form;
	var repeat_flag = false;//防重复标识
	form.on('submit(save)', function (data) {
		var field = data.field;
		if (repeat_flag) return;
		repeat_flag = true;
		$.ajax({
			type: "post",
			url: nc.url("wechat://admin/config/wechat"),
			data: field,
			dataType: "JSON",
			success: function (res) {
				repeat_flag = false;
				layer.msg(res.message);
			}
		});
		return false;
	});

});

function copy(copy_num_id) {
	var c = document.getElementById(copy_num_id);
	c.select();
	document.execCommand("Copy");
	layer.msg('复制成功');
}