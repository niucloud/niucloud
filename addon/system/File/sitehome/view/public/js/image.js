$(function () {
	//筛选或排序
	$(".search-wrap .sort-wrap").mouseover(function () {
		$(".search-wrap .sort-wrap dl").addClass("show");
	}).mouseout(function () {
		$(".search-wrap .sort-wrap dl").removeClass("show");
	});
	//选择相册分组
	$(".search-wrap .sort-wrap dl dd").click(function () {
		$(this).addClass("selected").siblings().removeClass("selected");
		$(".search-wrap .sort-wrap > span").html($(this).find("a").html());
		$(".search-wrap .sort-wrap dl").removeClass("show");
		getFileAlbumList(1, limit);
	});
	
	$('.group_name').text($('.category-item.active').children('.category-name').text());
});

var form;
var laypage;
var laytpl;
var limit = 20;

layui.use(['form', 'laypage', 'laytpl'], function () {
	laytpl = layui.laytpl;
	form = layui.form;
	laypage = layui.laypage;
	
	//分组绑定事件
	$('body').on("click", ".category-item", function () {
		$(this).addClass('active').siblings().removeClass('active');
		var group_name = $(this).children('.category-name').text();
		$('.group_name').text(group_name);
		var is_default = $(this).children("input[name='is_default']").val();
		if (is_default == 1) {
			$("#operation .delete_packet").hide();
		} else {
			$("#operation .delete_packet").show();
		}
		getFileAlbumList(1, limit);
	});
	
	getFileAlbumList(1, limit); //分组文件
	
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
			// 	return '请输入图片名称';
			// }
		}
	});
	
	//监听筛选事件
	form.on('submit(search)', function (data) {
		getFileAlbumList(1, limit); //分组文件
	});
	
	//文件改名
	$('body').on("click", ".file-rename", function () {
		var file_name = $(this).parent().children('.file_name').val();
		var file_id = $(this).parent().children('.file_id').val();
		layer.prompt({
			title: '修改名称',
			formType: 3,
			value: file_name
		}, function (val, index) {
			$.ajax({
				type: "post",
				async: true,
				url: nc.url("File://sitehome/File/updateFileName"),
				data: {
					file_name: val,
					file_id: file_id
				},
				dataType: "JSON",
				success: function (data) {
					var index = layer.index;
					layer.close(index);
					layer.msg(data.message);
					if (data.code == 0) {
						getFileAlbumList(1, 20);
					}
				}
			});
			
		});
	});
	
	//链接
	$('body').on("click", ".pathlink", function () {
		var path = $(this).parent().children('.file_path').val();
		var tpl_html = $("#copy_path_html").html();
		laytpl(tpl_html).render({"path": path}, function (html) {
			layer.open({
				type: 1,
				title: "复制图片地址",
				area: ['450px'], //宽高
				content: html,
				// cancel: function () {
				// 	//右上角关闭回调
				// 	$("#copy_path").hide();
				// },
				success: function (layero) {
					var mask = $(".layui-layer-shade");
					layero.parent().append(mask);
				}
			});
		});
		form.render();//重载form表单
	});
	
	//文件分组
	$('body').on("click", ".grouping", function () {
		var file_id = $(this).parent().children('.file_id').val();
		grouping(file_id);
	});
	
	//删除文件
	$('body').on("click", ".delfile", function () {
		var file_id = $(this).parent().children('.file_id').val();
		deleteFile(file_id);
	});
	
	//添加分组
	var repeat_flag_add_group = false;//防重复标识
	$('body').on("click", ".add-group", function () {
		layer.prompt({
			title: '添加分组',
			formType: 3,
		}, function (val, index,elem) {
			if(repeat_flag_add_group) return;
			repeat_flag_add_group = true;
			$.ajax({
				type: "post",
				url: nc.url("File://sitehome/File/addGroup"),
				data: {
					name: val,
					type: 'IMAGE'
				},
				dataType: "JSON",
				success: function (data) {
					var index = layer.index;
					layer.close(index);
					layer.msg(data.message);
					if (data.code == 0) {
						location.reload();
						getFileAlbumList(1, 20);
					}else{
						repeat_flag_add_group = false;
					}
					
				}
			});
		});
	});
	
	//分组重命名
	$('body').on("click", ".rename", function () {
		var category_name = $(".active>.category-name").text();
		var category_id = $(".active>.category-id").val();
		layer.prompt({
			title: '编辑名称',
			formType: 3,
			value: category_name
		}, function (val, index) {
			$.ajax({
				type: "post",
				async: true,
				url: nc.url("File://sitehome/File/updateGroup"),
				data: {
					name: val,
					category_id: category_id
				},
				dataType: "JSON",
				success: function (data) {
					var index = layer.index;
					layer.close(index);
					layer.msg(data.message);
					if (data.code == 0) {
						location.reload();
						getFileAlbumList(1, 20);
						$('.group_name').text(val);
					}
					
				}
			});
			
		});
	});
	
	//删除分组
	$('body').on("click", ".delete_packet", function () {
		var category_id = $(".active>.category-id").val();
		layer.confirm('仅删除分组，不删除图片，组内图片将自动归入默认分组', {
			btn: ['确定', '取消']
		}, function () {
			$.ajax({
				type: "post",
				async: true,
				url: nc.url("File://sitehome/File/deleteGroup"),
				data: {
					category_id: category_id
				},
				dataType: "JSON",
				success: function (data) {
					var index = layer.index;
					layer.close(index);
					layer.msg(data.message);
					if (data.code == 0) {
						location.reload();
						getFileAlbumList(1, 20);
						$('.group_name').text('默认分组');
						$("#operation").hide();
					}
				}
			});
		}, function () {
			layer.close();
		});
	});
	
	//批量修改文件分组
	$('body').on("click", ".grouping-all", function () {
		var ids = "";
		$("input[name='check[]']:checked").each(function () {
			if (ids == "") {
				ids = $(this).val();
			} else {
				ids += "," + $(this).val();
			}
		});
		if (ids == "") {
			layer.msg("请选择图片");
			return false;
		}
		grouping(ids);
	});
	
	//批量删除文件
	$('body').on("click", ".delete-all", function () {
		var ids = "";
		$("input[name='check[]']:checked").each(function () {
			if (ids == "") {
				ids = $(this).val();
			} else {
				ids += "," + $(this).val();
			}
		});
		if (ids == "") {
			layer.msg("请选择图片");
			return false;
		}
		deleteFile(ids);
	});
	
	//分组操作
	function grouping(id) {
		var category_id = $(".active>.category-id").val();
		var tpl_html = $("#group_html").html();
		laytpl(tpl_html).render({"category_id": category_id}, function (html) {
			
			layer.confirm(html, {
				type: 1,
				title: '分组',
				shadeClose: true,
				closeBtn: 1,
				btn: ['确认', '取消'],
				success: function (layero) {
					var mask = $(".layui-layer-shade");
					layero.parent().append(mask);
				}
			}, function () {
				var category_id = $("input[name='group_id']:checked").val();
				$.ajax({
					type: "post",
					async: true,
					url: nc.url("File://sitehome/File/updateImageGroup"),
					data: {
						id: id.toString(),
						category_id: category_id
					},
					dataType: "JSON",
					success: function (data) {
						var index = layer.index;
						layer.close(index);
						layer.msg(data.message);
						if (data.code == 0) {
							getFileAlbumList(1, 20);
							getFileCategory("IMAGE");
						}
					}
				});
			}, function () {
				layer.close();
			});
		});
		form.render();//重载form表单
	}
	
	//删除文件操作
	function deleteFile(id) {
		layer.confirm('若删除，不会对目前已使用该图片的相关业务造成影响。', {
			btn: ['确定', '取消']
		}, function () {
			$.ajax({
				type: "post",
				async: true,
				url: nc.url("File://sitehome/File/deleteFile"),
				data: {id: id},
				dataType: "JSON",
				success: function (data) {
					var index = layer.index;
					layer.close(index);
					layer.msg(data.message);
					if (data.code == 0) {
						getFileAlbumList(1, 20);
						getFileCategory("IMAGE");
					}
				}
			});
		}, function () {
			layer.close();
		});
	}
});

//复制路径
function JScopy() {
	nc.copy("path_file",function(res){
		$("#hidden_image_path").val(res.url);
	});
}

//获取相册数据
function getFileAlbumList(page, limit) {
	var category_id = $(".active .category-id").val();
	$.ajax({
		type: "get",
		url: nc.url("File://sitehome/File/image"),
		data: {
			page: page,
			limit: limit,
			type: 'IMAGE',
			category_id: category_id,
			order: $("#img_order").val(),
			file_name: $("input[name='search_file_name']").val()
		},
		async: false,
		dataType: "JSON",
		success: function (data) {
			//渲染相册
			var album_tpl = $("#album_html").html();
			laytpl(album_tpl).render(data.data, function (html) {
				$("#t_customerInfo").html(html);
				loadImgMagnify();
			});
			
			$('.image-data').hide();
			if (data.data.count > 0) {
				//调用分页
				laypage.render({
					elem: 'paged',
					count: data.data.count,
					curr: page, //当前页
					layout: nc.get_page_param(),
					prev: '<i class="layui-icon layui-icon-left"></i>',
					next: '<i class="layui-icon layui-icon-right"></i>',
					limit: limit,
					jump: function (obj, first) {
						if (!first) { //一定要加此判断，否则初始时会无限刷新
							getFileAlbumList(obj.curr, obj.limit);//一定要把翻页的ajax请求放到这里，不然会请求两次。
						}
						form.render('checkbox');
					}
				});
				$('.image-data').show();
			}
			
		}
	});
}

/**
 * 获取文件分类
 * @param type
 */
function getFileCategory(type) {
	$.ajax({
		type: "post",
		url: nc.url("File://sitehome/File/getFileCategory"),
		data: {type: type},
		dataType: "JSON",
		success: function (res) {
			if (res.data) {
				var category_tpl = $("#category").html();
				
				if ($("#t_category .active .category-id").length > 0) {
					res.data.category_id = $(".active .category-id").val();
				} else {
					res.data.category_id = 0;
				}
				laytpl(category_tpl).render(res.data, function (html) {
					$("#t_category").html(html);
				});
				
			}
		}
	});
}