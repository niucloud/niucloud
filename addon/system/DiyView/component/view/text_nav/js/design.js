/**
 * 文本导航中的属性插件
 */

var textNavPreviewHtml = '<div v-bind:id="id" class="text-navigation">';

		textNavPreviewHtml += '<div v-show="data.arrangement==\'horizontal\'" v-bind:class="[data.arrangement]">';
			textNavPreviewHtml += '<div class="item" v-for="item in list">';
				textNavPreviewHtml += '<a href="javascript:;" v-bind:style="{color: data.textColor}">{{item.text}}</a>';
			textNavPreviewHtml += '</div>';
		textNavPreviewHtml += '</div>';

		textNavPreviewHtml += '<div v-show="data.arrangement==\'vertical\'" v-bind:style="{ textAlign : data.textAlign }">';
			textNavPreviewHtml += '<a href="javascript:;" v-bind:style="{color: data.textColor}">{{list[0].text}}</a>';
		textNavPreviewHtml += '</div>';

textNavPreviewHtml += '</div>';

Vue.component("text-nav", {
	data: function () {
		return {
			id: "text_nav_" + nc.gen_non_duplicate(10),
			data: this.$parent.data,
			list: this.$parent.data.list
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
			
			if(this.data.arrangement == "horizontal"){
				var _self = this;
				setTimeout(function () {
					hui('#'+_self.id).scrollX(3, '.item');
				},10);
			}
		}
		
	},
	template: textNavPreviewHtml
});

var arrangementHtml = '<div class="layui-form-item">';
		arrangementHtml += '<label class="layui-form-label sm">排列方式</label>';
		arrangementHtml += '<div class="layui-input-block">';
		
			arrangementHtml += '<div v-bind:class="{ \'layui-unselect layui-form-select\' : true, \'layui-form-selected\' : isShowArrangement }" v-on:click="isShowArrangement=!isShowArrangement;">';
				arrangementHtml += '<div class="layui-select-title">';
					arrangementHtml += '<input type="text" placeholder="请选择" v-bind:value="($parent.data.arrangement==\'vertical\') ? \'竖排\' : \'横排\'" readonly="readonly" class="layui-input layui-unselect">';
					arrangementHtml += '<i class="layui-edge"></i>';
				arrangementHtml += '</div>';
				
				arrangementHtml += '<dl class="layui-anim layui-anim-upbit">';
					arrangementHtml += '<dd v-bind:class="{ \'layui-this\' : ($parent.data.arrangement==\'vertical\') }" v-on:click.stop="change(\'vertical\')">竖排</dd>';
					arrangementHtml += '<dd v-bind:class="{ \'layui-this\' : ($parent.data.arrangement==\'horizontal\') }" v-on:click.stop="change(\'horizontal\')">橫排</dd>';
				arrangementHtml += '</dl>';
			arrangementHtml += '</div>';
			
		arrangementHtml += '</div>';
arrangementHtml += '</div>';

Vue.component("arrangement", {
	data: function () {
		return {
			isShowArrangement: false
		}
	},
	methods: {
		change: function (v) {
			this.$parent.data.arrangement = v;
			if (v == "vertical") {
				for (var i = 1; i < this.$parent.data.list.length;) {
					this.$parent.data.list.splice(i, 1);
					i = 1;
				}
			}
			this.isShowArrangement = false;
		}
	},
	template: arrangementHtml
});