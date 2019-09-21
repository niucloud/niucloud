var table = new Table({
	elem: '#cron_list',
	filter: "cron",
	url: nc.url('admin/cron/cronlist'),
	cols: [
		[
			{
				field: 'cron_name',
				width: '13%',
				title: '任务名称'
			},
			{
				field: 'cron_desc',
				width: '10%',
				title: '说明'
			},
			{
				field: 'cron_period',
				width: '10%',
				title: '执行周期单位',
				templet: '#cron_period',
			},
			{
				field: 'cron_hook',
				width: '20%',
				title: '执行任务钩子'
			},
			{
				field: 'cron_addon',
				width: '15%',
				title: '任务插件'
			},
			{
				field: 'status',
				width: '10%',
				title: '状态',
				templet: '#status',
			},
			{
				title: '操作',
				toolbar: '#operation',
				align: 'right'
			}
		]
	],
});

table.tool(function (obj) {
	var data = obj.data;
	if (obj.event === 'record') {
		window.location.href = nc.url('admin/cron/cronExecuteList', {"cron_id": data.cron_id});
	} else if (obj.event === 'detail') {
	}
});


layui.use(['form'], function () {
	var form = layui.form;
	
	//监听状态操作
	form.on('switch(status)', function (obj) {
		$.ajax({
			type: "post",
			url: nc.url("admin/cron/modifyCronStatus"),
			data: {
				'cron_id': this.value,
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

function setCron() {
	layer.open({
		type: 1,
		shadeClose: true,
		shade: 0,
		scrollbar: false,
		title: '任务设置',
		area: ['550px', '360px'],
		content: $('#cron_action_box'),
		cancel: function (index, layero) {
			//右上角关闭回调
			layer.close(index);
			$("#cron_action_box").html("");
		},
		btn: ['保存'],
		yes: function (index, layero) {
			$("#button_set").click();
			layer.close(index);
			$("#cron_action_box").html("");
		},
		success: function (layero, index) {
			layui.use(['form', 'laytpl'], function () {
				var laytpl = layui.laytpl;
				var form = layui.form;
				//监听是否启用操作
				var tpl = $("#cron_action_html").html();
				
				laytpl(tpl).render([], function (html) {
					$('#cron_action_box').html(html);
					form.render();
				});
				
				//监听搜索提交
				var repeat_flag = false;//防重复标识
				form.on('submit(save)', function (data) {
					var field = data.field;
					if(repeat_flag) return;
					repeat_flag = true;
					$.ajax({
						type: "post",
						url: nc.url("admin/cron/setCron"),
						data: field,
						dataType: "JSON",
						success: function (res) {
							if (data.field.type == "php") {
								layer.msg(res.message);
							} else {
								//示范一个公告层
								var html = "";
								if (data.field.type == "window") {
									html += '<div style="padding: 50px; line-height: 22px;  font-weight: 300;"><b>bat命令：</b><br><p id="bat_text" type="window">' + res.data + '</p></div>';
									var btn_array = ['下载', '取消'];
								} else {
									html += '<div style="padding: 50px; line-height: 22px;  font-weight: 300;"><b>linux命令：</b><br><p id="bat_text"  type="linux">' + res.data + '</p></div>';
									var btn_array = ['确定'];
								}
								
								layer.open({
									type: 1
									, title: false //不显示标题栏
									, closeBtn: false
									, area: '500px;'
									, shade: 0.3
									, id: 'lay_layuipro' //设定一个id，防止重复弹出
									, resize: false
									, btn: btn_array,
									yes: function (index, layero) {
										if ($("#bat_text").attr("type") == "window") {
											funDownload($("#bat_text").text(), "NiucloudCron.bat");
											return false;
										} else {
											layer.close(index);
										}
									}
									, btnAlign: 'c'
									, moveType: 1 //拖拽模式，0或者1
									, content: html
									, success: function (layero) {
									
									}
								});
							}
							repeat_flag = false;
						}
					});
				});
			})
			
		}
	})
}

// 下载文件方法
var funDownload = function (content, filename) {
	var eleLink = document.createElement('a');
	eleLink.download = filename;
	eleLink.style.display = 'none';
	// 字符内容转变成blob地址
	var blob = new Blob([content]);
	eleLink.href = URL.createObjectURL(blob);
	// 触发点击
	document.body.appendChild(eleLink);
	eleLink.click();
	// 然后移除
	document.body.removeChild(eleLink);
};