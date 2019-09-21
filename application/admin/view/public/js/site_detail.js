var table = new Table({
	elem: '#site_list',
	filter: "site_list",
	where: {
		site_id: site_id,
		uid: uid
	},
	url: nc.url('admin/site/getModuleInformationList'),
	cols: [[
		{
			field: 'site_style',
			width: '30%',
			title: '插件信息',
			templet: '#site_name',
		},
		{
			field: 'site_style',
			width: '20%',
			title: '插件类型',
			templet: '#site_style',
		},
		{
			width: '15%',
			title: '支持端口',
			templet: '#support_app_type_arr',
		},
		{
			field: 'create_time',
			width: '20%',
			title: '安装时间',
			templet: function (data) {
				return data.create_time == 0 ? '永久' : nc.time_to_date(data.create_time);
			}
		},
		{
			field: 'version',
			width: '15%',
			title: '版本',
			align: 'right',
		}
	]],
	size: 'lg'
});