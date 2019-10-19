var sendtype = $("#tesing").val();

hui.formInit();
var is_submit = false;
hui('#submit').click(function () {
	if (is_submit) {
		return false;
	}
	var res = huiFormCheck('#form_find');
	//提交
	if (res) {
		var data = hui.getFormData('#form_find');
		var is_true = formCheck(data);
		if (is_true) {
			if (sendtype == 1) {
				data.type = "mobile";
				data.code = data.mobile_code;
				data.account = data.mobile;
			} else {
				data.type = "email";
				data.code = data.email_code;
				data.account = data.email;
			}
			is_submit = true;
			
			nc.api("System.Login.passwordReset", {
				account: data.account,
				password: data.pass,
				type: data.type,
				code: data.code
			}, function (res) {
				if (res["code"] == 0) {
					location.href = nc.url("wap/login/login");
				} else {
					hui.toast(res["message"]);
					is_submit = false;
				}
			});
		}
	}
});

function formCheck(data) {
	if (sendtype == 1) {
		var type = "mobile";
		if (data.mobile.length == 0) {
			hui.toast("请输入您的手机号码");
			$("#mobile").focus();
			return false;
		}
		if (data.mobile_code.length == 0) {
			hui.toast("请输入手机验证码");
			$("#mobile_code").focus();
			return false;
		}
	} else {
		var type = "email";
		if (data.email.length == 0) {
			hui.toast('请输入您注册的邮箱');
			$("#email").focus();
			return false;
		}
		if (data.email_code.length == 0) {
			hui.toast('请输入邮箱验证码');
			$("#email-code").focus();
			return false;
		}
	}
	if (data.pass.length < 6) {
		hui.toast('登录密码不能少于 6 个字符');
		$("#pass").focus();
		return false;
	}
	if (data.new_pass != data.pass) {
		hui.toast('两次输入的密码不一致');
		$("#new_pass").focus();
		return false;
	}
	
	return true;
}

/**
 * 发送短信验证码
 * @param event
 * @returns {boolean}
 */
function sendSmsCode(event) {
	var mobile = $("#mobile").val();
	
	if (mobile == '') {
		$("#mobile").trigger("focus");
		hui.toast('手机号不能为空');
		return false;
	} else if (mobile.search(/^(13[0-9]|14[579]|15[0-3,5-9]|16[6]|17[0135678]|18[0-9]|19[89])\d{8}$/) == -1) {
		$("#mobile").trigger("focus");
		hui.toast('手机格式不正确');
		return false;
	}
	
	nc.api("System.Login.sendFindCode", {mobile: mobile}, function (res) {
		if (res.code == 0) {
			countDown(event);
		} else {
			hui.toast(res.message);
		}
		
	});
}

/**
 * 发送邮箱验证码
 * @param event
 * @returns {boolean}
 */
function sendEmailCode(event) {
	var email = $("#email").val();
	
	if (email == '') {
		$("#email").trigger("focus");
		hui.toast('邮箱不能为空');
		return false;
	} else if (email.search(/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/) == -1) {
		$("#email").trigger("focus");
		hui.toast('邮箱格式不正确');
		return false;
	}
	
	nc.api("System.Login.sendFindCode", {email: email}, function (res) {
		if (res.code == 0) {
			countDown(event);
		} else {
			hui.toast(res.message);
		}
	});
}

/**
 * 发送验证码倒计时
 * @param chageObj
 * @param oldText
 * @param time
 */
function countDown(chageObj, oldText, time) {
	var time = time != undefined ? time : 180,
		oldText = oldText != undefined ? oldText : '获取动态码',
		text = time + 's后重新获取';
	
	if (time > 0) {
		$(chageObj).text(text).prop('disabled', true);
		time -= 1;
		setTimeout(function () {
			countDown(chageObj, oldText, time);
		}, 1000);
	} else {
		$(chageObj).text(oldText).prop('disabled', false);
	}
}