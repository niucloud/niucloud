var laytpl, index, form;
layui.use(['laytpl', 'form'], function () {
	laytpl = layui.laytpl;
	form = layui.form;
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
function upgrade(count){
	if(count > 0)
		{
			download(0, count);
		}
	
}

function download(index, count){
	$.ajax({
		type: "post",
		url: nc.url("admin/system/download"),
		data: {
			'index': index,
		},
		dataType: "JSON",
		success: function (res) {
			layer.msg(res.data);
			$(".layui-progress-bar").attr("lay-percent", index/count + '%');
			if(index < count-1)
			{
				download(index+1, count);
			}
			
		}
	});
}

function execute()
{
	$.ajax({
		type: "post",
		url: nc.url("admin/system/download"),
		data: {
			'index': index,
		},
		dataType: "JSON",
		success: function (res) {
			layer.msg(res.data);
			$(".layui-progress-bar").attr("lay-percent", index/count + '%');
			if(index < count-1)
			{
				download(index+1, count);
			}
			
		}
	});
	
}
