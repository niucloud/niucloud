var total_count = 0;
$(function () {
	//查询
	$(".search-wrap .sort-wrap").mouseover(function () {
		$(".search-wrap .sort-wrap dl").addClass("show");
	}).mouseout(function () {
		$(".search-wrap .sort-wrap dl").removeClass("show");
	});
	
	//筛选或排序
	$(".search-wrap .sort-wrap dl dd").click(function () {
		$(this).addClass("selected").siblings().removeClass("selected");
		$(".search-wrap .sort-wrap > span").html($(this).find("a").html());
		$(".search-wrap .sort-wrap dl").removeClass("show");
		getGroupManagePageInfo(1, 20);
	});
	
	//选择相册分组
	$('.category-item').click(function () {
		$(this).addClass('active').siblings().removeClass('active');
		var group_name = $(this).children('.category-name').text();
		$('.group_name').text(group_name);
		var is_default = $(this).children("input[name='is_default']").val();
		if (is_default == 1) {
			$("#operation .delete_packet").hide();
		} else {
			$("#operation .delete_packet").show();
		}
		
		getGroupManagePageInfo(1, 20);
		toPage(total_count);
	});
	
	$('.category-item:first-child').addClass('active');
	$('.group_name').text($('.category-item.active').children('.category-name').text());
	//ajax请求后台数据
	getGroupManagePageInfo(1, 20);
	toPage(total_count);
	
});

var repeat_flag_add_group = false;//防重复标识
function addGroup() {
	layer.prompt({title: '添加分组', formType: 3}, function (val, index) {
		if(repeat_flag_add_group) return;
		repeat_flag_add_group = true;
		$.ajax({
			type: "post",
			async: false,
			url: nc.url("File://sitehome/File/addGroup"),
			data: {name: val, type: 'VIDEO'},
			dataType: "JSON",
			success: function (res) {
				if (res.data > 0) {
					layer.msg('添加成功');
					location.reload();
				}else{
					repeat_flag_add_group = false;
				}
			}
		});
		layer.close(index);
	});
	
}

//ajax请求后台数据
function getGroupManagePageInfo(page, limit) {
	var category_id = $(".active>.category-id").val();
	$.ajax({
		type: "get",
		async: false,
		url: nc.url("File://sitehome/File/video"),
		data: {
			page: page, limit: limit, type: 'VIDEO', category_id: category_id,
			order: $(".search-wrap .sort-wrap dl dd.selected").attr("data-order"),
			file_name: $("input[name='search_file_name']").val()
		},
		dataType: "JSON",
		success: function (res) {
			getFileCustomesInfo(res.data);
			total_count = res.data.count;//总页数(后台返回)
		}
	});
}

function getFileCustomesInfo(data) {
	var s = "";
	for (var i = 0; i < data.list.length; i++) {
		var item = data.list[i];
		var size = parseFloat(item.size / 1024 / 1024).toFixed(2);
		s += '<div class="tr">';
		s += '<div class="layui-col-md2">';
		s += '<div style="padding-left: 25px;">';
		s += '<div class="layui-form zent-checkbox-wrap" style="float: left;position:relative">';
		s += '<span class="layui-form-item">';
		s += '<input type="checkbox" name="check[]" lay-skin="primary" title="" value="' + item.id + '" lay-filter="oneChoose">';
		s += '</span>';
		s += '</div>';
		s += '<div class="cell__child-container">';
		s += '<div class="material-image-card">';
		s += '<div class="image-box is-can-play">';
		s += '<div class="image-cover" style="background-image: url(&quot;https://img.yzcdn.cn/upload_files/2018/06/21/fd86e25f212f8c7fa0ae06b71610e0e0.jpg?imageView2/2/w/120/h/120/q/75/format/webp&quot;);"></div>';
		s += '</div>';
		s += '<div class="info-box">';
		s += '<p class="name">' + item.file_name + '.' + item.file_ext + '</p>';
		s += '<p class="size">00:04</p>';
		s += '</div>';
		s += '</div>';
		s += '</div>';
		s += '</div>';
		s += '</div>';
		
		s += '<div class="layui-col-md2">';
		s += '<div>' + size + 'MB</div>';
		s += '</div>';
		
		s += '<div class="layui-col-md2">';
		s += '<div>' + nc.time_to_date(item.create_time) + '</div>';
		s += '</div>';
		
		s += '<div class="layui-col-md2">';
		s += '<div>0</div>';
		s += '</div>';
		
		s += '<div class="layui-col-md2">';
		s += '<div>审核通过</div>';
		s += '</div>';
		
		s += '<div class="layui-col-md2">';
		s += '<a>编辑</a>';
		s += '<span class="opts-seprate">|</span>';
		s += '<a>删除</a>';
		s += '</div>';
		
		s += '</div>';
	}
	
	if (data.list.length > 0) {
		$("#t_customerInfo").html(s);
	} else {
		$("#page1").hide();
		$("#t_customerInfo").html("<br/><span style='width:10%;height:30px;display:block;margin:0 auto;'>暂无数据</span>");
	}
	
}

function toPage(total_count) {
	layui.use(['form', 'laypage'], function () {
		var form = layui.form,
			laypage = layui.laypage;
		form.render('checkbox');
		//全选
		form.on('checkbox(allChoose)', function (data) {
			$("input[name='check[]']").each(function () {
				this.checked = data.elem.checked;
			});
			form.render('checkbox');
		});
		
		form.verify({
			file_name: function (value, item) { //value：表单的值、item：表单的DOM对象
				// if (value.length == 0) {
				// return '请输入图片名称';
				// }
			}
		});
		
		//监听筛选事件
		form.on('submit(search)', function (data) {
			getGroupManagePageInfo(1, 20);
		});
		
		//调用分页
		laypage.render({
			elem: 'paged'
			, count: total_count, //得到总页数，在layui2.X中使用count替代了，并且不是使用"总页数"，而是"总记录条数"
			layout: nc.get_page_param(),
			jump: function (obj, first) {
				if (!first) { //一定要加此判断，否则初始时会无限刷新
					getGroupManagePageInfo(obj.curr, obj.limit);//一定要把翻页的ajax请求放到这里，不然会请求两次。
					form.render('checkbox');
				}
			}
		});
		
	});
}

//分组重命名
function updateGroup() {
	var category_name = $(".active>.category-name").text();
	var category_id = $(".active>.category-id").val();
	//prompt层
	layer.prompt({title: '编辑名称', formType: 3, value: category_name}, function (val, index) {
		$.ajax({
			type: "post",
			async: false,
			url: nc.url("File://sitehome/File/updateGroup"),
			data: {name: val, category_id: category_id},
			dataType: "JSON",
			success: function (res) {
				if (res.data > 0) {
					layer.close(index);
					layer.msg('编辑成功');
					location.reload();
				}
				
			}
		});
		
	});
}

//删除分组
function deletePacket() {
	var category_id = $(".active>.category-id").val();
	layer.confirm('仅删除分组，不删除视频，组内视频将自动归入默认分组', {
		btn: ['确定', '取消']
	}, function () {
		$.ajax({
			type: "post",
			async: false,
			url: nc.url("File://sitehome/File/deleteGroup"),
			data: {category_id: category_id},
			dataType: "JSON",
			success: function (res) {
				if (res.data > 0) {
					layer.msg('删除成功');
					location.reload();
				}
				
			}
		});
	}, function () {
		layer.close();
	});
}

//文件重命名
function rename(file_id, file_name) {
	//prompt层
	layer.prompt({title: '修改名称', formType: 3, value: file_name}, function (val, index) {
		$.ajax({
			type: "post",
			async: false,
			url: nc.url("File://sitehome/File/updateFileName"),
			data: {file_name: val, file_id: file_id},
			dataType: "JSON",
			success: function (res) {
				if (res.data > 0) {
					layer.close(index);
					layer.msg('编辑成功');
					location.reload();
				}
				
			}
		});
		
	});
}

//链接文件
function pathLink(path) {
	$("#path_file").val(path);
	layer.open({
		type: 1,
		shadeClose: true,
		shade: false,
		area: ['550px', '130px'], //宽高
		content: $('#copy_path'),
		cancel: function () {
			//右上角关闭回调
			$("#copy_path").hide();
			//return false 开启该代码可禁止点击该按钮关闭
		}
	});
}

//复制路径
function JScopy() {
	nc.copy("path_file",function(res){
		$("#hidden_image_path").val(res.url);
	})
}

//分组文件
function grouping(id) {
	var category_id = $(".active>.category-id").val();
	$("#modify_group input[name='oldGroup']").each(function (i, item) {
		if ($(item).val() == category_id) {
			$(item).prop('checked', true);
			layui.use(['form'], function () {
				var form = layui.form;
				form.render();
			});
		}
	});
	
	layui.use(['form'], function () {
		var form = layui.form;
		form.on('radio(example)', function (data) {
			$("#hidden_category_id").val(data.value);
		});
		
	});
	
	layer.confirm($('#modify_group'), {
		type: 1,
		title: false,
		shadeClose: true,
		closeBtn: 0,
		btn: ['确认', '取消']
	}, function () {
		
		var hidden_category_id = $("#hidden_category_id").val() ? $("#hidden_category_id").val() : category_id;
		$.ajax({
			type: "post",
			async: false,
			url: nc.url("File://sitehome/File/updateImageGroup"),
			data: {id: id.toString(), category_id: hidden_category_id},
			dataType: "JSON",
			success: function (res) {
				if (res.data > 0) {
					layer.close();
					layer.msg('编辑成功');
					location.reload();
				}
				
			}
		});
	}, function () {
		layer.close();
	});
	
}

//删除文件
function deleteFile(id) {
	layer.confirm('若删除，不会对目前已使用该视频的相关业务造成影响。', {
		btn: ['确定', '取消']
	}, function () {
		$.ajax({
			type: "post",
			async: false,
			url: nc.url("File://sitehome/File/deleteFile"),
			data: {id: id.toString()},
			dataType: "JSON",
			success: function (res) {
				if (res.data > 0) {
					layer.msg('删除成功');
					location.reload();
				}
				
			}
		});
	}, function () {
		layer.close();
	});
}

//批量操作修改分组
function groupingAll() {
	var ids = new Array();
	$("input[name='check[]']:checked").each(function () {
		if (!isNaN($(this).val())) {
			ids.push($(this).val());
		}
	});
	if (ids.length == 0) {
		layer.msg("请选择视频");
		return false;
	}
	grouping(ids);
}

//批量操作删除
function deleteAll() {
	var ids = new Array();
	$("input[name='check[]']:checked").each(function () {
		if (!isNaN($(this).val())) {
			ids.push($(this).val());
		}
	});
	if (ids.length == 0) {
		layer.msg("请选择视频");
		return false;
	}
	deleteFile(ids);
}