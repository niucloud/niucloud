var table = new Table({
	elem: '#sms_list',
	filter: "sms_list",
	url: nc.url("sms://admin/config/smslist"),
	cols: [
		[
			{
				field: 'account',
				width: '10%',
				title: '账号',
				templet: "#send_account"
			},
            {
                width: '20%',
                title: '配置信息',
                templet: "#sms_template"
            },
            {
                width: '30%',
                title: '发送结果',
                field: 'result'
            },
			{
				field: 'status',
				width: '5%',
				title: '状态',
				templet: '#status'
			},
			{
				width: '20%',
				title: '发送时间',
				templet: '#create_time'
			},
			{
				title: '操作',
				toolbar: '#operation',
                align: 'right'
			}
		]
	]
});

table.tool(function (obj) {
	var data = obj.data;
	switch (obj.event) {
		case "send_sms":
			sendMsg(data);
			break;
	}
	
});

function sendMsg(data) {
	layer.confirm('确定要重新发送?', {
		btn: ['确定', '取消']
	}, function () { 
		//请求地址是错的
		$.ajax({
			type: "post",
			async: false,
			url: nc.url("sms://admin/config/smsSend"),
			data: {'id': data.id},
			dataType: "JSON",
			success: function (res) {
				layer.msg(res['message'], {}, function () {
					if (res.code == 0) {
						table.reload();
					}
				});
			}
		});
	}, function () {
		layer.close();
	});
}