//最外层组件
var ncComponentHtml = '<div v-show="data.lazyLoadCss && data.lazyLoad">';

ncComponentHtml += '<div class="preview-draggable">';//拖拽区域
ncComponentHtml += '<slot name="preview"></slot>';
ncComponentHtml += '<i class="del" v-on:click.stop="$parent.delComponent(data.index)">x</i>';
ncComponentHtml += '</div>';

ncComponentHtml += '<div class="edit-attribute" v-bind:data-have-edit="($slots.edit ? \'1\' : \'0\')" v-show="$parent.currentIndex==data.index && $slots.edit">';
ncComponentHtml += '<slot name="edit"></slot>';
ncComponentHtml += '</div>';

ncComponentHtml += '<div style="display:none;">';
ncComponentHtml += '<slot name="resource"></slot>';
ncComponentHtml += '<slot name="verify"></slot>';
ncComponentHtml += '</div>';

ncComponentHtml += '</div>';

var ncComponent = {
	props: ["data"],
	template: ncComponentHtml,
	created: function () {
		
		//判断当前添加的组件是否有验证
		if (this.$slots.verify) {
			this.data.verify = this.$slots.verify[0].text.replace(/&lt;/g, "<");
			// console.log("Add verify");
		}
		
		//如果当前添加的组件没有添加过资源
		if (!this.$slots.resource) {
			this.data.lazyLoadCss = true;
			this.data.lazyLoad = true;
		} else {
			//检测是否只添加了JS或者CSS，没有添加默认为true
			var countCss = 0, countJs = 0, outerCountJs = 0;
			for (var i = 0; i < this.$slots.resource.length; i++) {
				if (this.$slots.resource[i].componentOptions) {
					if (this.$slots.resource[i].componentOptions.tag == "css") {
						countCss++;
					} else if (this.$slots.resource[i].componentOptions.tag == "js") {
						countJs++;
						//统计外部JS数量
						if (!$.isEmptyObject(this.$slots.resource[i].componentOptions.propsData)) outerCountJs++;
					}
				}
			}
			
			if (countCss == 0) this.data.lazyLoadCss = true;
			if (countJs == 0) this.data.lazyLoad = true;
			
			this.data.outerCountJs = outerCountJs;
			
		}
	}
};

/**
 * 手机端自定义模板Vue对象
 */
var vue = new Vue({
	
	el: "#diyView",
	
	data: {
		//当前编辑的组件位置
		currentIndex: 0,
		
		//全局属性
		global: {
			//模板标题
			title: "模板标题",
			
			//是否显示底部导航标识
			openBottomNav: false,
		},
		
		//自定义组件集合
		data: [],
	},
	
	components: {
		'nc-component': ncComponent,//最外层组件
	},
	
	mounted: function () {
		this.refresh();
		// console.log("vue is mounted");
	},
	
	methods: {
		
		addComponent: function (obj, other) {
			
			//附加公共字段
			obj.index = 0;
			obj.sort = 0;
			obj.lazyLoadCss = false;//资源懒加载，防止看到界面缓慢加载
			obj.lazyLoad = false;//资源懒加载，防止看到界面缓慢加载
			obj.outerCountJs = 0;
			
			//第一次添加组件时，添加以下字段
			if (other) {
				obj.addon_name = other.addon_name;
				obj.type = other.name;
				obj.name = other.title;
				obj.controller = other.controller;
			}
			
			if (other && !this.checkComponentIsAdd(obj.type, other.max_count)) {
				return;
			}
			
			this.data.push(obj);
			this.currentIndex = this.data.length - 1;
			this.refresh();

//			console.log(JSON.stringify(this.data));
		
		},
		
		//检测组件是否允许添加，true：允许 false：不允许
		checkComponentIsAdd: function (type, max_count) {
			//max_count为0时不处理
			if (max_count == 0) return true;
			
			var count = 0;
			
			//遍历已添加的自定义组件，检测是否超出数量
			for (var i in this.data) if (this.data[i].type == type) count++;
			
			if (count >= max_count) return false;
			
			else return true;
		},
		
		//改变当前编辑的组件选中
		changeCurrentIndex: function (sort) {
			this.currentIndex = sort;
			this.refresh();
		},
		
		//改变当前的删除弹出框的显示状态
		delComponent: function (i) {
			var self = this;
			
			layer.confirm('确定要删除吗?', {title: '操作提示'}, function (index) {
				
				self.data.splice(i, 1);
				//删除当前组件后，选中最后一个组件进行编辑
				if (self.data[self.data.length - 1]) {
					self.currentIndex = $(".draggable-element:last").attr("data-index");
					//删除组件后，进行重新排序
					self.refresh();
				}
				layer.close(index);
				
			});
		},
		
		//刷新数据排序
		refresh: function () {
			var self = this;
			//vue框架执行，异步操作组件列表的排序
			setTimeout(function () {
				
				$(".draggable-element").each(function (i) {
					$(this).attr("data-sort", i);
				});
				
				for (var i = 0; i < self.data.length; i++) {
					self.data[i].index = $(".draggable-element[data-index=" + i + "]").attr("data-index");
					self.data[i].sort = $(".draggable-element[data-index=" + i + "]").attr("data-sort");
				}
				
				//触发变异方法，进行视图更新。不能用sort()方法，会改变组件的顺序，导致显示的顺序错乱
				self.data.push({});
				self.data.pop();
				// console.log(self.currentIndex);
				// console.log("触发变异方法，进行视图更新。不能用sort()方法，会改变组件的顺序，导致显示的顺序错乱");
				
				//如果当前编辑的组件不存在了，则选中最后一个
				if (parseInt(self.currentIndex) >= self.data.length) self.currentIndex--;
				
			}, 50);
			
		},
		
		//转换图片路径
		changeImgUrl: function (url) {
			if (url == null || url == "") return '';
			if (url.indexOf("static/img/") > -1) return nc.img(STATICIMG + "/" + url.replace("static/img/", ""));
			else return nc.img(url);
		},
		
		//设置全局对象属性
		setGlobal: function (obj) {
			for (var k in obj) {
				this.$set(this.global, k, obj[k]);
			}
		},
		// test: function () {
		// 	var url = "http://localhost/niucloud_service/s10155/Draw/component/Design/headEdit";
		// 	$.post(url, {}, function (str) {
		// 		layer.open({
		// 			title: "业务信息",
		// 			type: 0,
		// 			// btn: [],
		// 			content: str,
		// 			maxWidth: 1020,
		// 		});
		// 	});
		//
		// }
		
		
	}
});

/**
 * 绑定拖拽事件
 * 创建时间：2018年7月3日18:50:11 全栈小学生
 */
$('.preview-block').DDSort({
	
	//拖拽数据源
	target: '.draggable-element',
	
	//拖拽时显示的样式
	floatStyle: {
		'border': '1px solid #0d73f9',
		'background-color': '#ffffff'
	},
	
	//设置可拖拽区域
	draggableArea: "preview-draggable",
	
	//拖拽中，隐藏右侧编辑属性栏
	move: function (index) {
		if ($(".draggable-element[data-index='" + index + "'] .edit-attribute").attr("data-have-edit") == 1)
			$(".draggable-element[data-index='" + index + "'] .edit-attribute").hide();
	},
	
	//拖拽结束后，选择当前拖拽，并且显示右侧编辑属性栏，刷新数据
	up: function (index) {
		if ($(".draggable-element[data-index='" + index + "'] .edit-attribute").attr("data-have-edit") == 1) {
			vue.currentIndex = index;
			$(".draggable-element[data-index='" + index + "'] .edit-attribute").show();
		}
		vue.refresh();
	}
});

//验证
function verifyData() {
	
	if (vue.title == 0) {
		layer.msg('请输入模板名称');
//		$("#title").focus();
		return false;
	}
	
	for (var i = 0; i < vue.data.length; i++) {
		
		if (vue.data[i].verify) {
			try {
				//动态加载verify函数
				eval(vue.data[i].verify);
				var res = verify();
				console.log(JSON.stringify(res));
				
				if (!res.code) {
					vue.currentIndex = i;
					layer.msg(res.message);
					return false;
				}
			} catch (e) {
				console.log("verify Error:" + e);
			}
		}
	}
	
	return true;
}