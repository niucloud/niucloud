/**
 * @param type  info 信息，warning 警告，success 成功
 * @param msg
 */
function showMsg(msg, type) {
	if (type == undefined) type = "info";
	new $.Display({
		display: 'messager',
		autoHide: 1500,
		placement: "center",
		closeButton: false
	}).show({
		content: msg,
		type: type
	});
}

/**
 * 遮罩层对象
 * 创建时间：2018年11月15日10:57:27  xxs
 * @param dom    在遮罩层之上的DOM
 * @param callback    点击遮罩层触发回调
 * @constructor
 */
function MaskLayer(dom, callback) {
	this.dom = $(dom);
	this.callback = callback;
	this.created();
}

MaskLayer.prototype = {
	id: "",
	zIndex: 19961213,
	created: function () {
		this.id = "js-" + nc.gen_non_duplicate(3);
		var h = '<div class="niu-mask-layer ' + this.id + '" style="display:none;position: fixed;top: 0;right: 0;bottom: 0;left: 0;z-index: ' + this.zIndex + ';background-color: rgba(0,0,0,.6);"></div>';
		$("body").append(h);
		if (this.callback) {
			var self = this;
			$("body").on("click", "." + this.id, function () {
				self.callback();
				self.hide();
			});
		}
	},
	show: function () {
		this.dom.css("z-index", ++this.zIndex);
		$("." + this.id).show();
		//防止遮罩层之下滑动
		ModalHelper.afterOpen();
	},
	hide: function () {
		this.dom.css("z-index", "");
		$("." + this.id).hide();
		ModalHelper.beforeClose();
	}
};


//解决遮罩层防止穿透问题
var ModalHelper = (function (bodyCls) {
	var scrollTop;
	return {
		afterOpen: function () {
			scrollTop = document.scrollingElement.scrollTop;
			document.body.classList.add(bodyCls);
			document.body.style.top = -scrollTop + 'px';
		},
		beforeClose: function () {
			document.body.classList.remove(bodyCls);
			// scrollTop lost after set position:fixed, restore it back.
			document.scrollingElement.scrollTop = scrollTop;
		}
	};
})('mask-layer-open');

/**
 * 验证码验证
 * @param url
 * @param captcha
 * @param callback
 */
function checkCaptcha(url, captcha, callback) {
	$.ajax({
		url: url,
		type: 'post',
		dataType: 'json',
		data: {captcha: captcha},
		async: false,
		success: function (data) {
			if (callback) callback(data);
		}
	})
}

/**
 * 上下拉刷新滚动列表
 * 创建时间：2018年11月24日14:47:25
 * @param id
 * @param load_list
 * @param page_size
 * @returns {MeScroll}
 * @constructor
 */
function ScrollList(id, load_list, page_size) {
	page_size = page_size || 10;
	
	var mescroll = new MeScroll(id, {
		down: {
			auto: false, //是否在初始化完毕之后自动执行下拉回调callback; 默认true
			callback: function () {
				//下拉刷新的回调
				load_list(1, false);
			}
		},
		up: {
			auto: true, //是否在初始化时以上拉加载的方式自动加载第一页数据; 默认false
			isBounce: false, //此处禁止ios回弹
			callback: function (page) {
				//上拉加载的回调 page = {num:1, size:10}; num:当前页 从1开始, size:每页数据条数
				load_list(page.num, true);
			},
			page: {
				num: 0,
				size: page_size
			},
			toTop: { //配置回到顶部按钮图标
				src: STATIC + "/ext/mescroll/img/mescroll_to_top.png"
			}
		},
		lazyLoad: {
			use: true, // 是否开启懒加载,默认false
			attr: 'lazy-url' // 标签中网络图的属性名 : <img imgurl='网络图  src='占位图''/>
		}
	});
	return mescroll;
}