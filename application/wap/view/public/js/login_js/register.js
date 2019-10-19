hui.formInit();
//表单验证
var is_submit = false;
hui('#submit').click(function () {
	
	if (is_submit) return false;
	
	var res = huiFormCheck('#form_register');
	if (!res) return false;
	
	var data = hui.getFormData('#form_register');
	var is_true = formCheck(data);
	if (!is_true) return;
	
	is_submit = true;
	
	data.tag = tag;//第三方登录标识
	$.ajax({
		type: 'post',
		url: nc.url("wap/login/register"),
		data: data,
		async: false,
		success: function (res) {
			if (res['code'] == 0) {
				if (res.data.redirect_login_url) location.href = nc.url(res.data.redirect_login_url);
				else location.href = nc.url("wap/index/index");
			} else {
				hui.toast(res.message);
				is_submit = false;
			}
		}
	});
	
});

//附加验证函数用于单选多选等特殊检查项目
function formCheck(data) {
	// hui('#username').
	if (data.username == '') {
		hui.toast('请输入账号');
		
		$("input[name='username']").focus();
		return false;
	}
	if (data.mobile == '') {
		hui.toast('请输入手机号');
		$("input[name='mobile']").focus();
		return false;
	}
	if (data.mobile && data.mobile.search(/^(13[0-9]|14[579]|15[0-3,5-9]|16[6]|17[0135678]|18[0-9]|19[89])\d{8}$/) == -1) {
		hui.toast('手机号格式不正确');
		$("input[name='mobile']").focus();
		return false;
	}
	if (data.sms_code == '') {
		hui.toast('请输入短信动态码');
		$("input[name='sms_code']").focus();
		return false;
	}
	if (data.email == '') {
		hui.toast('请输入邮箱');
		$("input[name='email']").focus();
		return false;
	}
	if (data.email && data.email.search(/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/) == -1) {
		hui.toast('邮箱格式不正确');
		$("input[name='email']").focus();
		return false;
	}
	if (data.email_code == '') {
		hui.toast('请输入邮箱动态码');
		$("input[name='email_code']").focus();
		return false;
	}
	
	if (data.password == '') {
		hui.toast('请输入密码');
		$("input[name='password']").focus();
		return false;
	}
	
	var pwd_length = 6;
	if ($("#hidden_pwd_length").val()) {
		pwd_length = $("#hidden_pwd_length").val();
	}
	
	if (data.password.length < pwd_length) {
		hui.toast('密码长度最少为' + pwd_length + "位");
		$("input[name='password']").focus();
		return false;
	}
	
	if (data.cfpassword == '') {
		hui.toast('请输入确认密码');
		$("input[name='cfpassword']").focus();
		return false;
	}
	if (data.password != data.cfpassword) {
		hui.toast('两次输入的密码不一致');
		$("input[name='cfpassword']").focus();
		return false;
	}
	if (data.captcha == '') {
		hui.toast('请输入验证码');
		$("input[name='captcha']").focus();
		return false;
	}
	if (!$("[name='agreement']").is(":checked")) {
		hui.toast('请同意用户注册协议');
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
	
	nc.api("System.Login.sendCode", {mobile: mobile}, function (res) {
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
	
	nc.api("System.Login.sendCode", {email: email}, function (res) {
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

//第三方注册，System.Login.oauthRegister