var laytpl, index, form;
layui.use(['laytpl', 'form'], function () {
	laytpl = layui.laytpl;
	form = layui.form;
	
	//修改原因
	form.on('submit(reasons_for_closing_submit)', function (data) {
		
		system_site_config.reasons_for_closing = data.field.reasons_for_closing;
		$.ajax({
			type: "post",
			url: nc.url("admin/config/setsystemconfig"),
			dataType: 'json',
			data: system_site_config,
			success: function (res) {
				back();
				if (res.code == 0) {
					location.reload();
				} else {
					layer.msg(res.message);
				}
			}
		})
	});
	
	//网站标题
	form.on('submit(title_submit)', function (data) {
		system_site_config.title = data.field.title;
		$.ajax({
			type: "post",
			url: nc.url("admin/config/setsystemconfig"),
			dataType: 'json',
			data: system_site_config,
			success: function (res) {
				back();
				if (res.code == 0) {
					location.reload();
				} else {
					layer.msg(res.message);
				}
			}
		})
	});
	
	//网站关键词
	form.on('submit(keywords_submit)', function (data) {
		system_site_config.keywords = data.field.keywords;
		$.ajax({
			type: "post",
			url: nc.url("admin/config/setsystemconfig"),
			dataType: 'json',
			data: system_site_config,
			success: function (res) {
				back();
				if (res.code == 0) {
					location.reload();
				} else {
					layer.msg(res.message);
				}
			}
		})
	});
	
	//网站关键词
	form.on('submit(description_submit)', function (data) {
		system_site_config.description = data.field.description;
		$.ajax({
			type: "post",
			url: nc.url("admin/config/setsystemconfig"),
			dataType: 'json',
			data: system_site_config,
			success: function (res) {
				back();
				if (res.code == 0) {
					location.reload();
				} else {
					layer.msg(res.message);
				}
			}
		})
	});
	
	//备案号
	form.on('submit(icp_submit)', function (data) {
		system_site_config.icp = data.field.icp;
		$.ajax({
			type: "post",
			url: nc.url("admin/config/setsystemconfig"),
			dataType: 'json',
			data: system_site_config,
			success: function (res) {
				back();
				if (res.code == 0) {
					location.reload();
				} else {
					layer.msg(res.message);
				}
			}
		})
	});
	
	//联网备案信息
	form.on('submit(police_icp_submit)', function (data) {
		system_site_config.police_icp_location = data.field.police_icp_location;
		system_site_config.police_icp_code = data.field.police_icp_code;
		$.ajax({
			type: "post",
			url: nc.url("admin/config/setsystemconfig"),
			dataType: 'json',
			data: system_site_config,
			success: function (res) {
				back();
				if (res.code == 0) {
					location.reload();
				} else {
					layer.msg(res.message);
				}
			}
		})
	});
	
	//是否开启站点
	form.on('switch(close_site_status)', function (obj) {
		system_site_config.close_site_status = obj.elem.checked ? 1 : 0;
		if(system_site_config.close_site_status) {
			var index = layer.confirm('关闭站点后，只有系统管理员可以访问', {
				btn: ['确定', '取消']
			}, function () {
				$.ajax({
					type: "post",
					url: nc.url("admin/config/setsystemconfig"),
					dataType: 'json',
					data: system_site_config,
					success: function (res) {
						if (res.code == 0) {
							location.reload();
						} else {
							layer.msg(res.message);
						}
					}
				});
			}, function () {
				layer.close();
			});
		}else{
			$.ajax({
				type: "post",
				url: nc.url("admin/config/setsystemconfig"),
				dataType: 'json',
				data: system_site_config,
				success: function (res) {
					if (res.code == 0) {
						location.reload();
					} else {
						layer.msg(res.message);
					}
				}
			});
		}
		
	});
	
	//是否开启日志
	form.on('switch(log_status)', function (obj) {
		var log_status = '';
		obj.elem.checked ? log_status = true : log_status = false;
		$.ajax({
			type: "post",
			url: nc.url("admin/config/siteconfig"),
			data: {
				'log_status': log_status
			},
			dataType: "JSON",
			success: function (res) {
				if (res.code == 0) {
					location.reload();
				} else {
					layer.msg(res.message);
				}
			}
		});
	});
	
	//是否开启debug
	form.on('switch(develop_status)', function (obj) {
		var develop_status = '';
		obj.elem.checked ? develop_status = true : develop_status = false;
		$.ajax({
			type: "post",
			url: nc.url("admin/config/siteconfig"),
			data: {
				'develop_status': develop_status
			},
			dataType: "JSON",
			success: function (res) {
				if (res.code == 0) {
					location.reload();
				} else {
					layer.msg(res.message);
				}
			}
		});
	});
});

function editLayer(type, title, width, height) {
	laytpl($("#" + type).html()).render([], function (html) {
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
