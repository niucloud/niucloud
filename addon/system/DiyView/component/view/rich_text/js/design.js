var html = '<textarea v-bind:id="id"></textarea>';

Vue.component("rich-text", {
	template: html,
	data: function () {
		return {
			id: "",
		}
	},
	created: function () {
		
		var self = this;
		self.id = GenNonDuplicateID(10);
		setTimeout(function () {
			
			var editor = new Editor(self.id,{},function () {
				self.$parent.data.html = editor.getContent();
			});
			editor.setContent(self.$parent.data.html);
			
		}, 10);
		
	},
	methods: {
	}
});