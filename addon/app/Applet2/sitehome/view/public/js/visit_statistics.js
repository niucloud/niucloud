var chart_ALL;
var chart_HOME;
var chart_APP;
var option_ALL = {};
var option_HOME = {};
var option_APP = {};

var chart_array = ["ALL", "HOME", "APP"];
layui.use(['element', 'laydate'], function () {
	var laydate = layui.laydate;
	var element = layui.element;
	getVisitCountData();
	eachCreateChart();//初始渲染统计图
	//监听Tab切换，以改变地址hash值
	element.on('tab(chart_tab)', function (data) {
		renderChart($(this).data("type"));
	});
	//日期范围
	laydate.render({
		elem: '#daterange'
		, format: 'yyyy-MM-dd'
		, range: '至'
		, value: daterange //必须遵循format参数设定的格式
		, done: function (value, date, endDate) {
			daterange = value;
			eachUpdateChart();
		}
	});
	
});

/**
 * 循环创建 统计图
 * */
function eachCreateChart() {
	$.each(chart_array, function (name, value) {
		createChart(value);
	});
}

/**
 * 循环更新 统计图
 * */
function eachUpdateChart() {
	$.each(chart_array, function (name, value) {
		updateChart(value);
	});
}

/**
 * 渲染轮播图
 **/
function renderChart(type) {
	var chart_name = 'chart_' + type;
	if (window[chart_name] == undefined) {
		window[chart_name] = echarts.init(document.getElementById('main_' + type));
		window[chart_name].showLoading();
	}
	var option_name = 'option_' + type;
	window[chart_name].hideLoading();
	window[chart_name].setOption(window[option_name]);
}

/**
 * 创建统计图
 * @param type
 */
function createChart(type) {
	
	var chart_name = 'chart_' + type;
	var option_name = 'option_' + type;
	//折线图
	
	if ($(".chart-tab-item.layui-this").data("type") == type) {
		window[chart_name] = echarts.init(document.getElementById('main_' + type));
		window[chart_name].showLoading();
	}
	var chart_data = getChartData(type);
}

/**
 * 创建统计图
 * @param type
 */
function updateChart(type) {
	var chart_name = 'chart_' + type;
	var option_name = 'option_' + type;
	if ($(".chart-tab-item.layui-this").data("type") == type) {
		window[chart_name].showLoading();
	}
	var chart_data = getChartData(type);
	
}

/**
 * 获取统计数据
 * @param type
 */
function getChartData(type) {
	var result = {};
	var temp_daterange = daterange.split(' 至 ');
    temp_daterange = temp_daterange.join(' - ');
	$.ajax({
		type: "post",
		url: nc.url('sitehome/index/getVisitStatisticsData'),
		dataType: "JSON",
		data: {date_type: "daterange", type: type, daterange: temp_daterange},
		success: function (res) {
			chart_data = res.data;
			var chart_name = 'chart_' + type;
			var option_name = 'option_' + type;
			window[option_name] = {
				legend: {
					data: ['访问次数', '访问人数'],
					x: 'right',
					right: '20',
				},
				tooltip: {
					trigger: 'axis'
				},
				grid: {
					left: '20',
					right: '20',
					bottom: '20',
					containLabel: true
				},
				xAxis: {
					type: 'category',
					data: chart_data.date
				},
				yAxis: {
					type: 'value'
				},
				series: [
					{
						data: chart_data.data.count_data,
						type: 'line',
						smooth: true,
						stack: '次数',
						name: "访问次数",
						itemStyle : {
							normal : {
								color:'#12b7f5', //改变折线点的颜色
							},
						},
					},
					{
						data: chart_data.data.ip_count_data,
						type: 'line',
						smooth: true,
						stack: '人数',
						name: "访问人数"
					}
				]
			};
			if ($(".chart-tab-item.layui-this").data("type") == type) {
				window[chart_name].hideLoading();
				window[chart_name].setOption(window[option_name]);
			}
			
		}
	});
}

/**
 * 统计数据
 */
function getVisitCountData() {
	$.ajax({
		type: "post",
		url: nc.url('sitehome/index/getVisitCountData'),
		dataType: "JSON",
		success: function (res) {
			if (res.code == 0) {
				$("#today_count").text(res.data.today_count);
				$("#today_ip_count").text(res.data.today_ip_count);
				$("#yesterday_count").text(res.data.yesterday_count);
				$("#yesterday_ip_count").text(res.data.yesterday_ip_count);
				$("#week_count").text(res.data.week_count);
				$("#week_ip_count").text(res.data.week_ip_count);
				$("#month_count").text(res.data.month_count);
				$("#month_ip_count").text(res.data.month_ip_count);
			}
		}
	});
}