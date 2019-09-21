var table = new Table({
	elem: '#notice_category_list',
	filter: "notice_category_list",
	url: nc.url("admin/config/noticecategorylist"),
	cols: [
		[
			{
				field: 'title',
				width: '60%',
				title: '标题',
			},
			{
				field: 'sort',
				width: '20%',
				title: '排序',
			},
			{
				title: '操作',
				width: '20%',
				toolbar: '#operation',
				align: 'right',
			}
		]
	],
});

//监听工具条
table.tool(function (obj) {
	var data = obj.data;
	switch (obj.event) {
		case "delete":
			$.ajax({
				type: "post",
				url: nc.url("admin/Config/deleteNoticeCategory"),
				data: {
					'notice_category_id': data.notice_category_id
				},
				dataType: "JSON",
				success: function (res) {
					layer.msg(res.message);
					location.reload();
				}
			});
			break;
		case 'edit':
            openModel(data);
			break;
	}
});

layui.use('form', function () {
	var layui_form = layui.form;
	layui_form.render();
	layui_form.on('select(notice_category_id)', function (data) {
		table.reload({
			page: {
				curr: 1
			},
			where: {
				notice_category_id: data.value
			}
		});
		return false;
	});
	
	layui_form.on('select(select_create_time)', function (data) {
		table.reload({
			page: {
				curr: 1
			},
			where: {
				select_create_time: data.value
			}
		});
		return false;
	});
	
	$(".layui-icon-search").click(function () {
		table.reload({
			page: {
				curr: 1
			},
			where: {
				title: $("#title").val()
			}
		});
		return false;
	});
	
});

var laytpl, index, form;
layui.use(['laytpl', 'form'], function () {
	laytpl = layui.laytpl;
	form = layui.form;
	// 修改公告分类标题
	form.on('submit(noticeCategorySubmit)', function (data) {
		var field = data.field;
		var notice_category_id = field.notice_category_id;
		if(notice_category_id > 0){
            var url = nc.url("admin/Config/editCategoryTitle")
		}else{
            var url = nc.url("admin/Config/addNoticeCategory")
		}
		$.ajax({
			type: "post",
			async: false,
			url: url,
			dataType: 'json',
			data: field,
			success: function (res) {
				back();
				layer.msg(res.message);
				if (res.code == 0) {
					location.reload();
				}
			}
		})
	})
});


function openModel(data){
	var title = "编辑公告分类";
	if(data == undefined){
        data = [];
        title = "添加公告分类"; 
	}
    laytpl($("#notice_category_html" ).html()).render(data, function (html) {
        index = layer.open({
            type: 1,
            title: title,
            skin: 'layer-tips-class',
            area: ["450px"],
            content: html,
        });
        form.render();
    });
}

