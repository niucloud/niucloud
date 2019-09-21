var table = new Table({
	elem: '#oss_type_list',
	url: nc.url("file://admin/oss/index"),
	cols: [[{
		field: 'title',
		width: '50%',
		title: '上传方式',
		templet: '#title'
	}, {
		field: 'status',
		title: '是否启用',
		templet: '#status'
	}, {
		title: '操作',
		toolbar: '#operation',
		align: 'right'
	}]],
	page: false
});

layui.use(['form'], function () {
	var form = layui.form;
	form.on('switch(status)', function (obj) {
		$.ajax({
			type: "post",
			url: nc.url("file://admin/oss/modifyfiletypeisopen"),
			data: {
				'name': this.value,
				'status': obj.elem.checked ? 1 : 0
			},
			dataType: "JSON",
			success: function (res) {
				layer.msg(res.message);
				if (res.code == 0) {
					table.reload({
						page: false
					});
				}
			}
		});
	});
});