$(function () {
	var myChart = echarts.init($('#fans_statistical')[0]);
	var xAxisData = [];
	for (var i = 30; i > 0; i--) {
		if (i % 3 == 0) {
			var myDate = new Date();
			myDate.setDate(myDate.getDate() - i);
			xAxisData.push(myDate.getFullYear() + "-" + (myDate.getMonth() + 1) + "-" + myDate.getDate());
		}
	}

// 指定图表的配置项和数据
	var option = {
		legend: {
			data: ['新关注人数', '取消关注人数', '净增关注人数', '累计关注人数'],
			left: "100px"
		},
		color: ['#2998FF', '#4ECB74', '#FBD950', '#F47D6F'],
		tooltip: {
			trigger: 'axis'
		},
		xAxis: {
			type: 'category',
			data: xAxisData,
		},
		yAxis: {
			type: 'value',
			axisLabel: {
				formatter: '{value}'
			},
			max: function (value) {
				return value.max + 1;
			},
			minInterval: 1,
		},
		series: [
			{
				name: '新关注人数',
				type: 'line',
				data: [],
			},
			{
				name: '取消关注人数',
				type: 'line',
				data: [],
			},
			{
				name: '净增关注人数',
				type: 'line',
				data: [],
			},
			{
				name: '累计关注人数',
				type: 'line',
				data: [],
				
			}
		]
	};
	
	myChart.showLoading();
	
	$.ajax({
		type: "post",
		url: nc.url('ncapplet://sitehome/index/getFansStatistical'),
		data: {time: xAxisData.toString()},
		success: function (res) {
			if (res) {
				for (var i = 0; i < res.length; i++) {
					option.series[0].data.push(res[i].subscribe);
					option.series[1].data.push(res[i].unsubscribe);
					option.series[2].data.push(res[i].net_gain);
					option.series[3].data.push(res[i].total);
				}
			}
			myChart.hideLoading();
			myChart.setOption(option);
		}
	});
});