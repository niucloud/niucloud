var table = new Table({
	elem: '#group_list',
	filter: "group_list",
	url: nc.url("admin/user/groupList"),
	cols: [[{
		width: "3%",
		type: 'checkbox'
	}, {
		field: 'group_name',
		width: "40%",
		title: '用户组名称'
	}, {
		field: 'status',
		width: "20%",
		title: '状态',
		templet: '#status'
	}, {
		field: '',
		width: "37%",
		title: '操作',
		align: 'right',
		toolbar: '#operation'
	}
	]],
});

//监听工具条
table.tool(function (obj) {
	var data = obj.data;
	switch (obj.event) {
		case "edit":
			window.location.href = nc.url('admin/user/editgroup', {
				"group_id": data.group_id,
				"site_id": data.site_id
			});
			break;
		case "deleteGroup":
			deleteGroup(data.group_id);
			break;
	}
});

function deleteGroup(group_id) {
	$.ajax({
		url: nc.url("admin/user/deleteGroup"),
		type: 'post',
		data: {
			"group_id": group_id
		},
		dataType: "JSON",
		success: function (res) {
			layer.msg(res.message);
			if (res.code == 0) {
				table.reload();
			}
		}
	});
}

layui.use(['form'], function () {
	var form = layui.form;
	form.on('switch(status)', function (obj) {
		$.ajax({
			type: "post",
			url: nc.url("admin/user/setGroupStatus"),
			data: {
				'group_id': obj.value,
				'status': obj.elem.checked ? 1 : 0
			},
			dataType: "JSON",
			success: function (res) {
				layer.msg(res.message);
				if (res.code == 0) {
					table.reload();
				}
			}
		});
	});
	
});