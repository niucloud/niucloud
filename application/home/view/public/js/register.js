/**
 * 注册js
 */
$(function () {
	
	layui.use(['form'], function () {
		var form = layui.form;
		var repeat_flag = false;//防重复标识
		
		form.verify({
			required: function (value, item) {
				var msg = $(item).attr("placeholder") != undefined ? $(item).attr("placeholder") : '';
				if (value == '') return msg;
			},
			pass: [
				/^[\S]{6,12}$/
				, '密码必须大于6位，且不能出现空格'
			], agreement: function (value, item) {
				
				if (!item.checked) return '请先阅读并接受协议';
			}
		});
		
		form.on('submit(save)', function (data) {
			
			if (repeat_flag) return;
			repeat_flag = true;
			
			$.ajax({
				type: "post",
				url: nc.url('home/login/register'),
				data: data.field,
				dataType: "JSON",
				success: function (res) {
					if (res.code == 0) {
						window.location.href = res.url;
					} else {
						repeat_flag = false;
						if (res.code == -1) {
							$("#capthcha_code").attr("src", nc.url('home/login/captcha'));
						}
						layer.msg(res.message, {icon: 5});
					}
				}
			})
		});
	});
	
});