/**
 * 弹窗广告·组件
 */

//[弹窗广告]属性组件
var html = '<div class="pop-window-config">';

		html += '<ul>';
			html += '<li >';
			html += '<div class="image-block">';
				html += '<img-upload v-bind:data="{ data : $parent.data, field : \'image_url\' }"></img-upload>';
			html += '</div>';
			
			html += '<div class="content-block">';
			
				html += '<nc-link v-bind:data="{ field : $parent.data.link }"></nc-link>';
				
			html += '</div>';
			
			html += '<div class="error-msg"></div>';
			
			html += '</li>';
		
		html += '</ul>';
	
	html += '</div>';

Vue.component("pop-window",{

	template : html,
	data : function(){

		return {
			data : this.$parent.data,
		};
	}
});