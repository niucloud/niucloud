hui.blackMask();
hui.formInit();
hui('#submit').click(function () {
	//验证
	var res = huiFormCheck('#member_form');
	if (res) {
		var data = hui.getFormData('#member_form');
		$.ajax({
			type: 'post',
			url: nc.url("wap/login/login"),
			data: data,
			async: false,
			success: function (res) {
				if (res['code'] == 0) {
					if (res.data.redirect_login_url) location.href = nc.url(res.data.redirect_login_url);
					else location.href = nc.url("wap/index/index");
				} else {
					hui.toast(res.message);
				}
			}
		});
	}
});