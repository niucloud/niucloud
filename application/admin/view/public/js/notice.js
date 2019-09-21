var table = new Table({
	elem: '#notice_list',
	filter: "notice_list",
	url: nc.url("admin/config/notice"),
	cols: [
		[
			{
				field: 'title',
				width: '30%',
				title: '标题',
                toolbar: '#title_html',
				align: 'left',
				unresize: 'false',

			},
			{
				field: 'notice_category_title',
				width: '10%',
				title: '归属分类',
				unresize: 'false',
			},
			{
				field: 'is_recommend',
				width: '8%',
				title: '是否推荐',
				unresize: 'false',
				templet: '#is_recommend',
			},
			{
				field: 'is_display',
				width: '8%',
				title: '是否显示',
				unresize: 'false',
				templet: '#is_display',
			},
			{
				field: 'create_time',
				width: '16%',
				title: '发布时间',
				unresize: 'false',
				templet: function (data) {
					return nc.time_to_date(data.create_time);
				}
			},
			{
				field: 'sort',
				width: '6%',
				unresize: 'false',
				title: '排序',
			},
			{
				field: 'click',
				width: '10%',
				unresize: 'false',
				title: '阅读次数',
			},
			{
				title: '操作',
				width: '11%',
				unresize: 'false',
				toolbar: '#operation',
				align: 'right',
			}
		]
	],
});

//监听工具条
table.tool(function (obj) {
	var data = obj.data;
	switch (obj.event) {
		case "delete":
			$.ajax({
				type: "post",
				url: nc.url("admin/Config/deleteNotice"),
				data: {
					'notice_id': data.notice_id,
				},
				dataType: "JSON",
				success: function (res) {
					layer.msg(res.message);
					location.reload();
				}
			});
			break;
		case 'edit':
			location.href = nc.url("admin/config/editnotice", {notice_id: data.notice_id});
			break;
		case 'preview':
			window.open(nc.url("admin/config/previewnotice", {notice_id: data.notice_id}));
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
	
	layui_form.on('switch(is_display)', function (obj) {
		var is_display = '';
		obj.elem.checked ? is_display = 1 : is_display = 0;
		$.ajax({
			type: "post",
			url: nc.url("admin/Config/setIsDisplay"),
			data: {
				'notice_id': this.value,
				'is_display': is_display
			},
			dataType: "JSON",
			success: function (res) {
				layer.msg(res.message);
			}
		});
	});
	
	layui_form.on('switch(is_recommend)', function (obj) {
		var is_recommend = '';
		obj.elem.checked ? is_recommend = 1 : is_recommend = 0;
		$.ajax({
			type: "post",
			url: nc.url("admin/Config/setIsRecommend"),
			data: {
				'notice_id': this.value,
				'is_recommend': is_recommend
			},
			dataType: "JSON",
			success: function (res) {
				layer.msg(res.message);
			}
		});
	});
	
});
