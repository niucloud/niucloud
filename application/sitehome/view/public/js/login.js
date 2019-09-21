/**
 * 登陆js
 */
$(function () {
	
	layui.use(['form'], function () {
		var form = layui.form;
		var repeat_flag = false;//防重复标识
		form.on('submit(save)', function (data) {
			if (repeat_flag) return;
			repeat_flag = true;
			$.ajax({
				type: "post",
				url: nc.url('home/login/login'),
				data: data.field,
				dataType: "JSON",
				success: function (res) {
					if (res.code == 0) {
						window.location.href = res.data.url;
					} else {
						repeat_flag = false;
						if (res.code == -1) {
							$("#capthcha_code").attr("src", nc.url('home/login/captcha'));
						}
					}
				}
			})
		});
		form.verify({
			required: function (value, item) {
				var msg = $(item).attr("placeholder") != undefined ? $(item).attr("placeholder") : '';
				if (value == '') return msg;
			},
		});
	});
	
	$('.nui-home-username input').focus(function () {
		var obj = $(this).parent();
		obj.css('border-color', '#5491DF');
		focusImg(obj);
	}).blur(function () {
		var obj = $(this).parent();
		obj.css('border-color', '#D2D2D2');
		focusImg(obj);
	})
});

function focusImg(obj) {
	var left_img_obj = obj.children('.nui-username-bg').children('img');
	var focus_src = left_img_obj.attr('src');
	if (left_img_obj.attr('focus_src') != undefined) {
		left_img_obj.attr('src', left_img_obj.attr('focus_src'));
		left_img_obj.attr('focus_src', focus_src);
	}
}