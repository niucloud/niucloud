/**
 * 图片广告的图片上传
 */
var imageAdsCarouselHtml = '<div class="layui-carousel" v-bind:id="id">';
		imageAdsCarouselHtml += '<div carousel-item>';
			imageAdsCarouselHtml += '<div class="image-ads-item carousel-posters" v-for="(item,index) in list">';
			
				imageAdsCarouselHtml += '<p v-show="item.imageUrl==\'\'">';
					imageAdsCarouselHtml += '<span>点击编辑图片广告</span>';
				imageAdsCarouselHtml += '</p>';
			
				imageAdsCarouselHtml += '<div v-show="item.imageUrl" v-bind:class="[$parent.data.fillStyle]">';
					imageAdsCarouselHtml += '<img v-bind:src="$parent.$parent.changeImgUrl(item.imageUrl)" draggable="false"/>';
				imageAdsCarouselHtml += '</div>';

				imageAdsCarouselHtml += '<span v-show="item.title">{{item.title}}</span>';
				
			imageAdsCarouselHtml += '</div>';
		imageAdsCarouselHtml += '</div>';
	imageAdsCarouselHtml += '</div>';

Vue.component("image-ads-carouse",{
	
	props : ['index'],
	data : function(){
		return {
			id : GenNonDuplicateID(10) + this.index,
			selectedTemplate : this.$parent.data.selectedTemplate,
			list : this.$parent.data.list,
			carousel : null,
            carousel_render : null
		}
	},
	created : function(){
		var self = this;
		setTimeout(function(){
			var elem = "#" + self.id;
			layui.use('carousel', function(){
				self.carousel = layui.carousel;
				self.carousel_render = self.carousel.render({ elem: elem , width : '100%' , height : '150px' , indicator : 'none' });
			});
		},10);
	},
	
	template : imageAdsCarouselHtml,
	
	watch : {
		list : function(){
			var self = this;
			setTimeout(function(){
                self.carousel_render.reload();//重置轮播
			},10);
		}
	}
});

var imageAdsListHtml = '<div class="image-ad-list">';
		imageAdsListHtml += '<ul>';
		
				imageAdsListHtml += '<li v-for="(item,index) in list" v-bind:data-sort="index" v-bind:data-index="index">';

					imageAdsListHtml += '<img-upload v-bind:data="{ data : item }"></img-upload>';
					
					imageAdsListHtml += '<div class="content-block">';
						imageAdsListHtml += '<div class="layui-form-item">';
							imageAdsListHtml += '<label class="layui-form-label sm">标题 </label>';
							imageAdsListHtml += '<div class="layui-input-block">';
								imageAdsListHtml += '<input type="text" v-model="item.title" class="layui-input" />';
							imageAdsListHtml += '</div>';
						imageAdsListHtml += '</div>';
							
						imageAdsListHtml += '<nc-link v-bind:data="{ field : $parent.data.list[index].link }"></nc-link>';
					imageAdsListHtml += '</div>';
					
					imageAdsListHtml += '<i class="del" v-on:click="deleteItem(index)">x</i>';

					imageAdsListHtml += '<div class="error-msg"></div>';
				imageAdsListHtml += '</li>';
				
		imageAdsListHtml += '</ul>';
		
		imageAdsListHtml += '<div class="add-item" v-on:click="addItem">';
			imageAdsListHtml += '<p>';
				imageAdsListHtml += '<i>+</i>';
				imageAdsListHtml += '<span>添加一个背景图</span>';
			imageAdsListHtml += '</p>';
		imageAdsListHtml += '</div>';
		
		imageAdsListHtml += '<p class="hint">轮播海报建议上传比例相同的图片</p>';//，拖动选中的图片广告可对其排序
	
	imageAdsListHtml += '</div>';

Vue.component("image-ads-list",{
	
	data : function(){
		return {
            data : this.$parent.data,
			list : this.$parent.data.list,
//			parentList : this.$parent.data.list
		}
	},
	created : function(){
		
//		//采用单项绑定，复制当前对象
//		this.list = JSON.parse(JSON.stringify(this.parentList));
//		
//		//初始化原始下标
//		for(var i=0;i<this.list.length;i++) this.list[i].originalIndex = i;
//		
//		var self = this;
//		var parent = vue.data[vue.currentIndex];
//		var index = vue.currentIndex;

//		setTimeout(function(){
//			$('.draggable-element[data-index="' + vue.currentIndex + '"] .image-ads .image-ad-list ul').DDSort({
//				
//				//拖拽数据源
//				target: 'li',
//				
//				//拖拽时显示的样式
//				floatStyle: {
//					'border': '1px solid #38f',
//					'background-color': '#ffffff'
//				},
//				
//				//拖拽结束后
//				up : function(){
//					
//					//记录拖拽后的顺序
//					var sortArray = new Array();
//
//					//遍历集合
//					$(".draggable-element[data-index='" + index + "'] .image-ad-list li").each(function(i){
//						
//						self.list[$(this).attr("data-index")].originalIndex = i;
//						
//						//因为是单项绑定，需要记录原始下标，用于后续CURD操作
//						sortArray.push($(this).attr("data-sort"));
//						
//						//还原排序
//						$(this).attr("data-sort",i);
//						
//					});
//
//					//复制当前对象
//					var tempList = JSON.parse(JSON.stringify(parent.list));
//					
//					for(var i=0;i<sortArray.length;i++){
//						parent.list[i] = tempList[sortArray[i]];
//					}
//
//					//触发变异方法，进行视图更新。
//					parent.list.push({});
//					parent.list.pop();
//					
//				}
//			});
//		},20);

	},
	
	methods : {
		
		addItem : function(){
			this.list.push({ imageUrl : "", title : "", link : {} });
		},
		
//		changeTitle : function(event,index){
//			
//			console.log(this.list[index].originalIndex);
//			
//			//改变内部数据
//			this.list[index].title = event.target.value;
//			
//			//改变外层原数据
//			this.parentList[this.list[index].originalIndex].title = event.target.value;
//
//			this.parentList.push({});
//			this.parentList.pop();
//		},
//		
		deleteItem : function(index){

			this.list.splice(index,1);
//			var originalIndex = this.list[index].originalIndex;
//			console.log(index,originalIndex,this.$parent.data.list.length);
//			console.log("删除父级集合中的第" + (originalIndex+1)+"个");
//			var a = this.getDeleteOriginalIndex(originalIndex);
//			console.log("A:"+a,"index:" + index);
////			if(originalIndex >=this.$parent.data.list.length){
////				console.log();
////			}
////				originalIndex--;
//			this.$parent.data.list.splice(a,1);
//			this.list.splice(index,1);
//			console.log(JSON.stringify(this.$parent.data.list));
//			console.log(JSON.stringify(this.$parent.data.list));
		},
//		getDeleteOriginalIndex : function(index){
//			if(index>=this.$parent.data.list.length){
//				index--;
//				return this.getDeleteOriginalIndex(index);
//			}
//			return index;
//		}
	
	},
	
	template : imageAdsListHtml
});