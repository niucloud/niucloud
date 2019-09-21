$(function () {
	
	layui.use(['element'], function () {
		var element = layui.element;
		
		//获取hash来切换选项卡
		var layid = location.hash.replace(/^#tab=/, '');
		
		if (layid == "") layid = "basic_info";
		
		element.tabChange('addon_tab', layid);
		
		//监听Tab切换，以改变地址hash值
		element.on('tab(addon_tab)', function () {
			location.hash = 'tab=' + this.getAttribute('lay-id');
		});
	});
	
	var table = new Table({
		elem: '#site_list',
		url: nc.url("admin/addon/addondetail"),
		where: {
			name: name
		},
		cols: [
			[
				{
					field: 'site_name',
					width: '20%',
					title: '站点名称',
				},
				{
					field: 'title',
					width: '30%',
					title: '站点类型'
				},
				{
					field: 'status',
					width: '20%',
					title: '站点状态',
					templet: '#status'
				},
				{
					title: '到期时间',
					align: 'center',
					templet: function (data) {
						return data.validity_time == 0 ? '永久' : nc.time_to_date(data.validity_time);
					}
				}
			]
		]
	});
});

//安装
function install(addon_name, is_buy, version) {
	var loading = layer.load(2);
	$.ajax({
		type: "post",
		url: nc.url("admin/addon/install"),
		data: {
			'addon_name': addon_name,
			'is_buy': is_buy,
			'version': version,
		},
		dataType: "JSON",
		success: function (res) {
			layer.msg(res.message);
			layer.close(loading);
			if (res.code == 0) {
				location.reload();
			}
		}
	});
}

//卸载
function uninstall(addon_name) {
	layer.confirm('该插件在卸载的同时，也会将上面的数据清除，您确定继续卸载吗？', {title: '提示'}, function (index) {
		var loading = layer.load(2);
		$.ajax({
			type: "post",
			url: nc.url("admin/addon/uninstall"),
			data: {
				'addon_name': addon_name
			},
			dataType: "JSON",
			success: function (res) {
				layer.msg(res.message);
				layer.close(loading);
				if (res.code == 0) location.reload();
			}
		});
		layer.close(index);
	});
}