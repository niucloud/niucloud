$(function () {
	
	layui.use(['form'], function () {
		var form = layui.form;
		var repeat_flag = false;//防重复标识
		
		form.verify({
			title: function (value) {
				if (value.length < 1) {
					return '标题不能为空';
				}
				
			}
		});
		
		form.on('submit(save)', function (data) {
			var content = editor.getContent();
			if (!/[\S]+/.test(content)) {
				layer.msg("请输入协议内容");
				return;
			}
			if (content.length > 65535) {
				layer.msg("协议内容太长");
				return;
			}
			if (repeat_flag) return;
			repeat_flag = true;
			
			var field = {
				title: data.field.title,
				content: content
			};
			
			$.ajax({
				type: "post",
				url: nc.url('sitehome/member/agreement'),
				data: {data: JSON.stringify(field)},
				dataType: "JSON",
				success: function (data) {
					layer.msg(data.message);
					if (data.code == 0) {
						location.reload();
					} else {
						repeat_flag = false;
					}
				}
			});
			
			return false;
		});
		
	});
});