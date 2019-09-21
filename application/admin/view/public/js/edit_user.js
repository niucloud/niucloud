var laytpl, index, form;
layui.use(['laytpl', 'form', 'element'], function () {
	laytpl = layui.laytpl;
	form = layui.form;
	var element = layui.element;
	
	//获取hash来切换选项卡
	var layid = location.hash.replace(/^#tab=/, '');
	
	if (layid == "") layid = tab;
	
	element.tabChange('edit_user_tab', layid);
	
	//监听Tab切换，以改变地址hash值
	element.on('tab(edit_user_tab)', function () {
		location.hash = 'tab=' + this.getAttribute('lay-id');
	});
	
	form.verify({
		groupId: function (value, item) {
			if (!/[\S]+/.test(value)) {
				return "请选择管理组";
			}
		},
		oldPass: function (value, item) {
			if (!/[\S]+/.test(value)) {
				return "请输入原密码";
			}
			if (/[\s]+/.test(value)) {
				return "不允许使用空格等特殊字符";
			}
			if (!/^[\S]{6,18}$/.test(value)) {
				return "请输入6到18位的原密码";
			}
		},
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
	form.on('submit(editUserName)', function (data) {
		$.ajax({
			type: "post",
			async: false,
			url: nc.url("admin/user/edituser"),
			dataType: 'json',
			data: {
				username: data.field.username,
				uid: uid
			},
			success: function (res) {
				
				layer.msg(res.message);
				if (res.code == 0) {
					back();
					location.reload();
				}
			}
		})
	});
	
	// 修改密码
	form.on('submit(editPassSubmit)', function (data) {
		$.ajax({
			type: "post",
			async: false,
			url: nc.url("admin/user/editUserPwd"),
			dataType: 'json',
			data: {
				password: data.field.new_pass,
				old_pass: data.field.old_pass,
				uid: uid
			},
			success: function (res) {
				console.log(res);
				layer.msg(res.message);
				if (res.code == 0) {
					back();
					location.reload();
				}
			}
		})
	});
	
	// 修改昵称
	form.on('submit(editNickName)', function (data) {
		$.ajax({
			type: "post",
			async: false,
			url: nc.url("admin/user/edituser"),
			dataType: 'json',
			data: {
				nick_name: data.field.nick_name,
				uid: uid
			},
			success: function (res) {
				layer.msg(res.message);
				if (res.code == 0) {
					back();
					location.reload();
				}
			}
		})
	});
	
	// 修改真实姓名
	form.on('submit(editRealName)', function (data) {
		$.ajax({
			type: "post",
			async: false,
			url: nc.url("admin/user/edituser"),
			dataType: 'json',
			data: {
				real_name: data.field.real_name,
				uid: uid
			},
			success: function (res) {
				layer.msg(res.message);
				if (res.code == 0) {
					back();
					location.reload();
				}
			}
		})
	});
	
	// 修改手机号
	form.on('submit(editMobile)', function (data) {
		$.ajax({
			type: "post",
			async: false,
			url: nc.url("admin/user/edituser"),
			dataType: 'json',
			data: {
				mobile: data.field.mobile,
				uid: uid
			},
			success: function (res) {
				layer.msg(res.message);
				if (res.code == 0) {
					back();
					location.reload();
				}
			}
		})
	});
	
	// 修改权限
	form.on('submit(editGroup)', function (data) {
		if (uid == 1) {
			layer.msg('超级管理员不可修改此选择', {icon: 1});
			return false;
		}
		data.field.uid = uid;
		$.ajax({
			type: "post",
			async: false,
			url: nc.url("admin/user/edituser"),
			dataType: 'json',
			data: data.field,
			success: function (res) {
				layer.msg(res.message);
				if (res.code == 0) {
					back();
					location.reload();
				}
			}
		})
	})
	
});

function editLayer(type, title, width, height) {
	laytpl($("#" + type).html()).render({}, function (html) {
		index = layer.open({
			type: 1,
			title: title,
			skin: 'layer-tips-class',
			area: [width, height],
			content: html,
		});
		form.render();
	});
}

function singleImageUploadSuccess(res, name) {
	if (name == "icon") {
		$.ajax({
			type: "post",
			async: false,
			url: nc.url("admin/user/edituser"),
			dataType: 'json',
			data: {
				headimg: res.path,
				uid: uid
			},
			success: function (res) {
				layer.msg(res.message);
				if (res.code == 0) {
					back();
					location.reload();
				}
			}
		})
	}
}

var table = new Table({
	elem: '#site_list',
	filter: "site_list",
	where: {
		uid: uid
	},
	url: nc.url("admin/site/siteList"),
	cols: [
		[
			{
				field: 'site_name',
				width: '25%',
				title: '站点名称',
				templet: '#site_name',
			},
			{
				field: 'site_style',
				width: '15%',
				title: '站点信息',
				templet: '#site_style',
			},
			{
				field: 'username',
				width: '15%',
				title: '创建人',
			},
			{
				field: 'create_time',
				width: '15%',
				title: '创建时间',
				templet: function (data) {
					return data.create_time == 0 ? '永久' : nc.time_to_date(data.create_time);
				}
			},
			{
				field: 'validity_time',
				width: '15%',
				title: '到期时间',
				templet: function (data) {
					return data.validity_time == 0 ? '永久' : nc.time_to_date(data.validity_time);
				}
			},
			{
				title: '操作',
				width: '15%',
				toolbar: '#operation',
				align: 'right',
			},
		]
	],
	size: 'lg'
});

//监听工具条
table.tool(function (obj) {
	var data = obj.data;
	switch (obj.event) {
		case "sitedetail":
			location.href = nc.url('admin/site/sitedetail', {siteid: data.site_id});
			break;
		case 'deleteSite':
			$.ajax({
				type: "post",
				url: nc.url("admin/site/deletesite"),
				data: {
					'site_id': data.site_id,
				},
				dataType: "JSON",
				success: function (res) {
					layer.msg(res.message);
					location.reload();
				}
			});
			break;
	}
});