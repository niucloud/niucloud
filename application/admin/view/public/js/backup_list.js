var table = new Table({
	elem: '#list',
	filter: "table",
	url: nc.url("admin/system/backupList"),
	cols: [
		[
			{
				field: 'name',
				width: '25%',
				title: '文件名称',
			},
			{
				field: 'ext',
				width: '15%',
				title: '文件类型',
				align: 'center'
			},
			{
				field: 'create_time',
				width: '15%',
				title: '创建时间',
				align: 'center',
				templet: function (data) {
					return nc.time_to_date(data.create_time)
				}
			},
			{
				field: 'size',
				width: '15%',
				title: '文件大小',
				align: 'center',
				templet: function (data) {
					return renderSize(data.size);
				}
			},
			{
				title: '操作',
				toolbar: '#operation',
				align: 'right'
			}
		]
	]
});

//监听工具条
table.tool(function (obj) {
	var data = obj.data;
	switch (obj.event) {
		case "delete":
			delBackup(data.name);
			break;
		case "recovery":
			recoveryBackup(data.name, 0, 0);
			break;
	}
});

function renderSize(value) {
	if (null == value || value == '') {
		return "0 Bytes";
	}
	var unitArr = new Array("Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
	var index = 0,
		srcsize = parseFloat(value);
	index = Math.floor(Math.log(srcsize) / Math.log(1024));
	var size = srcsize / Math.pow(1024, index);
	//  保留的小数位数
	size = size.toFixed(2);
	return size + unitArr[index];
}

function delBackup(name) {
	$.ajax({
		url: nc.url("admin/system/delBackup"),
		type: 'post',
		data: {name: name},
		success: function (res) {
			layer.msg(res.message);
			table.reload();
		}
	})
}

var repeat_flag = false;//防重复标识
function recoveryBackup(name, part, start) {
    if($(".backup-msg").length == 0){
        layer.msg("<div><i class='layui-icon layui-icon-loading layui-icon layui-anim layui-anim-rotate layui-anim-loop'></i><span class='backup-msg'>正在还原...</span></div>", {time : 0});
    }
    
    if(repeat_flag) return;
	repeat_flag = true;
	$.ajax({
		url: nc.url("admin/system/recoveryBackup"),
		type: 'post',
		data: {
			name: name,
			part: part,
			start: start,
		},
		success: function (res) {
			if (res.code == 0) {
				if (res.data.part) {
					recoveryBackup(name, res.data.part, res.data.start)
				} else {
					layer.msg(res.data.message);
				}
			} else {
				// $('.backup-progress').addClass('hide');
				layer.msg(res.data.message);
			}
			repeat_flag = false;
		}
	})
}