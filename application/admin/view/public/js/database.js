var form, index, util;
var table = new Table({
	elem: '#table_list',
	filter: "table",
	url: nc.url("admin/system/database"),
	cols: [
		[
			{
				type: 'checkbox'
			},
			{
				field: 'Name',
				width: '20%',
				title: '表名称',
			},
			{
				field: 'Engine',
				width: '10%',
				title: '表类型'
			},
			{
				field: 'Comment',
				width: '25%',
				title: '描述'
			},
			{
				field: 'Create_time',
				width: '15%',
				title: '创建时间',
				align: 'center'
			},
			{
				field: 'Data_length',
				width: '15%',
				title: '数据量',
				align: 'center',
				templet: function (data) {
					return renderSize(data.Data_length);
				}
			},
			{
				title: '操作',
				toolbar: '#operation',
				align: 'right'
			}
		]
	],
	page: false
});

table.tool(function (obj) {
	var data = obj.data;
	switch (obj.event) {
		case "repair":
			repairTable(data.Name);
			break;
		case "backup":
			backupTable(data.Name, -1, 0);
			break;
	}
});

table.bottomToolbar(function (obj) {
	var checkedObj = obj.data;
	if (checkedObj.length > 0) {
		var nameArr = [];
		checkedObj.forEach(function (el, key) {
			nameArr.push(el.Name);
		});
		backupTable(nameArr.toString(), -1, 0);
	} else {
		layer.msg("请选中需要操作的数据");
	}
	
});

layui.use(['form', 'util'], function () {
	form = layui.form;
    util = layui.util;
    //按钮事件
    util.event('lay-util', {
        repair: function(othis){
            var checkedObj = table.checkStatus();
            if (checkedObj.data.length > 0) {
                var nameArr = [];
                checkedObj.data.forEach(function (el, key) {
                    nameArr.push(el.Name);
                });
                repairTable(nameArr.toString());
            }else{
                layer.msg('必须至少选中一项!', {time: 5000, icon:2});
                return;
			}
        }
        ,backup: function(){
            var checkedObj = table.checkStatus();
            if (checkedObj.data.length > 0) {
                var nameArr = [];
                checkedObj.data.forEach(function (el, key) {
                    nameArr.push(el.Name);
                });
                backupTableInit(nameArr.toString());
            }else{
                layer.msg('必须至少选中一项!', {time: 5000, icon:2});
                return;
			}
        }
    });

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

// 表修复
var repeat_flag_repair_table = false;//防重复标识
function repairTable(table) {
	if(repeat_flag_repair_table) return;
	repeat_flag_repair_table = true;
	$.ajax({
		url: nc.url("admin/system/repairTable"),
		type: 'POST',
		data: {table: table},
		beforeSend: function () {
            layer.msg("<div><i class='layui-icon layui-icon-loading layui-icon layui-anim layui-anim-rotate layui-anim-loop'></i><span class='backup-msg'>正在修复...</span></div>", {time : 0});
		},
		complete: function () {
			layer.close(index);
		},
		success: function (res) {
			layer.msg(res.message);
			repeat_flag_repair_table =false;
		}
	})
}

/**
 * 初始化备份表操作
 */
function backupTableInit(tables){
    $.ajax({
        url: nc.url("admin/system/backupTable"),
        type: 'post',
        data: {
            table: tables,
        },
        success: function (res) {
			if(res.code == 0){
				//初始化完毕
                layer.msg("<div><i class='layui-icon layui-icon-loading layui-icon layui-anim layui-anim-rotate layui-anim-loop'></i><span class='backup-msg'>"+res.data.message+"</span></div>", {time : 0});
                $(".backup-msg").text("数据库正在备份,请不要关闭窗口!");
                backupTable(res.data.tab);//调用数据库备份

			}else{
                layer.msg(res.message);//提示信息
			}
        }
    })
}
// 表备份
function backupTable(tab) {
	//备份数据
	$.ajax({
		url: nc.url("admin/system/backupTable"),
		type: 'post',
		data: { "id" : tab.id, "start" : tab.start },
		success: function (res) {
			//备份成功
			if(res.code == 0){
				if(res.data.status == 2){
					layer.msg(res.data.message);
                    table.reload();
				}else{
                    $(".backup-msg").text(res.data.message);
                    backupTable(res.data.tab);
				}
			}else{
                layer.msg(res.message);
			}
		}
	})
}