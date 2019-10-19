/**
 * 公告·组件
 */

var noticePreviewHtml = '<div class="notice-view hui-flex">';
	noticePreviewHtml += '<div class="img-wrap"><img v-bind:src="image_url"/></div>';
	noticePreviewHtml += '<div v-bind:id="id" class="hui-scroll-news">';
		noticePreviewHtml += '<div v-for="(item,index) in list" class="hui-scroll-news-items">';
			noticePreviewHtml += '<a href="javascript:;" v-bind:style="{ color : data.textColor,fontSize : data.fontSize + \'px\' }">{{item.title}}</a>';
		noticePreviewHtml += '</div>';
		noticePreviewHtml += '<div v-show="list.length>1" class="hui-scroll-news-items">';
			noticePreviewHtml += '<a href="javascript:;" v-bind:style="{ color : data.textColor,fontSize : data.fontSize + \'px\' }">{{list[list.length-1].title}}</a>';
		noticePreviewHtml += '</div>';
	noticePreviewHtml += '</div>';
noticePreviewHtml += '</div>';

Vue.component("notice",{
	
	template : noticePreviewHtml,
	data : function(){
		
		return {
			id: "notice_" + nc.gen_non_duplicate(10),
			data : this.$parent.data,
			list : this.$parent.data.list,
			image_url : this.$parent.data.image_url,
			interval : null
		};
		
	},
	
	created: function(){
		
		this.image_url = this.image_url? this.$parent.$parent.changeImgUrl(this.image_url) : RESOURCEPATH + "/component/view/notice/img/notice.png";
		this.refresh();
	},
	methods : {
		
		//刷新滚动事件
		refresh : function(){
			
			var _self = this;
			if(this.list.length>1) {
				setTimeout(function () {
				_self.interval = hui.scrollNews("#" + _self.id, 5000);
				}, 10);
			}else{
				if(_self.interval) clearInterval(_self.interval);
			}
		},
	},
	
	watch : {
		list : function () {
			this.refresh();
		}
	}
});


var noticeEditHtml = '<div class="notice-config">';

	noticeEditHtml += '<font-size v-bind:data="{ value : $parent.data.fontSize }"></font-size>';
	noticeEditHtml += '<color></color>';
	noticeEditHtml += '<color v-bind:data="{ field : \'backgroundColor\', label : \'背景颜色\' }"></color>';
	
	noticeEditHtml += '<div class="layui-form-item" >';
		noticeEditHtml += '<label class="layui-form-label sm">左侧图片</label>';
		noticeEditHtml += '<div class="layui-input-block">';
			noticeEditHtml += '<img-upload v-bind:data="{ data : $parent.data, field : \'image_url\' }"></img-upload>';
		noticeEditHtml += '</div>';
	noticeEditHtml += '</div>';
	
	noticeEditHtml += '<ul>';
		noticeEditHtml += '<li v-for="(item,index) in list" v-bind:key="index">';
			noticeEditHtml += '<div class="content-block">';
				noticeEditHtml += '<div class="layui-form-item" >';
					noticeEditHtml += '<label class="layui-form-label sm">公告内容</label>';
					noticeEditHtml += '<div class="layui-input-block">';
						noticeEditHtml += '<input type="text" name=\'title\' v-model="item.title" class="layui-input" />';
					noticeEditHtml += '</div>';
				noticeEditHtml += '</div>';
			
				noticeEditHtml += '<nc-link v-bind:data="{ field : $parent.data.list[index].link }"></nc-link>';
			noticeEditHtml += '</div>';
			
			noticeEditHtml += '<i class="del" v-on:click="list.splice(index,1)">x</i>';
			
			noticeEditHtml += '<div class="error-msg"></div>';
			
		noticeEditHtml += '</li>';
	
	noticeEditHtml += '</ul>';

	noticeEditHtml += '<div class="add-item" v-on:click="list.push({ title:\'公告\',link:\'\' })">';
	
		noticeEditHtml += '<i>+</i>';
		
		noticeEditHtml += '<span>添加一条公告</span>';
	
	noticeEditHtml += '</div>';
	
	noticeEditHtml += '</div>';
	
Vue.component("notice-edit",{

	template : noticeEditHtml,
	data : function(){

		return {
			data : this.$parent.data,
			list : this.$parent.data.list,
		};

	},
	
	methods : {
		
		//改变图文导航按钮的显示隐藏
		changeShowAddItem : function(){

			if(this.list.length >= this.maxTip) this.showAddItem = false;
			else this.showAddItem = true;
			
		},
	},
	
	watch : {
		list : function(){
			this.changeShowAddItem();
		}
	}
});