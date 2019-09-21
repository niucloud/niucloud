/**
 * 图文导航·组件
 * 开发时间：2018年7月25日17:19:37
 * 1、该组件不会对图片大小进行限制，会自适应图片比例
 * 2、上传图片的比例，最好统一，效果最佳
 */

var graphicNavPreviewHtml = '<div v-bind:id="id" class="graphic-nav">';
		graphicNavPreviewHtml += '<div class="wrap">';
			graphicNavPreviewHtml += '<div v-for="(item,index) in list" class="item" v-bind:style="{ backgroundColor : data.backgroundColor,padding : (data.padding + \'px 0\') }">';
				graphicNavPreviewHtml += '<a href="javascript:;">';
					graphicNavPreviewHtml += '<img v-show="data.selectedTemplate ==\'imageNavigation\'" v-bind:src="item.imageUrl? $parent.$parent.changeImgUrl(item.imageUrl) : \'' + DIYVIEWIMG + '/crack_figure.png\'" />';
					graphicNavPreviewHtml += '<span v-show="item.title" v-bind:style="{ color: data.textColor }">{{item.title}}</span>';
					graphicNavPreviewHtml += '</a>';
			graphicNavPreviewHtml += '</div>';
		graphicNavPreviewHtml += '</div>';
		
	graphicNavPreviewHtml += '</div>';

Vue.component("graphic-nav", {
	data: function () {
		return {
			id: "graphic_nav_" + nc.gen_non_duplicate(10),
			data: this.$parent.data,
			list: this.$parent.data.list,
			selectedTemplate : this.$parent.data.selectedTemplate
		}
	},
	created: function () {
		this.refresh();
	},
	watch : {
		list : function () {
			this.refresh();
		}
	},
	methods: {
		
		refresh : function () {
			
			var _self = this;
			setTimeout(function () {
				hui('#' + _self.id).scrollX((_self.data.scrollSetting == "fixed") ? _self.data.list.length : 3, '.item');
				
				setTimeout(function () {
					//解决渲染时加载闪动
					$('#' + _self.id).css("visibility","visible");
				},5);
				
			}, 10);
		}
		
	},
	template: graphicNavPreviewHtml
});


/**
 * [图片导航的图片]·组件
 */
var graphicNavListHtml = '<div class="graphic-nav-list">';

		graphicNavListHtml += '<div class="layui-form-item">';
			graphicNavListHtml += '<label class="layui-form-label sm">选择模板</label>';
			graphicNavListHtml += '<div class="layui-input-block">';
				graphicNavListHtml += '<div class="template-list">';
				
					graphicNavListHtml += '<div v-for="(item,i) in selectedTemplateList" v-bind:class="[\'template-item\',\'nc-border-color-selected\',selectedTemplate == item.value ? \'selected\' : \'\']" v-on:click="selectedTemplate =item.value">';
						graphicNavListHtml += '<img v-bind:src="item.src" width="90px" height="64px">';
						graphicNavListHtml += '<span>{{item.name}}</span>';
					graphicNavListHtml += '</div>';
					
				graphicNavListHtml += '</div>';
			graphicNavListHtml += '</div>';
		graphicNavListHtml += '</div>';

		graphicNavListHtml += '<color></color>';
		graphicNavListHtml += '<color v-bind:data="{ field : \'backgroundColor\', label : \'背景颜色\' }"></color>';

		graphicNavListHtml += '<div class="layui-form-item">';
			graphicNavListHtml += '<label class="layui-form-label sm">上下边距</label>';
			graphicNavListHtml += '<div class="layui-input-block">';
				graphicNavListHtml += '<input type="number" v-bind:value="padding" v-on:keyup="changePadding($event)" placeholder="上下边距1~100px" class="layui-input" />';
			graphicNavListHtml += '</div>';
		graphicNavListHtml += '</div>';

//		graphicNavListHtml += '<div class="layui-form-item">';
//			graphicNavListHtml += '<label class="layui-form-label">图片比例</label>';
//			graphicNavListHtml += '<div class="layui-input-block">';
//				graphicNavListHtml += '<input type="number" v-bind:value="imageScale" v-on:keyup="changeImageScale($event)" placeholder="图片比例1~100%" class="layui-input" />';
//			graphicNavListHtml += '</div>';
//		graphicNavListHtml += '</div>';

		graphicNavListHtml += '<div class="layui-form-item">';
			graphicNavListHtml += '<label class="layui-form-label sm">滚动设置</label>';
			graphicNavListHtml += '<div class="layui-input-block sm">';
				graphicNavListHtml += '<div v-for="(item,i) in scrollSettingList" v-bind:class="{ \'layui-unselect layui-form-radio\' : true,\'layui-form-radioed\' : (scrollSetting==item.value) }" v-on:click="scrollSetting=item.value">';
					graphicNavListHtml += '<i class="layui-anim layui-icon">&#xe643;</i>';
					graphicNavListHtml += '<div>{{item.name}}</div>';
				graphicNavListHtml += '</div>';

			graphicNavListHtml += '</div>';
		graphicNavListHtml += '</div>';
	
		graphicNavListHtml += '<ul>';
			graphicNavListHtml += '<li v-for="(item,index) in list" v-bind:key="index">';

				graphicNavListHtml += '<img-upload v-bind:data="{ data : item }" v-bind:condition="$parent.data.selectedTemplate == \'imageNavigation\'"></img-upload>';

				graphicNavListHtml += '<div class="content-block" v-bind:class="$parent.data.selectedTemplate">';
					graphicNavListHtml += '<div class="layui-form-item">';
						graphicNavListHtml += '<label class="layui-form-label sm">标题</label>';
						graphicNavListHtml += '<div class="layui-input-block">';
							graphicNavListHtml += '<input type="text" name=\'title\' v-model="item.title" class="layui-input" />';
						graphicNavListHtml += '</div>';
					graphicNavListHtml += '</div>';
				
					graphicNavListHtml += '<nc-link v-bind:data="{ field : $parent.data.list[index].link }"></nc-link>';
				graphicNavListHtml += '</div>';
				
				graphicNavListHtml += '<i class="del" v-on:click="list.splice(index,1)">x</i>';
				
				graphicNavListHtml += '<div class="error-msg"></div>';
				
			graphicNavListHtml += '</li>';
			
		graphicNavListHtml += '</ul>';
		
		graphicNavListHtml += '<div class="add-item" v-if="showAddItem" v-on:click="list.push({ imageUrl : \'\', title : \'\', link : {} })">';
		
			graphicNavListHtml += '<i>+</i>';
		
			graphicNavListHtml += '<span>添加一个图文导航</span>';
		
			graphicNavListHtml += '</div>';
		
		graphicNavListHtml += '<p class="hint">最多添加 {{maxTip}} 个导航，拖动选中的导航可对其排序</p>';
		
	graphicNavListHtml += '</div>';
	
Vue.component("graphic-nav-list",{
	
	data : function(){
		return {
            data : this.$parent.data,
			showAddItem : true,
			list : this.$parent.data.list,
			scrollSettingList : [{
				name : "固定",
				value : "fixed",
				max : 5
			},{
				name : "横向滚动",
				value : "horizontal-scroll",
				max : 20
			}],
			scrollSetting : this.$parent.data.scrollSetting,
			// imageScale : this.$parent.data.imageScale,
			padding : this.$parent.data.padding,
			selectedTemplate : this.$parent.data.selectedTemplate,
			maxTip : 5,//最大上传数量提示
			selectedTemplateList : [{
				name : '图片导航',
				value : 'imageNavigation',
				src : RESOURCEPATH + "/component/view/graphic_nav/img/image_navigation.png"
			},{
				name : '文字导航',
				value : 'textNavigation',
				src : RESOURCEPATH + "/component/view/graphic_nav/img/text_navigation.png"
			}]
			
		};
	},
	
	watch : {
		list : function(){
			this.changeShowAddItem();
		},
		scrollSetting : function(){
			//更新父级对象
			this.$parent.data.scrollSetting = this.scrollSetting;
			
			//当前滚动方式切换到固定时，要检测当前集合是否超过最多限制max
			if(this.scrollSetting == this.scrollSettingList[0].value && this.list.length>this.scrollSettingList[0].max){
				this.list.splice(5,this.scrollSettingList[0].max);
			}
			this.changeShowAddItem();
			
			this.refresh();
			
		},
		selectedTemplate : function(){
			this.$parent.data.selectedTemplate = this.selectedTemplate;
			if(this.selectedTemplate == "imageNavigation"){
				$(".draggable-element[data-index='" + vue.currentIndex + "'] .graphic-navigation .graphic-nav-list>ul>li input[name='title']").removeAttr("style");
			}
		}
	},
	
	methods : {
		
		//改变图文导航按钮的显示隐藏
		changeShowAddItem : function(){

			if(this.scrollSetting == this.scrollSettingList[0].value){
				
				if(this.list.length >= this.scrollSettingList[0].max) this.showAddItem = false;
				else this.showAddItem = true;
				
				this.maxTip =  this.scrollSettingList[0].max;
				
			}else if(this.scrollSetting == this.scrollSettingList[1].value){
				
				if(this.list.length >= this.scrollSettingList[1].max) this.showAddItem = false;
				else this.showAddItem = true;

				this.maxTip =  this.scrollSettingList[1].max;
				
			}
		},
		
		//改变图片比例
		changeImageScale : function(event){
			var v = event.target.value;
			if(v != ""){
				if(v > 0 && v <= 100){
					this.imageScale = v;
					this.$parent.data.imageScale =  this.imageScale;//更新父级对象
				}else{
					layer.msg("请输入合法数字1~100");
				}
			}else{
				layer.msg("请输入合法数字1~100");
			}
		},
		
		//改变上下边距
		changePadding : function(event){
			var v = event.target.value;
			if(v != ""){
				if(v >= 0 && v <= 100){
					this.padding = v;
					this.$parent.data.padding =  this.padding;//更新父级对象
				}else{
					layer.msg("请输入合法数字0~100");
				}
			}else{
				layer.msg("请输入合法数字0~100");
			}
		},
		refresh : function () {
			
			var id = $(".draggable-element[data-index='" + this.$parent.data.index + "'] .graphic-navigation .preview-draggable .graphic-nav").attr("id");
			
			var _self = this;
			setTimeout(function () {
				hui('#' + id).scrollX((_self.scrollSetting == "fixed") ? _self.list.length : 3, '.item');
				
				setTimeout(function () {
					//解决渲染时加载闪动
					$('#' + id).css("visibility","visible");
				},5);
				
			}, 10);
		}
		
	},
	
	created : function(){
		this.changeShowAddItem();
	},
	
	template : graphicNavListHtml
});

// $('.graphic-navigation .graphic-nav-list>ul').DDSort({
//
// 	//拖拽数据源
// 	target: 'li',
//
// 	//拖拽时显示的样式
// 	floatStyle: {
// 		'border': '1px solid #0d73f9',
// 		'background-color': '#ffffff'
// 	},
//
// 	//设置可拖拽区域
// //	draggableArea : "preview-draggable",
//
// 	//拖拽中，将右侧编辑属性栏右侧，并且显示拖拽中提示信息
// 	move : function(){
// //		$(".edit-attribute-list").hide();
// 		$(".drag-in").show();
// 	},
//
// 	//拖拽结束后，还原右侧编辑属性栏，并且刷新数据
// 	up : function(){
// //		console.log($(".edit-attribute-list"));
// //		$(".edit-attribute-list").show();
// 		$(".drag-in").hide();
// //		vue.refresh();
// 	}
// });