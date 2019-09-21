layui.use(['form'], function () {
	var form = layui.form;
	var repeat_flag = false;//防重复标识
	
	form.on('submit(*)', function (data) {
		var val = data.field;
		var name = $("#name").val();
		if (repeat_flag) return;
		repeat_flag = true;
		$.ajax({
			type: "post",
			url: nc.url("admin/system/auth"),
			data: {
				'val': JSON.stringify(val),
				'name': name,
			},
			dataType: "JSON",
			success: function (res) {
				layer.msg(res.message);
				if (res.code == 0) {
					location.reload();
				} else {
					repeat_flag = false;
				}
			}
		});
		return false;
	});
	
	// 授权详情
	form.on('submit(info)', function (data) {
		var val = data.field;
		var name = $("#name").val();
		$.ajax({
			type: "get",
			url: nc.url("admin/system/authInfo"),
			data: {
				'val': JSON.stringify(val),
				'name': name,
			},
			dataType: "JSON",
			success: function (res) {
				layer.msg(res.message);
			}
		});
		return false;
	});
	
	form.verify({
		app_secret: function (value, item) {
			if (value == '') {
				return '请输入授权码秘钥';
			}
		},
		app_key: function (value, item) {
			if (value == '') {
				return '请输入授权码';
			}
		}
	});
	
});

//详情操作
function list() {
	layer.open({
		type: 1,
		title: '授权列表',
		area: ['850px', '300px'],
		offset: 'auto',
		move: false,
		zIndex: 9999,
		content: $('#auth_list'),
		success: function (layero, index) {
			var table = new Table({
				elem: '#auth_table_list',
				url: nc.url("admin/system/getAuthList"),
				cols: [
					[
						{
							field: 'auth_code',
							width: "15%",
							title: '授权码',
							unresize: 'false'
						},
						{
							field: 'title',
							width: "10%",
							title: '绑定应用',
							unresize: 'false'
						},
						{
							field: 'domain',
							width: "15%",
							title: '授权域名',
							unresize: 'false'
						},
						{
							field: 'bind_site',
							width: "15%",
							title: '绑定站点',
							unresize: 'false'
						},
						{
							field: 'status',
							title: '状态', width: "8%",
							unresize: 'false',
							templet: function (d) {
								return d.status == 1 ? "正常" : "已到期";
							}
						},
						{
							field: 'validity_time',
							title: '有效期',
							unresize: 'false',
							templet: function (d) {
								return nc.time_to_date(d.validity_time);
							}
						},
						{
							field: 'create_time',
							title: '创建时间',
							unresize: 'false',
							templet: function (d) {
								return nc.time_to_date(d.create_time);
							}
						},
					]
				],
				page: false
			});
			var mask = $(".layui-layer-shade");
			mask.appendTo(layero.parent());
		},
		cancel: function () {
			layer.close();
		}
	});
}

$('input').change(function () {
	$('.btn-already').html('立即绑定');
});