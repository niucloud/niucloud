/**
 * 顶部搜索·组件
 */
var productSearchHtml = '<div>';

	productSearchHtml += '<div class="layui-form-item" >';
		productSearchHtml += '<label class="layui-form-label sm">左侧图片</label>';
		productSearchHtml += '<div class="layui-input-block">';
			productSearchHtml += '<img-upload v-bind:data="{ data : $parent.data, field : \'left_img_url\' }"></img-upload>';
		productSearchHtml += '</div>';
		productSearchHtml += '<p class="hint">建议尺寸：30 x 30 像素</p>';
	productSearchHtml += '</div>';

	productSearchHtml += '<nc-link v-bind:data="{ field : $parent.data.left_link }"></nc-link>';
	
	productSearchHtml += '<div class="layui-form-item" >';
		productSearchHtml += '<label class="layui-form-label sm">右侧图片</label>';
		productSearchHtml += '<div class="layui-input-block">';
			productSearchHtml += '<img-upload v-bind:data="{ data : $parent.data, field : \'right_img_url\' }"></img-upload>';
		productSearchHtml += '</div>';
		productSearchHtml += '<p class="hint">建议尺寸：30 x 30 像素</p>';

	productSearchHtml += '</div>';

	productSearchHtml += '<nc-link v-bind:data="{ field : $parent.data.right_link }"></nc-link>';
	
	productSearchHtml += '</div>';

Vue.component("top-search",{

	template : productSearchHtml,
	data : function(){
		return {
			data : this.$parent.data
		};
	}
});