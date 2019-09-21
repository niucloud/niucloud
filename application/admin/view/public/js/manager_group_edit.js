layui.use(['form'], function () {
	var form = layui.form;
	
	//权限列表目标标记
	var tree_target = [];
	
	//背景颜色配置
	var bg_arr = ['#ededed', '#f3f3f3', '#f7f7f7', '#fbfbfb'];
	
	//编译树
	function compile_tree(data, level, html) {
		var num = 0;
		var child_num_arr = [];
		var margin_left = level * 40;
		var start_div = '<div style="margin-left:' + margin_left + 'px;background-color:' + bg_arr[level] + ';" class="tree-line">';
		html += start_div;
		for (var per in data) {
			var child_num = data[per]['child_num'];
			child_num_arr.push(child_num);
			var name = data[per]['name'];
			tree_target[level] = name;
			tree_target = tree_target.slice(0, level + 1);
			var checked = data[per]['checked'];
			var checked_html = checked ? 'checked' : '';
			var checked_class = checked ? 'layui-form-checked' : '';
			if (num > 0) {
				if (child_num == 0 && child_num_arr[num - 1] == 0) html = html.replace(/<\/div>$/, '');
				else html += start_div;
			}
			html += '<label class="menu-cell">';
			html += '<input type="checkbox"  ' + checked_html + ' data-target="' + tree_target.toString() + '" value="' + name + '"/>';
			html += '<div class="layui-unselect layui-form-checkbox ' + checked_class + '" lay-skin="primary" ><i class="layui-icon layui-icon-ok"></i></div>';
			html += data[per]['title'];
			html += '</label>';
			html += '</div>';
			if (child_num > 0) html += compile_tree(data[per]['child_list'], level + 1, '');
			num++;
		}
		return html;
	}
	
	//通过标记找到数据位置
	function find_data_by_target(list, target) {
		var data = list;
		for (var per in target) {
			if (per == 0) {
				data = data[target[per]];
			} else {
				data = data['child_list'][target[per]];
			}
		}
		return data;
	}
	
	//向上遍历
	function up_alter(list, target) {
		
		target = target.slice(0, target.length - 1);
		if (target.length == 0) return;
		
		var curr_data = find_data_by_target(tree_data, target);
		
		var count = 0;
		for (var per in curr_data['child_list']) {
			if (curr_data['child_list'][per]['checked']) {
				count++;
				break;
			}
		}
		if (count == 0) {
			curr_data['checked'] = false;
		} else {
			curr_data['checked'] = true;
		}
		
		up_alter(list, target);
	}
	
	//向下遍历
	function down_alter(list, checked) {
		for (var per in list) {
			list[per]['checked'] = checked;
			if (list[per]['child_num'] > 0) down_alter(list[per]['child_list'], checked)
		}
	}
	
	//绑定点击事件
	$("#tree_box").on('click', '.menu-cell input', function () {
		var checked = $(this).prop('checked');
		var target = $(this).attr('data-target');
		target = target.split(',');
		
		var curr_data = find_data_by_target(tree_data, target);
		curr_data['checked'] = checked;
		
		down_alter(curr_data['child_list'], checked);
		up_alter(tree_data, target);
		
		$("#tree_box").html(compile_tree(tree_data, 0, ''));
	});
	
	$("#tree_box").html(compile_tree(tree_data, 0, ''));
	
	var repeat_flag = false;//防重复标识
	form.on('submit(save)', function (data) {
		
		var obj = $("#tree_box input:checked");
		var group_array = [];
		for (var i = 0; i < obj.length; i++) {
			group_array.push(obj.eq(i).val());
		}
		
		data.field.group_array = group_array.toString();
		
		if (repeat_flag) return;
		repeat_flag = true;
		$.ajax({
			url: submit_url,
			data: data.field,
			type: "post",
			dataType: "JSON",
			success: function (res) {
				layer.msg(res.message);
				if (res.code == 0) {
					location.href = nc.url('admin/user/grouplist');
				} else {
					repeat_flag = false;
				}
			}
		});
	});
	
	form.verify({
		title: function (value) {
			if (value.length == 0) {
				return '请输入用户组名称';
			}
		},
		
	});
	
});