$("#back").click(function () {
	$("#show").hide();
	$(".nc-quote-box").show();
});

function selectSite(that) {
	$(".nc-quote-box").hide();
	$("#show").show();
	var select_addon = $(that).attr("data-addon");
	var select_title = $(that).attr("data-title");
	
	$(".el-site-type").html(select_title);
	$("#select_addon").val(select_addon);
	$(".create-progress .progress-point:nth-child(3) span").css({
		'background-color': '#12b7f5',
		'color': '#fff',
	});
	$(".create-progress .progress-point:nth-child(3) p").css('color', "#12b7f5");
	$(".progress-bar:nth-child(2) div").css("background-color", "#12b7f5");
}

$('.return').click(function () {
	$(".create-progress .progress-point:nth-child(3) span").css({
		'background-color': '#f9f9f9',
		'color': '#999',
	});
	$(".create-progress .progress-point:nth-child(3) p").css('color', "#999");
	$(".progress-bar:nth-child(2) div").css("background-color", "#f9f9f9");
});

function inDevelopment() {
	layer.alert('开发中，敬请期待', {
		icon: 6,
		title: '提示'
	});
}

layui.use('form', function () {
	var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
	form.verify({
		title: function (value) {
			if (value.length < 1) {
				return '请输入站点';
			}
		}
	});
	
	var repeat_flag = false;//防重复标识
	var element;
	form.on('submit(add_site)', function (data) {
		if (repeat_flag) return;
		repeat_flag = true;
		var site_name = data.field.site_name;
		var addon_app = data.field.addon_app;
		var description = data.field.description;
		$(".create-site").hide();
		$(".create-site-end").show();
		var n = 0, timer;
		layui.use('element', function () {
			element = layui.element;
			
			if ($(".layui-progress").hasClass('DISABLED')) return;
			
			timer = setInterval(function () {
				n += 10;
				element.progress('demo', n + '%');
			}, 300);
		});
		
		$.ajax({
			type: "post",
			url: nc.url('admin/site/addSite'),
			data: {
				'site_name': site_name,
				'desc': description,
				"addon_app": addon_app
			},
			dataType: "JSON",
			success: function (res) {
				if (res['code'] == 0) {
					clearInterval(timer);
					element.progress('demo', '100%');
					setTimeout(function () {
						$(".create-site-pic-one,.create-progress-bar,.create-site-content").hide();
						$(".layui-progress").addClass('DISABLED');
						$(".create-progress .progress-point:nth-child(5) span").css({
							'background-color': '#12b7f5',
							'color': '#fff',
						});
						$(".create-progress .progress-point:nth-child(5) p").css('color', "#12b7f5");
						$(".progress-bar:nth-child(4) div").css("background-color", "#12b7f5");
						
						$(".create-site-pic-two,.create-site-content-one, .create-site-content-btn").show();
						$(".create-site-content-btn").attr("href", nc.url('/sitehome/index/index', {site_id: res.data}));
					}, 1000);
				} else {
					repeat_flag = false;
				}
			}
		});
	});
});