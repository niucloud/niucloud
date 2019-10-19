var adv_id = $("#adv_id").val() == undefined ? 0 : $("#adv_id").val();
var adv_data = {}; //初始化数据
(function () {
	adv_data.init_map = [];
	var elem = $(".selected-template-list ul li");
	for (i = 0; i < elem.length; i++) {
		adv_data.init_map[i] = null;
	}
	adv_data.init_pics = [];
})();
layui.use(['form'], function () {
	var form = layui.form;
	
	var repeat_flag = false;//防重复标识
	form.on('submit(save)', function (data) {
		var field = data.field;
		if (!keywordsIsExist(field.keywords)) {
			layer.msg('广告关键字已存在请重新输入');
			is_first = true;
			$("input[nmae='keywords']").val('');
			return;
		}
		if (repeat_flag) return;
		repeat_flag = true;
		
		var adv_arr = [];
		switch (parseInt(field.adv_display)) {
			case 1:
				var mapnum = parseInt(field.mapnum);
				field.adv_layout = adv_data.layout.value;
				for (i = 1; i <= mapnum; i++) {
					var adv_json = {};
					adv_json.img_sort = i;
					adv_json.img_path = field['map' + i + '_path'];
					adv_json.img_url = field['map' + i + '_url'];
					adv_json.img_name = field['map' + i + '_name'];
					adv_arr.push(adv_json);
				}
				break;
			case 2 :
				field.adv_layout = 0;
				var elem = $(".pics-boxs");
				$.each(elem, function (k, v) {
					var key = $(this).attr('data-key');
					var adv_json = {};
					adv_json.img_sort = k;
					adv_json.img_path = field['pics_path' + key];
					adv_json.img_url = field['pics_url' + key];
					adv_json.img_name = field['pics_name' + key];
					adv_json.img_color = field['pics_color' + key];
					adv_arr.push(adv_json);
				});
				break;
		}
		field.adv_json = JSON.stringify(adv_arr);
		field.adv_type = 1;
		field.adv_id = adv_id;
		$.ajax({
			type: "post",
			url: nc.url("DiyView://sitehome/Advertisement/edit"),
			data: field,
			dataType: "JSON",
			success: function (res) {
				layer.msg(res.message);
				if (res.code == 0) {
					window.location.href = nc.url("DiyView://sitehome/Advertisement/index");
				} else {
					repeat_flag = false;
				}
			}
		});
		return false;
	});
	
	keywordsIsExist = function (keywords) {
		var keywords_flag = false;
		if (adv_id > 0 && init_data.keywords == keywords) {
			return true;
		}
		$.ajax({
			type: "post",
			url: nc.url("DiyView://sitehome/Advertisement/keywordsIsExist"),
			data: {
				'keywords': keywords
			},
			dataType: "JSON",
			async: false,
			success: function (res) {
				if (res.data == null) {
					keywords_flag = true;
				} else {
					keywords_flag = false;
				}
			}
		});
		return keywords_flag;
	};
	
	//类型选择操作
	form.on('radio(type)', function (data) {
		var val = parseInt(data.value);
		switch (val) {
			case 1:
				$(".height_box").show();
				$(".width_box").show();
				break;
			case 2:
				$(".height_box").hide();
				$(".width_box").hide();
				$("input[name='adv_height']").val('');
				$("input[name='adv_width']").val('');
				break;
		}
	});
	
	//图片类型操作
	var display_type = $("input[name='adv_display']:checked").val();
	form.on('radio(display)', function (data) {
		var val = parseInt(data.value);
		//把轮播图数据保存起来
		if (val == 1) {
			var elem = $(".pics-boxs");
			var adv_arr = [];
			if (elem.length > 0) {
				$.each(elem, function (k, v) {
					var key = $(this).attr('data-key');
					var adv_json = {};
					adv_json.img_sort = k;
					adv_json.img_path = $('input[name="pics_path' + key + '"]').val();
					adv_json.img_url = $('input[name="pics_url' + key + '"]').val();
					adv_json.img_name = $('input[name="pics_name' + key + '"]').val();
					adv_json.img_color = $('input[name="pics_color' + key + '"]').val();
					adv_arr.push(
						adv_json
					);
				});
				adv_data.init_pics = adv_arr;
			}
		}
		
		if (val == 2) {
			var before_value = $(".selected").attr("value");
			var before_mapnum = $(".selected").attr("data-num");
			var before_arr = [];
			for (i = 0; i < before_mapnum; i++) {
				var adv_json = {};
				var key = i + 1;
				adv_json.img_sort = key;
				
				adv_json.img_path = $('input[name="map' + key + '_path"]').val() == undefined ? '' : $('input[name="map' + key + '_path"]').val();
				adv_json.img_url = $('input[name="map' + key + '_url"]').val() == undefined ? '' : $('input[name="map' + key + '_url"]').val();
				adv_json.img_name = $('input[name="map' + key + '_name"]').val() == undefined ? '' : $('input[name="map' + key + '_name"]').val();
				before_arr.push(adv_json);
			}
			adv_data.init_map[before_value] = before_arr;
			
		}
		switch (val) {
			case 1:
				$(".display1").show();
				$("#pics_box").hide();
				$("#pics_box").html('');
				if (adv_data.init_map[adv_data.layout.value] != null && adv_data.init_map[adv_data.layout.value].length > 0) {
					editMapAction(adv_data.layout.num, adv_data.init_map[adv_data.layout.value]);
				} else {
					mapAction(adv_data.layout.num);
				}
				
				break;
			case 2:
				$(".display1").hide();
				$("#pics_box").show();
				$("#map_box").html('');
				picsAction(2);
				break;
		}
		display_type = val;
	});
	
	
	//多图html
	mapAction = function (num) {
		var adv_layout = adv_data.layout.value;
		if (adv_id > 0 && init_data.adv_display == 1 && init_data.adv_layout == adv_layout) {
			editMapAction(adv_data.layout.num, init_data.adv_json);
			return;
		}
		var html = '';
		html += '<input type="hidden" name = "mapnum" value="' + num + '"/>';
		for (var i = 1; i <= num; i++) {
			switch (i) {
				case 1:
					num_name = '一';
					break;
				case 2:
					num_name = '二';
					break;
				case 3:
					num_name = '三';
					break;
				case 4:
					num_name = '四';
					break;
				case 5:
					num_name = '五';
					break;
			}
			html += '<div class="layui-form-item display1">';
			html += '<label class="layui-form-label"><span class="required">*</span>图片' + num_name + '</label>';
			html += '<div class="layui-input-inline">';
			html += '<input type="hidden" lay-verify="required" class="layui-input "  name = "map' + i + '_path" id="display0_img" autocomplete="off" />';
			html += '<input type="button" value="上传图片" class="layui-btn img-input" onclick="uploadSinglemap' + i + '()" />';
			html += '<div class="image-block img-block layui-col-md3"><img class="map' + i + '_image_url"/></div>';
			html += '<div class="img-info layui-col-md9">';
			html += '<div class="layui-form-item">';
			html += '<label class="layui-form-label"><span class="required">*</span>广告名称</label>';
			html += '<div class="layui-input-block">';
			html += '<input type="text" name="map' + i + '_name" value="" lay-verify="required" autocomplete="off" placeholder="请输入广告名称"  class="layui-input">';
			html += '</div>';
			html += '</div>';
			html += '<div class="layui-form-item">';
			html += '<label class="layui-form-label"><span class="required">*</span>广告链接</label>';
			html += '<div class="layui-input-block">';
			html += '<input type="text" name="map' + i + '_url" lay-verify="required|url" placeholder="请输入广告链接" value="" autocomplete="off" class="layui-input">';
			html += '</div>';
			html += '</div>';
			html += '</div>';
			html += '</div>';
			html += '</div>';
		}
		$("#map_box").html(html);
		
	};
	
	//编辑多图html
	editMapAction = function (num, data) {
		var html = '';
		html += '<input type="hidden" name = "mapnum" value="' + num + '"/>';
		$.each(data, function (k, v) {
			var i = k + 1;
			var num_name = '';
			switch (i) {
				case 1:
					num_name = '一';
					break;
				case 2:
					num_name = '二';
					break;
				case 3:
					num_name = '三';
					break;
				case 4:
					num_name = '四';
					break;
				case 5:
					num_name = '五';
					break;
			}
			
			html += '<div class="layui-form-item display1">';
			html += '<label class="layui-form-label"><span class="required">*</span>图片' + num_name + '</label>';
			html += '<div class="layui-input-inline">';
			html += '<input type="hidden" lay-verify="required" class="layui-input " value="' + v.img_path + '"  name = "map' + i + '_path" id="display0_img" autocomplete="off"   />';
			html += '<input type="button" value="上传图片" class="layui-btn img-input" onclick="uploadSinglemap' + i + '()" />';
			if (v.img_path.length > 0) {
				html += '<div class="image-block img-block layui-col-md3"><img class="map' + i + '_image_url" src="' + nc.img(v.img_path) + '" layer-src="' + nc.img(v.img_path) + '"/></div>';
			} else {
				html += '<div class="image-block img-block layui-col-md3"><img class="map' + i + '_image_url" /></div>';
			}
			
			html += '<div class="img-info layui-col-md9">';
			html += '<div class="layui-form-item">';
			html += '<label class="layui-form-label"><span class="required">*</span>广告名称</label>';
			html += '<div class="layui-input-block">';
			html += '<input type="text" name="map' + i + '_name" value="' + v.img_name + '" lay-verify="required" autocomplete="off" placeholder="请输入广告名称"  class="layui-input">';
			html += '</div>';
			html += '</div>';
			html += '<div class="layui-form-item">';
			html += '<label class="layui-form-label"><span class="required">*</span>广告链接</label>';
			html += '<div class="layui-input-block">';
			html += '<input type="text" name="map' + i + '_url" value="' + v.img_url + '"   lay-verify="required|url" placeholder="请输入广告链接" value="" autocomplete="off" class="layui-input">';
			html += '</div>';
			html += '</div>';
			html += '</div>';
			html += '</div>';
			html += '</div>';
		});
		$("#map_box").html(html);
	};
	
	//设置布局数据
	setLayoutData = function () {
		var elem = $(".selected-template-list ul li.selected");
		adv_data.layout = {
			"id": elem.attr('id'),
			"value": elem.attr('value'),
			"num": elem.attr('data-num'),
			'elem': elem
		};
		if (adv_data.init_map[elem.attr('value')] != null) {
			editMapAction(elem.attr('data-num'), adv_data.init_map[elem.attr('value')]);
		} else {
			mapAction(elem.attr('data-num'));
		}
		
	};
	setLayoutData();
	
	//布局模板选择
	$(".selected-template-list ul li").click(function () {
		var before_value = $(this).siblings(".selected").attr("value");
		var before_mapnum = $(this).siblings(".selected").attr("data-num");
		var adv_arr = [];
		for (i = 0; i < before_mapnum; i++) {
			var adv_json = {};
			var key = i + 1;
			adv_json.img_sort = key;
			adv_json.img_path = $('input[name="map' + key + '_path"]').val() == undefined ? '' : $('input[name="map' + key + '_path"]').val();
			adv_json.img_url = $('input[name="map' + key + '_url"]').val() == undefined ? '' : $('input[name="map' + key + '_url"]').val();
			adv_json.img_name = $('input[name="map' + key + '_name"]').val() == undefined ? '' : $('input[name="map' + key + '_name"]').val();
			adv_arr.push(adv_json);
		}
		adv_data.init_map[before_value] = adv_arr;
		$(this).siblings().removeClass('selected');
		$(this).addClass('selected');
		setLayoutData();
	});

	//编辑初始化数据
	if (adv_id > 0) {
		(function () {
			switch (init_data.adv_type) {
				case 1:
					$(".height_box").show();
					$(".width_box").show();
					break;
				case 2:
					$(".height_box").hide();
					$(".width_box").hide();
					break;
			}
			switch (init_data.adv_display) {
				case 0:
					$(".display0").show();
					$(".display1").hide();
					$("#map_box").html('');
					$("#pics_box").html('');
					editDisplay0Action();
					break;
				case 1:
					$(".display1").show();
					$(".display0").hide();
					$(".display0").html('');
					$("#pics_box").html('');
					break;
				case 2:
					$(".display1").hide();
					$(".display0").hide();
					$(".display0").html('');
					$("#map_box").html('');
					editPicsAction(init_data.adv_json);
					break;
			}
		})();
	}
});

//轮播html
picsAction = function () {
	if (adv_id > 0 && init_data.adv_display == 2 || adv_data.init_pics.length > 0) {
		editPicsAction(adv_data.init_pics);
		return;
	}
	var html = '';
	html += '<div class="layui-form-item">';
	html += '<label class="layui-form-label">轮播图</label>';
	html += '<div class="layui-input-inline">';
	html += '<button class="layui-btn" type="button" onclick="uploadAlbumpics(this)">上传</button>';
	html += '<br>';
	html += '<div class="pics-box">';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	$("#pics_box").html(html);
};

//编辑轮播html
editPicsAction = function (data) {
	var html = '';
	html += '<div class="layui-form-item">';
	html += '<label class="layui-form-label">轮播图</label>';
	html += '<div class="layui-input-inline">';
	html += '<button class="layui-btn" type="button" onclick="uploadAlbumpics(this)">上传</button>';
	html += '<br>';
	html += '<div class="pics-box">';
	$.each(data, function (k, v) {
		html += '<div class="pics-boxs pics_item' + k + '" data-key="' + k + '" style="position: relative;display:block;border-bottom: 1px solid #eee;padding: 8px 0;">';
		html += '<div class="upload-close-modal" onclick="delImg(this)" >×</div>';
		html += '<img src="' + nc.img(v.img_path) + '" data_img="' + v.img_path + '" layer-src="' + nc.img(v.img_path) + '" class="pics_item">';
		html += '<div class="img-info layui-col-md9">';
		html += '<div class="layui-form-item">';
		html += '<input type="hidden" name="pics_path' + k + '" value="' + v.img_path + '" >';
		html += '<label class="layui-form-label"><span class="required">*</span>广告名称</label>';
		html += '<div class="layui-input-block">';
		html += '<input type="text" name="pics_name' + k + '" lay-verify="required" value="' + v.img_name + '" autocomplete="off" placeholder="请输入广告名称"  class="layui-input ">';
		html += '</div>';
		html += '</div>';
		html += '<div class="layui-form-item">';
		html += '<label class="layui-form-label"><span class="required">*</span>广告链接</label>';
		html += '<div class="layui-input-block">';
		html += '<input type="text" name="pics_url' + k + '" lay-verify="required|url" value="' + v.img_url + '" autocomplete="off" placeholder="请输入广告链接"  class="layui-input ">';
		html += '</div>';
		html += '</div>';
		
		html += '<div class="layui-form-item">';
		html += '<label class="layui-form-label">背景色</label>';
		html += '<div class="layui-input-block">';
		html += '<input type="color" name="pics_color' + k + '"  value="' + v.img_color + '" class="layui-input sm ">';
		html += '</div>';
		html += '</div>';
		
		html += '</div>';
		html += '</div>';
	});
	html += '</div>';
	html += '</div>';
	html += '</div>';
	$("#pics_box").html(html);
};

//轮播上传回调
function ablumUploadSuccess(res, name) {
	for (var i = 0; i < res.length; i++) {
		var html = '';
		html += '<div class="pics-boxs pics_item' + i + '" data-key="' + i + '" style="position: relative;display:block;border-bottom: 1px solid #eee;padding: 8px 0;">';
		html += '<div class="upload-close-modal" onclick="delImg(this)" >×</div>';
		html += '<img src="' + nc.img(res[i]['path']) + '" data_img="' + res[i]['path'] + '" layer-src="' + nc.img(res[i]['path']) + '" class="pics_item">';
		html += '<div class="img-info layui-col-md9">';
		html += '<div class="layui-form-item">';
		html += '<input type="hidden" name="pics_path' + i + '" value="' + res[i]['path'] + '" >';
		html += '<label class="layui-form-label"><span class="required">*</span>广告名称</label>';
		html += '<div class="layui-input-block">';
		html += '<input type="text" name="pics_name' + i + '" lay-verify="required" value="" autocomplete="off" placeholder="请输入广告名称"  class="layui-input ">';
		html += '</div>';
		html += '</div>';
		html += '<div class="layui-form-item">';
		html += '<label class="layui-form-label"><span class="required">*</span>广告链接</label>';
		html += '<div class="layui-input-block">';
		html += '<input type="text" name="pics_url' + i + '" lay-verify="required|url" value="" autocomplete="off" placeholder="请输入广告链接"  class="layui-input ">';
		html += '</div>';
		html += '</div>';
		
		html += '<div class="layui-form-item">';
		html += '<label class="layui-form-label">背景色</label>';
		html += '<div class="layui-input-block">';
		html += '<input type="color" name="pics_color' + i + '"  value="#ffffff" class="layui-input sm">';
		html += '</div>';
		html += '</div>';
		
		html += '</div>';
		$('.pics-box').append(html);
	}
	$(".pics_box").hover(function () {
		$(this).find('.upload-close-modal').show();
	}, function () {
		$(this).find('.upload-close-modal').hide();
	});
}

//单图回调函数
function singleImageUploadSuccess(res, name) {
	if (name != 'advdisplay0') {
		$('.' + name + '_image_url').attr('src', nc.img(res.path)).attr('layer-src', nc.img(res.path));
		$('input[name="' + name + '_path"]').val(res.path);
	} else {
		$('.image_url').attr('src', nc.img(res.path)).attr('layer-src', nc.img(res.path));
		$('input[name="img_path0"]').val(res.path);
	}
	//清空之前先保留好上传的资料 下次回来存在
	
}

//删除轮播图元素
function delImg(obj) {
	$(obj).parents('.pics-boxs').remove();
}

//轮播删除移入移出
$(".pics-boxs").hover(function () {
	$(this).find('.upload-close-modal').show();
}, function () {
	$(this).find('.upload-close-modal').hide();
});