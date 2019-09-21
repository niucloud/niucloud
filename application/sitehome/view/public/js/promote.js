$(function () {
	
	layui.use(['form', 'element'], function () {
		
		var form_promote = layui.form;
		var element = layui.element;
		
		var current = $(".promote > .layui-tab-title .layui-this");
		var port = current.attr("data-port");
		var qrcode = current.attr("data-qrcode");
		$("input[name='port_type'][value='" + port + "']").prop('checked', true);
		$("input[name='share_type'][value='1']").prop('checked', true);
		form_promote.render();
		switch (port) {
			case "wap":
				$(".promote .edit-wrap .copy-tip").html("复制 H5 推广链接");
				$("#promote_url").val(nc.url(promote_data.h5_url));
				break;
			case "weapp":
				$(".promote .edit-wrap .copy-tip").html("复制 小程序 路径");
				$("#promote_url").val(promote_data.weapp_url);
				$("input[name='share_type'][value='3']").next().hide();
				break;
		}
		
		refreshQrcode(qrcode);
		
		form_promote.verify({
			required: function (value, item) {
				var msg = $(item).attr("placeholder") != undefined ? $(item).attr("placeholder") : '';
				if (value == '') return msg;
			},
		});
		
		element.on('tab(promote-port)', function (data) {
			var port = $(data.elem.context).attr("data-port");
			var qrcode = $(data.elem.context).attr("data-qrcode");
			$("input[name='port_type'][value='" + port + "']").prop('checked', true);
			$("input[name='share_type'][value='1']").prop('checked', true);
			form_promote.render();
			switch (port) {
				case "wap":
					$(".promote .edit-wrap .copy-tip").html("复制 H5 推广链接");
					$("#promote_url").val(nc.url(promote_data.h5_url));
					break;
				case "weapp":
					$(".promote .edit-wrap .copy-tip").html("复制 小程序 路径");
					$("#promote_url").val(promote_data.weapp_url);
					$("input[name='share_type'][value='3']").next().hide();
					break;
			}
			refreshQrcode(qrcode);
			
		});
		
		//路径
		form_promote.on('radio(port_type)', function (data) {
			$("input[name='share_type']:checked").prop('checked', true).next().trigger("click");
			if (data.value == "weapp") {
				if ($("input[name='share_type'][value='3']:checked").length > 0) {
					$("input[name='share_type'][value='1']").prop('checked', true).next().trigger("click");
				}
			}
			form_promote.render();
			if (data.value == "weapp") {
				$("input[name='share_type'][value='3']").next().hide();
			}
		});
		
		//分享样式
		form_promote.on('radio(share_type)', function (data) {
			$(".promote .preview-wrap").children().hide();
			if (data.value == 1) {
				$(".promote .preview-wrap .posters-style").show();
			} else if (data.value == 2 && $("input[name='port_type'][value='weapp']").is(":checked")) {
				$(".promote .preview-wrap .wechat-applet-style").show();
			} else if (data.value == 2) {
				$(".promote .preview-wrap .wechat-style").show();
			} else if (data.value == 3) {
				$(".promote .preview-wrap .wechat-friends-style").show();
			}
			form_promote.render();
		});
		
		var repeat_flag = false;//防重复标识
		form_promote.on('submit(save)', function (data) {
			if (repeat_flag) return;
			repeat_flag = true;
			data.field.title = data.title;
			$.ajax({
				type: "post",
				async: false,
				url: nc.url("sitehome/diy/editPromote"),
				data: data.field,
				dataType: "JSON",
				success: function (data) {
					layer.msg(data.message);
					if (data.code == 0) {
						layer.close(window.promoteIndex);
					} else {
						repeat_flag = false;
					}
					
				}
			});
		});
		
	});
	
	loadImgMagnify();
	
	$(".promote .edit-promote-setting").click(function () {
		
		$(".promote.layui-tab-brief .layui-tab-title").hide();
		$(".promote .edit-header").show();
		$(".promote .edit-wrap .layui-form").show().siblings().hide();
		
	});
	
	$(".promote .js-go-back").click(function () {
		$(".promote.layui-tab-brief .layui-tab-title").show();
		$(".promote .edit-header").hide();
		$(".promote .edit-wrap .layui-form").hide().siblings().show();
	});
	
	$("input[name='share_title']").keyup(function () {
		$("[data-share-title]").text($(this).val());
	});
	
	$("textarea[name='share_desc']").keyup(function () {
		$("[data-share-desc]").text($(this).val());
	});
	
});

/**
 * 单图回调事件
 */
function singleImageUploadSuccess(res, name) {
	if (name == "share_image") {
		$(".share_image").html('<div class="upload-img-box has-choose-image"><div><img src="' + nc.img(res.path) + '" layer-src="' + nc.img(res.path) + '"></div><span onclick="uploadSingleshare_image();">修改</span></div>');
		$(".promote .preview-wrap .posters-style .top-wrap img").attr("src", nc.img(res.path));
		$("input[name='share_image']").val(res.path);
	}
}

function refreshQrcode(path) {
	
	if (path == "") path = default_qrcode;
	$("[data-qrcode]").attr('src', path);
	$(".download-qrcode").attr("href", path);
}