var limit = 100;
var first = true;
var total = 0;
var laytpl, laypage, form;
layui.use(['laypage', 'laytpl', 'form'], function () {
	laytpl = layui.laytpl;
	laypage = layui.laypage;
	form = layui.form;
	
	getAdminAddonList(location.hash.replace('#!page=', '') || 1);
	
	form.on("checkbox(installType)", function (data) {
		//如果两个都选中则表示加载已安装、未安装的应用
		getAdminAddonList(1, {install_type: ($("input[name='install_type']:checked").length == 1) ? $("input[name='install_type']:checked").val() : ""})
	});
	
	form.on('submit(search)', function (data) {
		console.log(data);
		getAdminAddonList(1, {addon_name: data.field.addon_name});
	})
	
});

function getAdminAddonList(page, where) {
	var data = {page: page, type: type};
	if (where != undefined) Object.assign(data, where);
	$.ajax({
		type: "post",
		url: nc.url("admin/addon/addonlist"),
		data: data,
		success: function (res) {
			if (res.length > 0) {
				total = res.length;
				$(".empty-site").hide();
				$("#application").show();
				laytpl($("#application_module").html()).render(res, function (html) {
					$("#application").html(html);
				});
			} else {
				$("#application").hide();
				$(".empty-site").show();
			}
		}
	})
}

function getPage() {
	laypage.render({
		elem: 'page',
		count: total,
		limit: limit,
		curr: location.hash.replace('#!page=', ''),
		hash: 'page',
		layout: nc.get_page_param(),
		jump: function (obj, first) {
			//首次不执行
			if (!first) {
				getAdminAddonList(obj.curr);
			}
		}
		
	});
}

//安装
function install(addon_name, is_buy, version) {
	
	$.ajax({
		type: "post",
		url: nc.url("admin/addon/install"),
		data: {
			'addon_name': addon_name,
			'is_buy': is_buy,
			'version': version,
		},
		beforeSend: function () {
			layer.load(2)
		},
		dataType: "JSON",
		success: function (res) {
			layer.closeAll();
			layer.msg(res.message);
			if (res.code == 0) {
				getAdminAddonList(location.hash.replace('#!page=', '') || 1);
			}
		}
	});
}

//卸载
function uninstall(addon_name) {
	layer.confirm('该插件在卸载的同时，也会将上面的数据清除，您确定继续卸载吗？', {title: '提示'}, function (index) {
		layer.closeAll();
		$.ajax({
			type: "post",
			url: nc.url("admin/addon/uninstall"),
			data: {
				'addon_name': addon_name
			},
			beforeSend: function () {
				layer.load(2)
			},
			dataType: "JSON",
			success: function (res) {
				layer.closeAll();
				layer.msg(res.message);
				if (res.code == 0) {
					getAdminAddonList(location.hash.replace('#!page=', '') || 1);
				}
			}
		});
		
	});
}

//升级
function upgrade(addon_name, version) {
	$.ajax({
		type: "post",
		url: nc.url("admin/addon/upgrade"),
		data: {
			'addon_name': addon_name,
			'version': version
		},
		dataType: "JSON",
		success: function (res) {
			layer.msg(res.message);
			if (res.code == 0) {
				getAdminAddonList(location.hash.replace('#!page=', '') || 1);
			}
		}
	});
}

//重置菜单
function initMenu(addon_name) {
	
	layer.confirm('该操作会重置本模块相关的业务数据，您确定继续刷新吗？', {title: '提示'}, function (index) {
		layer.closeAll();
		$.ajax({
			type: "post",
			url: nc.url("admin/addon/initMenu"),
			data: {
				'addon_name': addon_name,
			},
			beforeSend: function () {
				layer.load(2);
			},
			dataType: "JSON",
			success: function (res) {
				layer.closeAll();
				layer.msg(res.message);
			}
		});
	});
}

// 版本更新详情
function versionList(data) {
	var list = data['version_list'];
	var html = '';
	html += '<div class="layui-form">';
	html += '<table class="layui-table">';
	html += '<colgroup><col width="30%"><col width="20%"><col width="30%"><col></colgroup>';
	html += '<thead><tr><th>插件名称</th><th>版本号</th><th>创建时间</th><th>操作</th></tr></thead>';
	html += '<tbody>';
	var create_time = undefined;
	for (var i = 0; i < list.length; i++) {
		html += '<tr>';
		html += '<td>' + list[i]['addon_name'] + '</td>';
		html += '<td>' + list[i]['version'] + '</td>';
		html += '<td>' + nc.time_to_date(list[i]['create_time']) + '</td>';
		html += '<td>';
		if (data.is_install) {
			if (data.version == list[i]['version']) {
				html += '<span>当前版本</span>';
				create_time = list[i]['create_time'];
			} else {
				if (list[i]['create_time'] > create_time && create_time != undefined) {
					html += '<a onclick="updateVersion(\'' + list[i]['addon_name'] + '\', ' + data.is_buy + ', \'' + list[i]['version'] + '\')" class="default">更新</a>';
				} else {
					html += '<span></span>';
				}
			}
		} else {
			html += '<a onclick="installAddon(\'' + list[i]['addon_name'] + '\', ' + data.is_buy + ', \'' + list[i]['version'] + '\')" class="default">安装</a>';
		}
		html += '</td>';
		html += '<tr>';
	}
	html += '</tbody>';
	html += '</table>';
	html += '</div>';
	
	layer.open({
		type: 1,
		title: '更新版本信息',
		closeBtn: 1,
		shadeClose: true,
		area: '800px',
		btn: '关闭',
		content: html
	});
}

//安装
function installAddon(addon_name, is_buy, version) {
	$.ajax({
		type: "post",
		url: nc.url("admin/addon/install"),
		data: {
			'addon_name': addon_name,
			'is_buy': is_buy,
			'version': version,
		},
		beforeSend: function () {
			layer.load(2)
		},
		success: function (res) {
			layer.closeAll();
			layer.msg(res.message);
			if (res.code == 0) {
				location.reload();
			}
		}
	});
}

function updateVersion(addon_name, is_buy, version) {
	$.ajax({
		type: "post",
		url: nc.url("admin/addon/upgrade"),
		data: {
			'addon_name': addon_name,
			'is_buy': is_buy,
			'version': version,
		},
		beforeSend: function () {
			layer.load(2)
		},
		success: function (res) {
			layer.closeAll();
			layer.msg(res.message);
			if (res.code == 0) {
				location.reload();
			}
		}
	});
}