window.promoteIndex = -1;

function getPromote(data) {
	
	var url = nc.url("sitehome/diy/promote");
	if (show_promote_flag) {
		show_promote_flag = false;
		$.post(url, {data: JSON.stringify(data)}, function (str) {
			window.promoteIndex = layer.open({
				type: 1,
				title: "推广链接",
				content: str,
				btn: [],
				area: ['680px', '600px'], //宽高
				maxWidth: 1920,
				cancel: function (index, layero) {
					show_promote_flag = true;
				},
				end: function () {
					show_promote_flag = true;
				}
			});
		});
	}
}