var layer_index = '';
var add_layer_index = '';
var limit = 15;
var laytpl;
var laypage;
var repeat_flag = false;//防重复标识

function chooseMaterial(type) {
	loadMaterialList(type);
	var content_id = $('#marterial_graphic_message');
	if (type == 1) {
		content_id = $('#marterial_graphic_message');
	} else if (type == 2) {
		content_id = $('#material_image');
	} else if (type == 5) {
		content_id = $('#material_text');
	}
	layer_index = layer.open({
		type: 1,
		title: false,
		//	shade: [0],
		area: ['800px', '450px'],
		content: content_id,
		success: function (layero, index) {
			var mask = $(".layui-layer-shade");
			mask.appendTo(layero.parent());
		}
	});
}

/**
 * 素材列表
 */
function loadMaterialList(type) {
	if (type == 1) {
		var table = new Table({
			elem: '#marterial_graphic_message_list',
			filter: "marterial_graphic_message",
			width: '780',
			url: nc.url("Wechat://sitehome/material/lists"),
			where: {type: 1, limit},
			cols: [[
				{field: 'value', width: '30%', title: '标题', align: 'center', templet: '#graphic_message_title'},
				{field: 'create_time', width: '20%', title: '创建时间', align: 'center', templet: '#create_time'},
				{field: 'update_time', width: '20%', title: '更新时间', align: 'center', templet: '#update_time'},
				{title: '操作', toolbar: '#operation', align: 'center'}
			]]
		});
		
		//监听工具条
		table.tool(function (obj) {
			var data = obj.data;
			switch (obj.event) {
				case "choose":
					chooseGraphicMessage(data);
					layer.close(layer_index);
					break;
			}
		});
	} else if (type == 2) {
		layui.use(['form', 'laypage', 'laytpl'], function () {
			var form = layui.form;
			laytpl = layui.laytpl;
			laypage = layui.laypage;
			
			getMaterialImageList(1, limit);
			
			$('.buttom-button #material_back').unbind().click(function () {
				layer.close(layer_index);
			});
			
			picSubmit();
		});
	} else if (type == 5) {
		var table = new Table({
			elem: '#material_text_list',
			filter: "material_text",
			width: '780',
			url: nc.url("Wechat://sitehome/material/lists"),
			where: {type: 5},
			cols: [[
				{field: 'value', width: '30%', title: '内容', align: 'center', templet: '#text_content'},
				{field: 'create_time', width: '20%', title: '创建时间', align: 'center', templet: '#create_time'},
				{field: 'update_time', width: '20%', title: '更新时间', align: 'center', templet: '#update_time'},
				{title: '操作', toolbar: '#operation', align: 'center'}
			]]
		});
		
		//监听工具条
		table.tool(function (obj) {
			var data = obj.data;
			switch (obj.event) {
				case "choose":
					chooseTextMessage(data);
					layer.close(layer_index);
					break;
			}
		});
	}
}

function addMaterial(type) {
	addMaterialForm(type);
	var content_id = $('#add_material_text');
	if (type == 1) {
		content_id = $('#marterial_graphic_message');
	} else if (type == 5) {
		content_id = $('#add_material_text');
	}
	add_layer_index = layer.open({
		type: 1,
		title: false,
		//	shade: [0],
		area: ['800px', '646px'],
		content: content_id,
		success: function (layero, index) {
			var mask = $(".layui-layer-shade");
			mask.appendTo(layero.parent());
		}
	});
}

function addMaterialForm(type) {
	if (type == 5) {
		layui.use('form', function () {
			var form = layui.form;
			
			$('#material_text_content').on('input', function (e) {
				var num = e.target.value.length;
				num = 300 - parseInt(num);
				$('#add_material_text .input-text-hint').html('剩余' + num);
			});
			form.verify({
				'material_text_content': function (value, item) {
					if (value == '' || value == undefined) {
						return '文本内容不可为空';
					}
				}
			});
			
			form.on('submit(addText)', function (data) {
				var value = JSON.stringify(data.field);
				if (repeat_flag) return;
				repeat_flag = true;
				$.ajax({
					type: 'post',
					url: nc.url('Wechat://sitehome/material/add'),
					data: {type: 5, value},
					dataType: "JSON",
					success: function (res) {
						layer.msg(res.message);
						if (res.code == 0) {
							layer.close(add_layer_index);
							var _data = {
								id: res.data,
								value: data.field
							};
							textMessageAddSuccess(_data);
						} else {
							repeat_flag = false;
						}
					}
				});
			});
		});
	}
}

/**
 * 图文消息预览
 */
function preview(id, index = 0) {
	var parme = {"id": id, "i": index};
	var url = nc.url("Wechat://sitehome/material/previewgraphicmessage");
	url = url.replace('.html', '') + '/id/' + id + '/i/' + index + '.html';
	window.open(url);
}

/**
 * 文本消息预览
 */
function previewText(content) {
	layer.open({
		title: '文本内容',
		content: content
	})
}

/**
 * 获取相册数据
 */
function getMaterialImageList(page, limit) {
	$.ajax({
		type: 'post',
		url: nc.url('Wechat://sitehome/material/lists'),
		data: {
			page: page,
			limit: limit,
			type: 2,
		},
		dataType: "JSON",
		async: false,
		success: function (data) {
			//渲染相册
			var img_list = $('#image_list').html();
			laytpl(img_list).render(data.data, function (html) {
				$('.img-list').html(html);
			});
			
			var timer = setTimeout(function () {
				var _img = $('.layui-layer-content .img-list .layui-col-md2');
				_img.each(function () {
					var that = this;
					picChoose(that, _img);
				})
			}, 50);
			
			//调用分页
			laypage.render({
				elem: 'paged',
				count: data.data.count,
				layout: nc.get_page_param(),
				jump: function (obj, first) {
					if (!first) { //一定要加此判断，否则初始时会无限刷新
						getMaterialImageList(laypage, obj.curr, obj.limit);//一定要把翻页的ajax请求放到这里，不然会请求两次。
					}
				}
			});
		}
	});
}

/**
 * 图片选择
 */
function picChoose(that, _img) {
	$(that).unbind().click(function () {
		if ($(this).find('.img-check-mask').attr('data-show') == 'false') {
			$(this).find('.img-check-mask').attr('data-show', 'true');
			_img.not(that).find('.img-check-mask').attr('data-show', 'false');
		}
		var path = $(that).find('i.img').attr('data-path');
		var media_id = $(that).find('i.img').attr('data-media-id');
		var url = $(that).find('i.img').attr('data-url');
		var file_id = $(that).find('i.img').attr('data-id');
		
		$('#material_picture_path').val(path);
		$('#material_picture_media_id').val(media_id);
		$('#material_picture_url').val(url);
		$('#material_picture_id').val(file_id);
	});
}

/**
 * 确认
 * @param _type
 */
function picSubmit() {
	$('.buttom-button #material_submit').unbind().click(function () {
		var path = $('#material_picture_path').val();
		var media_id = $('#material_picture_media_id').val();
		var url = $('#material_picture_url').val();
		var file_id = $('#material_picture_id').val();
		var _data = {
			'path': path == undefined ? '' : path,
			'media_id': media_id == undefined ? '' : media_id,
			'url': url == undefined ? '' : url,
			'file_id': file_id == undefined ? '' : file_id,
		};
		if (path == '') {
			layer.msg('请选择图片');
			return false;
		}
		
		if (media_id == '' || url == '') {
			layer.msg('该图片非微信素材，请选择其他图片素材');
			return false;
		}
		try {
			materialPicCallBack(_data);
			$('.buttom-button #material_back').click();
		} catch (e) {
			console.log('未定义的materialPicCallBack()');
			console.log('错误信息: ');
			console.log(e);
		}
	});
}

/**
 * 相册选择回调
 *
 * @param _data
 * @param _name
 */
function ablumUploadSuccess(_data, _name) {
	try {
		if (_data[0] != undefined && _data[0].id != undefined) {
			for (var index = 0; index < _data.length; index++) {
				var path = _data[index].path;
				var name = _data[index].file_name;
				var name = name == '' || name == null || name == undefined ? path.substring(path.lastIndexOf('/') + 1) : name;
				$.ajax({
					type: 'post',
					url: nc.url('Wechat://sitehome/material/add'),
					data: {
						'type': 2,
						'path': path,
						'title': name
					},
					dataType: "JSON",
					success: function (res) {
						layer.msg(res.message);
						if (res.code == 0 && res.data > 0) {
							getMaterialImageList(1, limit);
						}
					}
				});
			}
		}
	} catch (e) {
		console.log(e);
	}
}