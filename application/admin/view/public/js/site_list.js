var table = new Table({
	elem: '#site_list',
	filter: "site_list",
	where: {
		search_text: $('.search_text').val(),
		search_type: $('.search_type').val(),
		uid: uid
	},
	url: nc.url("admin/site/siteList"),
		cols: [[{
			field: 'site_name',
			width: '40%',
			title: '站点名称',
			templet: '#site_name',
		},
		{
			field: 'site_style',
			width: '15%',
			title: '站点类型',
			templet: '#site_style',
		},
		{
			field: 'username',
			width: '10%',
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
			width: '10%',
			title: '到期时间',
			templet: function (data) {
				return data.validity_time == 0 ? '永久' : nc.time_to_date(data.validity_time);
			}
		},
		{
			title: '操作',
			width: '10%',
			toolbar: '#operation',
			align: 'right',
		},
		{
			field: 'more-info',
			width: '100%',
			templet: '#more_info',
			hide: true
		}
	]],
	size: "lg"
});

//监听工具条
table.tool(function (obj) {
	var data = obj.data;
	switch (obj.event) {
		case 'deleteSite':
			deleteSite(data.site_id);
			break;
		case 'basic_info':
			location.href = nc.url("admin/site/siteDetail", {
				site_id: data.site_id,
				tab: "basic_info"
			});
			break;
		case 'founder_info':
			location.href = nc.url("admin/site/siteDetail", {
				site_id: data.site_id,
				tab: "founder_info"
			});
			break;
		case 'module_info':
			location.href = nc.url("admin/site/siteDetail", {
				site_id: data.site_id,
				tab: "module_info"
			});
			break;
	}
});

function deleteSite(siteId){
	layer.confirm('确定要删除该站点吗？', { btn: ['确认', '取消']},
		function() {
	    	$.ajax({
				type: "post",
				url: nc.url("admin/site/deleteSite"),
				data: {
					'site_id': siteId,
				},
				dataType: "JSON",
				success: function (res) {
					layer.msg(res.message);
					location.reload();
				}
			});
		}
	);
}

layui.use(['form'], function () {
	var form = layui.form;
	form.on('submit(search)', function(data){
		table.reload({
			page: {
				curr: 1
			},
			where: data.field
		});
	  });
});