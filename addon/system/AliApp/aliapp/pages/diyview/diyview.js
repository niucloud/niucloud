const app = new getApp();

Page({

  /**
   * 页面的初始数据
   */
  data: {
    prompt: '',
    custom_info: [],
    diy_view_name: '',
    diy_view_id: 0,
    prev_id: 0,
    scroll_top: 0,
    background_color: '',
    background_url: ''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    let that = this;
    let params = {
      type: 'APPLET'
    };
    if (options.name) {
      params.name = options.name
      that.setData({
        diy_view_name: params.name
      })
    }

    if (options.id) {
      params.id = options.id
      that.setData({
        diy_view_id: params.id
      })
    }

    if (options.prev_id) {
      that.setData({
        prev_id: prev_id
      })
    }

    app.sendRequest({
      url: 'DiyView.Diy.getDiyViewData',
      data: params,
      method: 'get',
      success: function(res) {
        console.log(res);
        if (res.code == 0) {
          try {
            let data = JSON.parse(res.data.value);
            let custom_info = app.checkEmpty(data.value, []);
            that.setData({
              custom_info: custom_info,
            })
            data.global = app.checkEmpty(data.global, {});

            // 标题
            if (app.checkEmpty(data.global.title) != '') {
              wx.setNavigationBarTitle({
                title: data.global.title,
              })
            }

            //背景 设置
            let param = {};
            let key = '';
            // 背景色
            if (app.checkEmpty(data.global.bgColor) != '') {
              key = 'background_color';
              param[key] = data.global.bgColor;
            }

            // 背景图片
            if (app.checkEmpty(data.global.bgUrl) != '') {
              key = 'background_url';
              param[key] = app.IMG(data.global.bgUrl);
            }

            if (key != '') {
              that.setData(param);
            }

            console.log(custom_info);
          } catch (e) {
            //console.log(e);
            let prev_id = that.data.prev_id;
            if (prev_id > 0) {
              wx.navigateTo({
                url: '/pages/diyview/diyview?id=' + prev_id,
              })
            } else {
              try {
                wx.navigateBack({
                  delta: 1
                })
              } catch (e) {}
            }
          }
        }
      }
    })
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function() {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function() {

  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function() {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function() {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function() {

  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function() {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function() {

  },

  /**
   * 滚动
   */
  onPageScroll: function(e) {
    let scroll_top = app.checkEmpty(e.scrollTop, 0);
    let prev_scroll_top = this.data.scroll_top;
    // 防止影响性能，大于0时值仅变动一次
    if ((scroll_top == 0 && prev_scroll_top != 0) || (scroll_top > 0 && prev_scroll_top == 0)) {
      this.setData({
        scroll_top: scroll_top
      })
    }
  },

  /**
   * 弹框
   */
  showBox: function (e) {
    let message = app.checkEmpty(e.detail.message, '');
    let times = app.checkEmpty(e.detail.times, 1500);
    app.showBox(this, message, times);
  }
})