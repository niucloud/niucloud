var sendCodeId = 0;
var laytpl, index, form, util;

layui.use(['laytpl', 'form', 'util'], function (data) {
	laytpl = layui.laytpl;
	form = layui.form;
	util = layui.util;
	form.verify({
		password: function (value, item) {
			if (!/[\S]+/.test(value)) {
				return "请输入新密码";
			}
			if (/[\s]+/.test(value)) {
				return "不允许使用空格等特殊字符";
			}
			if (!/^[\S]{6,18}$/.test(value)) {
				return "请输入6到18位的密码";
			}
		},
		cPassword: function (value, item) {
			if (!/[\S]+/.test(value)) {
				return "请再次确认新密码";
			}
			if ($('[name="new_pass"]').val() != value) {
				return "两次输入的密码不一致";
			}
		},
		mobile: function (value, item) {
			if (!/^(((13[0-9]{1})|(14[7]{1})|(15[0-9]{1})|(18[0-9]{1})|(17[0-9]{1}))+\d{8})$/.test(value)) {
				return "请输入正确的手机号";
			}
		},
	});
	
	// 修改用户名
	form.on('submit(editusername)', function (data) {
		$.ajax({
			type: "post",
			async: false,
			url: nc.url("admin/user/edituser"),
			dataType: 'json',
			data: data.field,
			success: function (res) {
				back();
				layer.msg(res.message);
				location.reload();
			}
		})
	});
	
	// 修改密码
	form.on('submit(editPassSubmit)', function (data) {
		$.ajax({
			type: "post",
			async: false,
			url: nc.url("admin/Index/modifypassword"),
			dataType: 'json',
			data: {
				old_pass: data.field.old_password,
				new_pass: data.field.new_pass,
			},
			success: function (res) {
				back();
				layer.msg(res.message);
				location.reload();
			}
		})
	});
	
	// 修改绑定手机号
	form.on('submit(next)', function (data) {
		$('.layui-layer-wrap .old-mobile').hide().siblings('.new-mobile').show();
	});
	
	var repeat_flag = false;//防重复标识
	form.on('submit(update_bind_mobile)', function (data) {
		var field = data.field;
		if (repeat_flag) return;
		repeat_flag = true;
		$.ajax({
			type: "post",
			url: nc.url('admin/index/updateMobile'),
			data: field,
			dataType: "JSON",
			async: false,
			success: function (data) {
				layer.msg(data.message);
				if (data.code >= 0) {
					layer.closeAll();
					location.reload();
				} else {
					repeat_flag = false;
				}
			}
		})
	});
	
	// 绑定手机
	form.on('submit(bind_mobile)', function (data) {
		var field = data.field;
		if (repeat_flag) return;
		repeat_flag = true;
		$.ajax({
			type: "post",
			url: nc.url('admin/index/bindmobile'),
			data: field,
			dataType: "JSON",
			async: false,
			success: function (data) {
				layer.msg(data.message);
				if (data.code >= 0) {
					layer.closeAll();
					location.reload();
				} else {
					repeat_flag = false;
				}
			}
		})
	});
	
});

/**
 * 更换手机号
 */
function updateMobile() {
	var title = "更换手机号";
	laytpl($("#update_mobile_html").html()).render(tpl_data, function (html) {
		index = layer.open({
			type: 1,
			title: title,
			skin: 'layer-tips-class',
			area: ['550px'],
			resize: false,
			content: html,
			cancel: function (index, layero) {
			}
		});
		form.render();
	});
}

/**
 * 绑定手机号
 */
function bindMobile() {
	var title = "绑定手机号";
	laytpl($("#bind_mobile_html").html()).render(tpl_data, function (html) {
		index = layer.open({
			type: 1,
			title: title,
			skin: 'layer-tips-class',
			area: ['550px'],
			resize: false,
			content: html,
			cancel: function (index, layero) {
			}
		});
		form.render();
	});
}

var repeat_flag_is_send_bind = false;//防重复标识
function sendOutCode(event) {
	var code_obj = $(event);
	
	var mobile = $(event).parent().parent().find("input[name='mobile']").val();
	if (mobile == '') {
		layer.msg('手机号不能为空', {icon: 5});
		return;
	} else if (mobile.search(/^(13[0-9]|14[579]|15[0-3,5-9]|16[6]|17[0135678]|18[0-9]|19[89])\d{8}$/) == -1) {
		layer.msg('请输入正确的手机格式', {icon: 5});
		return;
	}
	
	//发送短信验证码
	if (repeat_flag_is_send_bind) return false;
	$.ajax({
		type: "post",
		url: nc.url('admin/index/sendSmsCode'),
		async: false,
		data: {
			mobile: mobile
		},
		dataType: "JSON",
		success: function (res) {
			
			layer.msg(res.message);
			if (res.code == 0) {
				//示例
				var serverTime = new Date().getTime();
				var endTime = serverTime + 60 * 1000;
				code_obj.addClass("layui-hide");
				code_obj.parent().find(".code-time").removeClass("layui-hide");
				util.countdown(endTime, serverTime, function (date, serverTime, timer) {
					var time_num = date[2] * 60 + date[3];
					code_obj.parent().find(".code-time").text(time_num + "s");
					if (date[0] == 0 && date[1] == 0 && date[2] == 0 && date[3] == 0) {
						code_obj.parent().find(".code-time").addClass("layui-hide");
						code_obj.text("重新发送");
						code_obj.removeClass("layui-hide");
						repeat_flag_is_send_bind = false;
					}
				});
			} else {
				repeat_flag_is_send_bind = false;
			}
		}
	});
}

function singleImageUploadSuccess(res, name) {
	if (name == "icon") {
		$("input[name='icon']").val(res.path);
		$.ajax({
			type: "post",
			async: false,
			url: nc.url("admin/user/editUser"),
			dataType: 'json',
			data: {
				headimg: res.path,
				uid: uid
			},
			success: function (res) {
				back();
				layer.msg(res.message, {time: 100}, function () {
					location.reload();
				});
			}
		})
	}
}


function editLayer(type, title, width, height) {
	laytpl($("#" + type).html()).render(tpl_data, function (html) {
		index = layer.open({
			type: 1,
			title: title,
			skin: 'layer-tips-class',
			area: [width, height],
			content: html,
		});
		form.render();
		try {
			var funcName = type + 'LayerAfter';
			if (typeof(eval(funcName)) == "function") {
				eval(funcName + '(tpl_data)');
			}
		} catch (e) {
		}
	});
}