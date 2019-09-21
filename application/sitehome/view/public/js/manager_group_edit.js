layui.use(['form'], function () {
	var form = layui.form;
	
	var repeat_flag = false;//防重复标识
	form.on('submit(save)', function (data) {
		var field = data.field;
		//权限
		var obj = $("#tree_box input:checked.group-checkbox");
		var group_array = [];
		for (var i = 0; i < obj.length; i++) {
			group_array.push(obj.eq(i).val());
		}
		field.group_array = group_array.toString();
		//submit_url和 jump_url 在引用该js的页面规定
		var addon_obj = $("#tree_box input:checked.addon-checkbox");
		var addon_array = [];
		for (var i = 0; i < addon_obj.length; i++) {
			addon_array.push(addon_obj.eq(i).val());
		}
		field.addon_array = addon_array.toString();
		
		//应用公共装修页权限
		var diyview_page_array = [];
		var addon_diyview_obj = $("#tree_box input[value='ADDON_DIYVIEW']:checked");
		for (var i = 0; i < addon_diyview_obj.length; i++) {
			diyview_page_array.push(addon_diyview_obj.eq(i).data("tag"));
		}
		field.diyview_page_array = diyview_page_array.toString();
		//应用公共装修页权限
		var auth_page_array = [];
		var addon_auth_obj = $("#tree_box input[value='ADDON_AUTH']:checked");
		for (var i = 0; i < addon_auth_obj.length; i++) {
			auth_page_array.push(addon_auth_obj.eq(i).data("tag"));
		}
		field.auth_page_array = auth_page_array.toString();
		
		if (repeat_flag) return;
		repeat_flag = true;
		$.ajax({
			url: submit_url,
			data: field,
			type: "post",
			dataType: "JSON",
			success: function (res) {
				layer.msg(res.message);
				if (res.code == 0) {
					window.location.href = nc.url('sitehome/manager/group');
				} else {
					repeat_flag = false;
				}
			}
		});
	});
	
	//验证
	form.verify({
		title: function (value) {
			if (value.length == 0) {
				return '请输入用户组名称';
			}
		},
		
	});
	
});