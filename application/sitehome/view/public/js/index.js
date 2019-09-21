$(function(){
	 siteUser(1);
	 $(".layui-carousel-ind.member ul li").mouseover(function(){
	 	  $(this).siblings().removeClass("layui-this");
	 	  $(this).addClass("layui-this");
	 	  var type = $(this).attr('data-val');
	 	  siteUser(type);
	 });
})
// 站点会员
function siteUser(type){
	return;
	var names = [];    //类别数组（实际用来盛放X轴坐标值）
    var nums = [];    //销量数组（实际用来盛放Y坐标值）
	var myChart = echarts.init(document.getElementById('focusCharts'));
         // 显示标题，图例和空的坐标轴
         myChart.setOption({
             title: {
                 text: ''
             },
             tooltip: {},
             legend: {
                 data:['人数']
             },
             xAxis: {
                 data: names
             },
             yAxis: {},
             series: [{
                 name: '人数',
                 type :'line',
                 data: nums
             }]
         });
         
         myChart.showLoading();    //数据加载完之前先显示一段简单的loading动画
         
         $.ajax({
	         type : "get",
	         async : true,            //异步请求（同步请求将会锁住浏览器，用户其他操作必须等待请求完成才可以执行）
	         url : nc.url('sitehome/Index/getUserCount'),    //请求发送到TestServlet处
	         data : {"date":type},
	         dataType : "json",        //返回数据形式为json
	         success : function(result) {
	             //请求成功时执行该函数内容，result即为服务器返回的json对象
	             if (result) {
	             	 //  console.log(result);
	            	     for(var i=0;i<result.length;i++){       
	                        names.push(result[i][0]);    //挨个取出类别并填入类别数组
	                     }
	                     for(var i=0;i<result.length;i++){       
	                         nums.push(result[i][1]);    //挨个取出销量并填入销量数组
	                     }
	                    myChart.hideLoading();    //隐藏加载动画
	                    myChart.setOption({        //加载数据图表
	                        xAxis: {
	                            data: names
	                        },
	                        series: [{
	                            name: '人数',
	                            data: nums
	                        }]
	                    });
	             }
	        },
	        error : function(errorMsg) {
	             //请求失败时执行该函数
		         console.log("图表请求数据失败!");
		         myChart.hideLoading();
	        }
	    })
}
