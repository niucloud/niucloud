var table = new Table({
	elem: '#user_list',
	filter: "user_list",
	url: nc.url("admin/user/userlist"),
	cols: [
		[
			{
				field: 'username',
				width: '43%',
				title: '用户名',
				unresize: false,
				templet: '#username'
			},
			{
				width: '15%',
				title: '类型',
				unresize: false,
				templet: function (data) {
					return data.is_admin ? '<span>系统管理员</span>' : '<span>站点管理员</span>';
				}
			},
			{
				field: 'register_time',
				width: '20%',
				title: '注册时间',
				unresize: false,
				templet: function (data) {
					return nc.time_to_date(data.register_time);
				}
			},
			{
				field: 'status',
				width: '12%',
				title: '状态',
				unresize: false,
				templet: '#status'
			},
			{
				title: '操作',
				width: '10%',
				toolbar: '#operation',
				unresize: false,
				align: 'right'
			},
			{
				field: 'more-info',
				width: '100%',
				templet: '#more_info',
				hide: true,
			}
		]
	],
	size: "lg"
});

//监听工具条
table.tool(function (obj) {
	var data = obj.data;
	switch (obj.event) {
		case 'edit_user_basic_info':
			location.href = nc.url("admin/User/editUser", {uid: data.uid, tab: "basic_info"});
			break;
		case 'edit_user_site_manage':
			location.href = nc.url("admin/User/editUser", {uid: data.uid, tab: "site_manage"});
			break;
		case 'edit_user_group':
			location.href = nc.url("admin/User/editUser", {uid: data.uid, tab: "group"});
			break;
		case "delete":
			layer.confirm('确定删除吗?', {
				btn: ['确定', '取消']
			}, function () {
				$.ajax({
					type: "post",
					url: nc.url("admin/user/deleteuser"),
					data: {
						'uid': data.uid,
					},
					dataType: "JSON",
					success: function (res) {
						layer.msg(res.message);
						location.reload();
					}
				});
			}, function () {
				layer.close();
			});
			
			break;
	}
});

layui.use(['form'], function () {
	var form = layui.form;
	//搜索submit提交
	form.on('submit(search)', function(data){
		table.reload({
			page: {
				curr: 1
			},
			where: data.field
		});
	});
});

layui.use('form', function () {
	var layui_form = layui.form;
	layui_form.render();

	layui_form.on('switch(status)', function (obj) {
		var status = obj.elem.checked ? 1 : 0;
		var uid = obj.value;
		$.ajax({
			type: "post",
			url: nc.url("admin/User/setuserstatus"),
			data: {
				'uid': uid,
				'status': status
			},
			dataType: "JSON",
			success: function (res) {
				layer.msg(res.message);
				table.reload();
			}
		});
	});
});