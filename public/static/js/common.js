var nc = window.nc_url;
/* 基础对象检测 */
nc || $.error("js-nc_url基础配置没有正确加载！");
/**
 * 解析URL
 * @param  {string} url 被解析的URL
 * @return {object}     解析后的数据
 */
nc.parse_url = function (url) {
	var parse = url.match(/^(?:([a-z]+):\/\/)?([\w-]+(?:\.[\w-]+)+)?(?::(\d+))?([\w-\/]+)?(?:\?((?:\w+=[^#&=\/]*)?(?:&\w+=[^#&=\/]*)*))?(?:#([\w-]+))?$/i);
	parse || $.error("url格式不正确！");
	return {
		"scheme": parse[1],
		"host": parse[2],
		"port": parse[3],
		"path": parse[4],
		"query": parse[5],
		"fragment": parse[6]
	};
};

nc.parse_str = function (str) {
	var value = str.split("&"), vars = {}, param;
	for (var i = 0; i < value.length; i++) {
		param = value[i].split("=");
		vars[param[0]] = param[1];
	}
	return vars;
};

nc.parse_name = function (name, type) {
	if (type) {
		/* 下划线转驼峰 */
		name = name.replace(/_([a-z])/g, function ($0, $1) {
			return $1.toUpperCase();
		});
		/* 首字母大写 */
		name = name.replace(/[a-z]/, function ($0) {
			return $0.toUpperCase();
		});
	} else {
		/* 大写字母转小写 */
		name = name.replace(/[A-Z]/g, function ($0) {
			return "_" + $0.toLowerCase();
		});
		/* 去掉首字符的下划线 */
		if (0 === name.indexOf("_")) {
			name = name.substr(1);
		}
	}
	return name;
};

//scheme://host:port/path?query#fragment
nc.url = function (url, vars, suffix) {
	if (url.indexOf('http://') != -1 || url.indexOf('https://') != -1) {
		return url;
	}
	
	var info = this.parse_url(url), path = [], param = {}, reg;
	
	/* 验证info */
	info.path || alert("url格式错误！");
	url = info.path;
	/* 解析URL */
	path = url.split("/");
	path = [path.pop(), path.pop(), path.pop()].reverse();
	path[1] = path[1] || this.route[1];
	path[0] = path[0] || this.route[0];
//  param[this.route[0]] = path[0];
//  param[this.route[1]] = path[1];
//  param[this.route[2]] = path[2].toLowerCase();
//	url = param[this.route[0]] + '/' + param[this.route[1]] + '/' + param[this.route[2]];
	param[this.route[2]] = path[0];
	param[this.route[3]] = path[1];
	param[this.route[4]] = path[2].toLowerCase();
	url = param[this.route[2]] + '/' + param[this.route[3]] + '/' + param[this.route[4]];
	/* 解析参数 */
	if (typeof vars === "string") {
		vars = this.parse_str(vars);
	} else if (!$.isPlainObject(vars)) {
		vars = {};
	}
	/* 添加伪静态后缀 */
	if (false !== suffix) {
		suffix = suffix || 'html';
		if (suffix) {
			url += "." + suffix;
		}
	}
	/* 解析URL自带的参数 */
	info.query && $.extend(vars, this.parse_str(info.query));
	/* 判断站点id是否存在 */
	var site = '';
	if (vars.site_id) {
		var site_id = vars.site_id;
		delete vars.site_id;
		site = 's' + parseInt(site_id) + '/';
	} else {
		var site_id = this.route[0];
		site = site_id > 0 ? 's' + parseInt(site_id) + '/' : '';
	}
	var addon = '';
	if (info.scheme != '' && info.scheme != undefined) {
		addon = info.scheme + '/';
	}
	url = site + addon + url;
	if (vars) {
		var param_str = $.param(vars);
		if ('' !== param_str) {
			url += ((this.baseUrl + url).indexOf('?') !== -1 ? '&' : '?') + param_str;
		}
	}
	url = this.baseUrl + url;
	return url;
};

/**
 * 处理图片路径
 * type 类型 BIG MID SMALL THUMB
 */
nc.img = function (path, type = '') {
	if (path.indexOf("http://") == -1 && path.indexOf("https://") == -1) {
		var start = path.lastIndexOf('.');
		type = type ? '_' + type : '';
		var base_url = this.baseUrl.replace('/?s=', '');
		var base_url = base_url.replace('/index.php', '');
		var suffix = path.substring(start);
		var path = path.substring(0, start);
		var first = path.split("/");
		if (isNaN(first[0])) {
			var true_path = base_url + path + type + suffix;
		} else {
			var true_path = base_url + 'attachment/' + path + type + suffix;
		}
	} else {
		var true_path = path;
	}
	return true_path;
};

/**
 * 时间戳转时间
 *
 */
var default_time_format = 'YYYY-MM-DD h:m:s';
nc.time_to_date = function (timeStamp, time_format = '') {
	
	time_format = time_format == '' ? default_time_format : time_format;
	if (timeStamp > 0) {
		var date = new Date();
		date.setTime(timeStamp * 1000);
		var y = date.getFullYear();
		var m = date.getMonth() + 1;
		m = m < 10 ? ('0' + m) : m;
		var d = date.getDate();
		d = d < 10 ? ('0' + d) : d;
		var h = date.getHours();
		h = h < 10 ? ('0' + h) : h;
		var minute = date.getMinutes();
		var second = date.getSeconds();
		minute = minute < 10 ? ('0' + minute) : minute;
		second = second < 10 ? ('0' + second) : second;
		var time = '';
		time += time_format.indexOf('Y') > -1 ? y : '';
		time += time_format.indexOf('M') > -1 ? '-' + m : '';
		time += time_format.indexOf('D') > -1 ? '-' + d : '';
		time += time_format.indexOf('h') > -1 ? ' ' + h : '';
		time += time_format.indexOf('m') > -1 ? ':' + minute : '';
		time += time_format.indexOf('s') > -1 ? ':' + second : '';
		return time;
	} else {
		return "";
	}
};

/**
 * 日期 转换为 Unix时间戳
 * @param <string> 2014-01-01 20:20:20  日期格式
 * @return <int>        unix时间戳(秒)
 */
nc.date_to_time = function (string) {
	var f = string.split(' ', 2);
	var d = (f[0] ? f[0] : '').split('-', 3);
	var t = (f[1] ? f[1] : '').split(':', 3);
	return (new Date(
		parseInt(d[0], 10) || null,
		(parseInt(d[1], 10) || 1) - 1,
		parseInt(d[2], 10) || null,
		parseInt(t[0], 10) || null,
		parseInt(t[1], 10) || null,
		parseInt(t[2], 10) || null
	)).getTime() / 1000;
};

/**
 * url 反转义
 * @param url
 */
nc.urlReplace = function (url) {
	var new_url = url.replace(/%2B/g, "+");//"+"转义
	new_url = new_url.replace(/%26/g, "&");//"&"
	new_url = new_url.replace(/%23/g, "#");//"#"
	new_url = new_url.replace(/%20/g, " ");//" "
	new_url = new_url.replace(/%3F/g, "?");//"#"
	new_url = new_url.replace(/%25/g, "%");//"#"
	new_url = new_url.replace(/&3D/g, "=");//"#"
	new_url = new_url.replace(/%2F/g, "/");//"#"
	return new_url;
};

/**
 * 需要定义APP_KEY,API_URL
 * method 插件名.控制器.方法
 * data  json对象
 * async 是否异步，默认true 异步，false 同步
 */
nc.api = function (method, param, callback, async) {
	// async true为异步请求 false为同步请求
	var async = async != undefined ? async : true;
	param.app_key = APP_KEY;
	$.ajax({
		type: 'get',
		url: API_URL + '?s=/api/index/get/method/' + method + '/version/1.0',
		dataType: "JSON",
		async: async,
		data: {'param': JSON.stringify(param), method: method},
		success: function (res) {
			if (callback) callback(eval("(" + res + ")"));
		}
	});
};

/**
 * url 反转义
 * @param url
 */
nc.append_url_params = function (url, params) {
	if (params != undefined) {
		var url_params = '';
		for (var k in params) {
			url_params += "&" + k + "=" + params[k];
		}
		url += url_params;
	}
	return url;
};

/**
 * 生成随机不重复字符串
 * @param len
 * @returns {string}
 */
nc.gen_non_duplicate = function (len) {
	return Number(Math.random().toString().substr(3, len) + Date.now()).toString(36);
};

/**
 * 获取分页参数
 * @param param 参数
 * @returns {{layout: string[]}}
 */
nc.get_page_param = function (param) {
	var obj = {
		layout: ['count', 'limit', 'prev', 'page', 'next']
	};
	if (param != undefined) {
		if (param.limit != undefined) {
			obj.limit = param.limit;
		}
	}
	return obj;
};

/**
 * 弹出框，暂时没有使用
 * @param options 参数，参考layui：https://www.layui.com/doc/modules/layer.html
 */
nc.open = function (options) {
	if (!options) options = {};
	
	options.type = options.type || 1;
	
	//宽高，小、中、大
	// options.size
	options.area = options.area || ['500px'];
	layer.open(options);
};

/**
 * 上传
 * @param id
 * @param method
 * @param param
 * @param callback
 * @param async
 */
nc.upload_api = function (id, method, param, callback, async) {
	// async true为异步请求 false为同步请求
	var async = async != undefined ? async : true;
	param.app_key = APP_KEY;
	var file = document.getElementById(id).files[0];
	var formData = new FormData();
	formData.append("file", file);
	formData.append("method", method);
	formData.append("param", JSON.stringify(param));
	$.ajax({
		url: API_URL + '?s=/api/index/get/method/' + method + '/version/1.0',
		type: "post",
		data: formData,
		dataType: "JSON",
		contentType: false,
		processData: false,
		async: async,
		mimeType: "multipart/form-data",
		success: function (res) {
			if (callback) callback(eval("(" + res + ")"));
		},
		// error: function (data) {
		//     console.log(data);
		// }
	});
};

/**
 * 复制
 * @param dom
 * @param callback
 */
nc.copy = function JScopy(dom, callback) {
	var url = document.getElementById(dom);
	url.select();
	document.execCommand("Copy");
	var o = {
		url: url.value
	};

	if (callback) callback.call(this, o);
	layer.msg('复制成功');
};

var show_link_box_flag = true;
/**
 * 弹出框-->选择链接
 * @param link
 * @param callback
 */
nc.select_link = function (link, callback) {
	
	var url = nc.url("sitehome/diy/link");
	if (show_link_box_flag) {
		show_link_box_flag = false;
		$.post(url, {link: JSON.stringify(link)}, function (str) {
			window.linkIndex = layer.open({
				type: 1,
				title: "选择链接",
				content: str,
				btn: [],
				area: ['600px', '630px'], //宽高
				maxWidth: 1920,
				cancel: function (index, layero) {
					show_link_box_flag = true;
				},
				end: function () {
					
					if (window.linkData) {
						
						if (callback) callback(window.linkData);
					}
					
					show_link_box_flag = true;
					
				}
			});
		});
	}
};

var show_promote_flag = true;

/**
 * 推广链接
 * @param data
 */
nc.page_promote = function (data) {
	
	var url = nc.url("sitehome/diy/promote");
	if (show_promote_flag) {
		show_promote_flag = false;
		$.post(url, {data: JSON.stringify(data)}, function (str) {
			window.promoteIndex = layer.open({
				type: 1,
				title: "推广链接",
				content: str,
				btn: [],
				area: ['680px', '600px'], //宽高
				maxWidth: 1920,
				cancel: function (index, layero) {
					show_promote_flag = true;
				},
				end: function () {
					show_promote_flag = true;
				}
			});
		});
	}
};

/**
 * 数据表格
 * layui官方文档：https://www.layui.com/doc/modules/table.html
 * @param options
 * @constructor
 */
function Table(options) {
	if (!options) return;
	
	var _self = this;
	options.parseData = options.parseData || function (data) {
		return {
			"code": data.code,
			"msg": data.message,
			"count": data.data.count,
			"data": data.data.list
		};
	};
	
	if (options.page == undefined) {
		options.page = {
			layout: ['count', 'limit', 'prev', 'page', 'next'],
			limit: 10
		};
	}
	
	options.defaultToolbar = options.defaultToolbar || [];//'filter', 'print', 'exports'
	
	options.toolbar = options.toolbar || "";//头工具栏事件
	
	options.skin = options.skin || 'line';
	options.size = options.size || "";
	options.done = function (res, curr, count) {
		//加载图片放大
		loadImgMagnify();
	};
	
	layui.use('table', function () {
		_self._table = layui.table;
		_self._table.render(options);
	});
	
	this.filter = options.filter || options.elem.replace(/#/g, "");
	this.elem = options.elem;
	
	//获取当前选中的数据
	this.checkStatus = function () {
		return this._table.checkStatus(_self.elem.replace(/#/g, ""));
	};
}

/**
 * 监听头工具栏事件
 * @param callback 回调
 */
Table.prototype.toolbar = function (callback) {
	var _self = this;
	var interval = setInterval(function () {
		if (_self._table) {
			_self._table.on('toolbar(' + _self.filter + ')', function (obj) {
				var checkStatus = _self._table.checkStatus(obj.config.id);
				obj.data = checkStatus.data;
				obj.isAll = checkStatus.isAll;
				if (callback) callback.call(this, obj);
			});
			clearInterval(interval);
		}
	}, 50);
};

/**
 * 监听底部工具栏事件
 * @param callback 回调
 */
Table.prototype.bottomToolbar = function (callback) {
	var _self = this;
	var interval = setInterval(function () {
		if (_self._table) {
			_self._table.on('bottomToolbar(' + _self.filter + ')', function (obj) {
				var checkStatus = _self._table.checkStatus(obj.config.id);
				obj.data = checkStatus.data;
				obj.isAll = checkStatus.isAll;
				if (callback) callback.call(this, obj);
			});
			clearInterval(interval);
		}
	}, 50);
};

/**
 * 绑定layui的on事件
 * @param name
 * @param callback
 */
Table.prototype.on = function (name, callback) {
	var _self = this;
	var interval = setInterval(function () {
		if (_self._table) {
			_self._table.on(name + '(' + _self.filter + ')', function (obj) {
				if (callback) callback.call(this, obj);
			});
			clearInterval(interval);
		}
	}, 50);
};

/**
 * //监听行工具事件
 * @param callback 回调
 */
Table.prototype.tool = function (callback) {
	var _self = this;
	var interval = setInterval(function () {
		if (_self._table) {
			_self._table.on('tool(' + _self.filter + ')', function (obj) {
				if (callback) callback.call(this, obj);
			});
			clearInterval(interval);
		}
	}, 50);
};

/**
 * 刷新数据
 * @param options 参数，参考layui数据表格参数
 */
Table.prototype.reload = function (options) {
	options = options || {
		page: {
			curr: 1
		}
	};
	var _self = this;
	var interval = setInterval(function () {
		if (_self._table) {
			_self._table.reload(_self.elem.replace(/#/g, ""), options);
			clearInterval(interval);
		}
	}, 50);
};

var layedit;

/**
 * 富文本编辑器
 * https://www.layui.com/v1/doc/modules/layedit.html
 * @param id
 * @param options 参数，参考layui
 * @param callback 监听输入回调
 * @constructor
 */
function Editor(id, options, callback) {
	options = options || {};
	this.id = id;
	var _self = this;
	layui.use(['layedit'], function () {
		layedit = layui.layedit;
		layedit.set({
			uploadImage: {
				url: nc.url("file://common/File/image")
			},
			callback: callback
		});
		_self.index = layedit.build(id, options);
	});
}

/**
 * 设置内容
 * @param content 内容
 * @param append 是否追加
 */
Editor.prototype.setContent = function (content, append) {
	var _self = this;
	var time = setInterval(function () {
		layedit.setContent(_self.index, content, append);
		clearInterval(time);
	}, 150);
};

Editor.prototype.getContent = function () {
	return layedit.getContent(this.index);
};

Editor.prototype.getText = function () {
	return layedit.getText(this.index);
};

$(function () {
	
	loadImgMagnify();
});

//图片最大递归次数
var IMG_MAX_RECURSIVE_COUNT = 6;
var count = 0;

/**
 * //加载图片放大
 */
function loadImgMagnify() {
	setTimeout(function () {
		$("img[src!=''][layer-src]").each(function () {
			var id = getId($(this).parent());
			// console.log(id);
			layer.photos({
				photos: "#" + id,
				anim: 5
			});
			count = 0;
		});
	}, 100);
}

function getId(o) {
	count++;
	var id = o.attr("id");
	// console.log("递归次数:", count);
	if (id == undefined && count < IMG_MAX_RECURSIVE_COUNT) {
		id = getId(o.parent());
	}
	if (id == undefined) {
		id = nc.gen_non_duplicate(10);
		o.attr("id", id);
	}
	return id;
}

// 返回(关闭弹窗)
function back() {
	layer.closeAll('page');
}