hui.blackMask();
hui.formInit();
hui('#submit').click(function(){
    //验证
    var res = huiFormCheck('#member_form');
	if(res){
		var data = hui.getFormData('#member_form');
		console.log(data);
        nc.api("System.Login.login", data, function (res) {
            if (res['code'] == 0) {
                $.cookie(cookie_name, res.data.access_token, {expires: 1, path: '/'});//expires：有效期
                location.href = nc.url("wap/member/index");
            } else {
                hui.toast(res.message);
            }
        });
	}
});