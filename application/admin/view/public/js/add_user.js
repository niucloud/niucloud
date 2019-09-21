layui.use(['form'], function () {
	var form = layui.form;
	
	form.verify({
		groupId: function (value, item) {
			if($(".nc-step-tab.nc-step-active").index() == 2){
				if (!/[\S]+/.test(value)) {
					return "请选择管理组";
				}
			}
		},
		password: [/(.+){6,20}$/, '密码必须在6~20位'],
		repassword: function (value) {
			if (value != $('#password').val()) {
				return '两次密码不一致';
			}
		},
		phone: [/^1[3|4|5|7|8]\d{9}$/, '手机必须11位，只能是数字！'],
	});
	
	var repeat_flag = false;//防重复标识
	form.on('submit(save)', function (data) {
		if (repeat_flag) return;
		$.ajax({
			type: "post",
			url: nc.url("admin/user/adduser"),
			data: data.field,
			dataType: "JSON",
			success: function (res) {
				if (res.code == 0) {
					layer.msg(res.message, function () {
						location.href = nc.url("admin/user/userlist");
					});
				} else {
					repeat_flag = false;
					layer.msg(res.message);
				}
			}
		});
		//阻止表单跳转。如果需要表单跳转，去掉这段即可。
		return false;
	});
	
	//下一步
	form.on('submit(next_step)', function (data) {
		
		stepChange(1);
		//阻止表单跳转。如果需要表单跳转，去掉这段即可。
		return false;
	});
	
	//上一步
	form.on('submit(last_step)', function (data) {
		stepChange(0);
		//阻止表单跳转。如果需要表单跳转，去掉这段即可。
		return false;
	});
});

/**
 * 切换步骤
 */
function stepChange(step) {
	$(".nc-step .nc-step-tab").removeClass("nc-step-active");
	$(".nc-step .nc-step-tab:eq(" + step + ")").addClass("nc-step-active");
	$(".nc-step-content .nc-step-item").removeClass("layui-show");
	$(".nc-step-content .nc-step-item:eq(" + step + ")").addClass("layui-show");
}