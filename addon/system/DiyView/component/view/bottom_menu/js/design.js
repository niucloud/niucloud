/**
 * 底部菜单·组件
 */
var bottomMenuHtml = '<div class="bottom-menu-config">';

	bottomMenuHtml += '<div class="layui-form-item">';
	 	bottomMenuHtml += '<label class="layui-form-label sm">导航类型</label>';
		bottomMenuHtml += '<div class="layui-input-block">';
			bottomMenuHtml += '<template v-for="(item,index) in typeList" v-bind:k="index">';
				bottomMenuHtml += '<div v-on:click="($parent.data.bottomMenuType=item.value)" v-bind:class="{ \'layui-unselect layui-form-radio\' : true,\'layui-form-radioed\' : ($parent.data.bottomMenuType==item.value) }"><i class="layui-anim layui-icon">&#xe643;</i><div>{{item.label}}</div></div>';
			bottomMenuHtml += '</template>';
		bottomMenuHtml += '</div>';
	bottomMenuHtml += '</div>';

	bottomMenuHtml += '<font-size v-bind:data="{ value : $parent.data.fontSize }"></font-size>';
	bottomMenuHtml += '<color></color>';
	
	bottomMenuHtml += '<ul>';
		bottomMenuHtml += '<li v-for="(item,index) in menuList" v-bind:key="index">';
			bottomMenuHtml += '<div class="image-block" v-show="$parent.data.bottomMenuType != 3">';
				bottomMenuHtml += '<img-upload v-bind:data="{ data : item }"></img-upload>';
			bottomMenuHtml += '</div>';
			
			bottomMenuHtml += '<div class="content-block" v-bind:class="$parent.data.bottomMenuType">';
				bottomMenuHtml += '<div class="layui-form-item" v-show="$parent.data.bottomMenuType == 1 || $parent.data.bottomMenuType == 3">';
					bottomMenuHtml += '<label class="layui-form-label sm">标题</label>';
					bottomMenuHtml += '<div class="layui-input-block">';
						bottomMenuHtml += '<input type="text" name=\'title\' v-model="item.title" class="layui-input" />';
					bottomMenuHtml += '</div>';
				bottomMenuHtml += '</div>';
			
				bottomMenuHtml += '<nc-link v-bind:data="{ field : $parent.data.list[index].link }"></nc-link>';
			bottomMenuHtml += '</div>';
			
			bottomMenuHtml += '<i class="del" v-on:click="menuList.splice(index,1)">x</i>';
			
			bottomMenuHtml += '<div class="error-msg"></div>';
			
		bottomMenuHtml += '</li>';
	
	bottomMenuHtml += '</ul>';

	bottomMenuHtml += '<div class="add-item" v-if="showAddItem" v-on:click="menuList.push({ imageUrl : \'\', title : \'菜单\', link : {} })">';
	
		bottomMenuHtml += '<i>+</i>';
		
		bottomMenuHtml += '<span>添加一个图文导航</span>';
	
	bottomMenuHtml += '</div>';
	
	bottomMenuHtml += '<p class="hint">最多添加 {{maxTip}} 个导航，拖动选中的导航可对其排序</p>';
	
	bottomMenuHtml += '</div>';

Vue.component("bottom-menu",{

	template : bottomMenuHtml,
	data : function(){

		return {
			data : this.$parent.data,
			typeList : [
					{ label : "图文", value : "1"  },
					{ label : "图片", value : "2"  },
					{ label : "文字", value : "3"  },
				],
			menuList : this.$parent.data.list,
			showAddItem : true,
			maxTip : 6,
		};

	},
	
	methods : {
		
		//改变图文导航按钮的显示隐藏
		changeShowAddItem : function(){

			if(this.menuList.length >= this.maxTip) this.showAddItem = false;
			else this.showAddItem = true;
			
		},
	},
	
	created : function(){
		// for(var i=0;i<this.menuList.length;i++){
			// if(this.menuList[i].imageUrl) this.menuList[i].imageUrl = changeImgUrl(this.menuList[i].imageUrl);
		// }
        // console.log("CREATE",this.menuList);
		//this.changeShowAddItem();
	},
	
	watch : {
		menuList : function(){
			this.changeShowAddItem();
		}
	}
});