/**
 * 会员中心·组件
 */
var memberImgHtml = '<div>';
		memberImgHtml += '<div class="layui-form-item">';
			memberImgHtml += '<label class="layui-form-label sm">背景图</label>';
			memberImgHtml += '<img-upload v-bind:data="{ data : $parent.data, field : \'background_image\' }"></img-upload>';
			memberImgHtml += '<div class="layui-advice">建议尺寸：320 x 160 像素</div>';
		memberImgHtml += '</div>';

		memberImgHtml += '<div class="layui-form-item">';
			memberImgHtml += '<label class="layui-form-label sm">信息位置</label>';
			memberImgHtml += '<div class="layui-input-block">';
				memberImgHtml += '<template v-for="(item,index) in list" v-bind:k="index">';
					memberImgHtml += '<div v-on:click="$parent.data.member_info_location=item.value" v-bind:class="{ \'layui-unselect layui-form-radio\' : true,\'layui-form-radioed\' : ($parent.data.member_info_location==item.value) }"><i class="layui-anim layui-icon">&#xe643;</i><div>{{item.label}}</div></div>';
				memberImgHtml += '</template>';
			memberImgHtml += '</div>';
		memberImgHtml += '</div>';
	memberImgHtml += '</div>';
		
Vue.component("nc-member-img",{

	template : memberImgHtml,
    data : function(){
        return {
            data : this.$parent.data,
            list : [
                { label : "居左", value : "left"   },
                { label : "居中", value : "center" },
                { label : "居右", value : "right"  }
            ]
        };
    }
});