var laytpl, index, form, laydate;
var table = new Table({
	elem: '#member_account',
	url: nc.url("sitehome/member/accountDetail"),
	where: {
		member_id: tpl_data.member_id
	},
	cols: [[
		{field: 'nick_name', width: '20%', title: '会员昵称', align: 'left', unresize: 'true'},
		{field: 'account_name', width: '15%', title: '账户类型', unresize: 'true'},
		{field: 'account_data', width: '20%', title: '数量', unresize: 'true', templet: "#account_data"},
		{field: 'remark', width: '25%', title: '产生方式', unresize: 'true'},
		{field: 'create_time', title: '产生时间', templet: '#create_time', align: 'right', unresize: 'true'}
	]]
});

layui.use(['laytpl', 'form', 'laydate', 'element'], function () {
	laytpl = layui.laytpl;
	form = layui.form;
	laydate = layui.laydate;
	var element = layui.element;
	
	//获取hash来切换选项卡
	var layid = location.hash.replace(/^#tab=/, '');
	
	if (layid == "") layid = tab;
	
	element.tabChange('member_detail_tab', layid);
	
	//监听Tab切换，以改变地址hash值
	element.on('tab(member_detail_tab)', function () {
		location.hash = 'tab=' + this.getAttribute('lay-id');
	});
	
	form.on('select(province)', function (obj) {
		$("input[name='address']").val('');
		getAreaList(obj.value, 2);//重新渲染地址
		form.render();
	});
	form.on('select(city)', function (obj) {
		$("input[name='address']").val('');
		getAreaList(obj.value, 3);//重新渲染地址
		form.render();
	});
	form.on('submit(searchForm)', function (data) {
		var strs = $("#daterange").val().split(" - ");
		var field = data.field;
		field.start_time = strs[0];
		field.end_time = strs[1];
		table.reload({
			where: field,
			page: {
				curr: 1
			}
		});
		return false;
	});
	
	form.on('submit(setAccount)', function (data) {
		setAccount();
	});
	
	form.verify({
		username: function (val, obj) {
			if (value != '' && !/^\w+$/.test(value)) {
				return "用户名只允许数字，字母与下划线";
			}
			if (value != '' && !/^[a-z0-9_-]{3,16}$/.test(value)) {
				return "请输入3到16位的用户名";
			}
		},
		mobile: function (value, item) {
			if (value != '' && !/^(((13[0-9]{1})|(14[7]{1})|(15[0-9]{1})|(18[0-9]{1})|(17[0-9]{1}))+\d{8})$/.test(value)) {
				return "请输入正确的手机号";
			}
		},
		email: function (value, item) {
			if (value != '' && !/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value)) {
				return '邮箱格式不正确';
			}
		},
		account_number: function (value, item) {
			if (value != "") {
				if (!value || isNaN(value)) {
					return "只能填写数字";
				}
				var num = parseFloat($(item).attr('data-num'));
				if ((parseFloat(num) + parseFloat(value)) < 0) {
					return "减少数量不可超过当前会员所拥有数量";
				}
			}
		}
	});
	
	// 编辑会员信息
	form.on('submit(editMemberInfoSubmit)', function (data) {
		if (data.field.city && data.field.province && data.field.district) {
			var province_name = $("option[value='" + data.field.province + "']").text();
			var city_name = $("option[value='" + data.field.city + "']").text();
			var district_name = $("option[value='" + data.field.district + "']").text();
			data.field.full_address = province_name + city_name + district_name + data.field.address;
		}
		data.field.member_label = data.field.member_label != null ? data.field.member_label.toString() : '';
		$.ajax({
			type: "post",
			async: false,
			url: nc.url("sitehome/member/editMemberInfo"),
			dataType: 'json',
			data: data.field,
			success: function (res) {
				back();
				layer.msg(res.message);
				if (res.code == 0) {
					location.reload();
				}
			}
		})
	});
	
	// 保存会员组
	form.on('submit(memberGroupSubmit)', function (data) {
		$.ajax({
			type: "post",
			async: false,
			url: nc.url("sitehome/member/modifyMemberGroup"),
			dataType: 'json',
			data: data.field,
			success: function (res) {
				back();
				layer.msg(res.message);
				if (res.code == 0) {
					location.reload();
				}
			}
		})
	});
	
	form.on('submit(formAccount)', function (data) {
		var field = data.field;
		var count = 0;
		$("input[lay-verify='account_number']").each(function () {
			if (parseFloat($(this).val()) != 0) {
				count++;
			}
		});
		if (count == 0) {
			layer.msg('请输入调整数值');
			return false;
		}
		
		$.ajax({
			type: "post",
			url: nc.url("sitehome/member/setAccount"),
			data: field,
			dataType: 'json',
			success: function (data) {
				layer.msg(data.message);
				if (data.code == 0) {
					table.reload();
					layer.closeAll('page');
					location.reload();
				}
			}
		});
		return false;
	});
	
});

function editLayer(type, title, width, height) {
	laytpl($("#" + type).html()).render(tpl_data, function (html) {
		index = layer.open({
			type: 1,
			title: title,
			skin: 'layer-tips-class',
			area: [width, height],
			content: html,
		});
		form.render();
		
		try {
			var funcName = type + 'LayerAfter';
			if (typeof(eval(funcName)) == "function") {
				eval(funcName + '(tpl_data)');
			}
		} catch (e) {
		}
	});
}

function baseInfoLayerAfter(data) {
	if (data.birthdayyear && data.birthdaymonth && data.birthdaymonth) {
		laydate.render({
			elem: '#birthday',
			type: 'date',
			value: data.birthdayyear + '-' + data.birthdaymonth + '-' + data.birthday
		});
	}
	if (!data.province) {
		getAreaList(0, 1);
	} else {
		getAreaList(0, 1, 1);
		getAreaList(data.province, 2, 1);
		getAreaList(data.city, 3, 1);
	}
}

function getAreaList(pid, level, is_first_load = 0) {
	if (level <= 5) {
		$.ajax({
			type: "get",
			url: nc.url("sitehome/manager/getAreaList"),
			data: {
				'level': level,
				'pid': pid
			},
			dataType: "JSON",
			success: function (res) {
				
				if (res.code == 0) {
					var obj = {1: 'province', 2: 'city', 3: 'district', 4: 'subdistrict'};
					if (is_first_load == 0) removeSelectedData(level);
					$.each(res.data, function (name, value) {
						$("select[name=" + obj[level] + "]").append("<option value='" + value.id + "'>" + value.name + "</option>");
					});
					if (is_first_load) $("select[name=" + obj[level] + "]").val(tpl_data[obj[level]]);
					
					form.render();
				} else {
					layer.msg(res.message);
				}
				
			}
		});
	}
}

/**
 * 清除之前的地区数据
 */
function removeSelectedData(level) {
	level = Number(level);
	if (level <= 1) $("select[name=province] option:gt(0)").remove();
	if (level <= 2) $("select[name=city] option:gt(0)").remove();
	if (level <= 3) $("select[name=district] option:gt(0)").remove();
	if (level <= 4) $("select[name=subdistrict] option:gt(0)").remove();
}


function datePick(date_num, event_obj) {
	$(".date-picker-btn").removeClass("selected");
	$(event_obj).addClass('selected');
	Date.prototype.Format = function (fmt, date_num) { //author: meizz
		this.setDate(this.getDate() - date_num);
		var o = {
			"M+": this.getMonth() + 1, //月份
			"d+": this.getDate(), //日
			"H+": this.getHours(), //小时
			"m+": this.getMinutes(), //分
			"s+": this.getSeconds(), //秒
			"q+": Math.floor((this.getMonth() + 3) / 3), //季度
			"S": this.getMilliseconds() //毫秒
		};
		if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
		for (var k in o)
			if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
		return fmt;
	};
	var now_time = new Date().Format("yyyy-MM-dd HH:mm:ss", 0);//当前日期
	var before_time = new Date().Format("yyyy-MM-dd HH:mm:ss", date_num - 1);//前几天日期

	var daterange=before_time+" - "+now_time;
	$("#daterange").val(daterange);
}

/**
 * 账户调整
 */
function setAccount() {
	$.ajax({
		type: "post",
		url: nc.url("sitehome/member/memberInfo"),
		async: false,
		data: {member_id: tpl_data.member_id},
		dataType: 'json',
		success: function (data) {
			if (data.code != 0) {
				layer.msg(data.message);
			}
			data = data.data;
			var tpl_html = $("#account_html").html();
			laytpl(tpl_html).render(data, function (html) {
				index = layer.open({
					title: "账户调整",
					closeBtn: 1,
					skin: 'layer-tips-class',
					content: html,
					area: ['550px'],
					type: 1
				});
				
			});
		}
	});
}