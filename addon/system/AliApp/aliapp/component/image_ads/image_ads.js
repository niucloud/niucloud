const app = new getApp();

Component({
  /**
   * 组件的属性列表
   */
  properties: {
    config: Object,
    index: Number
  },

  /**
   * 组件的初始数据
   */
  data: {
    is_loading: 0, //是否已加载数据
    config_info: {}
  },

  /**
   * 数据监听
   */
  observers: {
    'config': function(config) {
      let is_loading = this.data.is_loading;
      if (is_loading == 1) {
        return false;
      }
      // 图片广告处理
      let img_url = '';
      let img_arr = app.checkEmpty(config.list, []);
      // 图片路径处理
      for (let key in img_arr) {
        img_arr[key].imageUrl = app.checkEmpty(img_arr[key].imageUrl, '');
        img_arr[key].imageUrl = app.IMG(img_arr[key].imageUrl);

        // 轮播图高度初始化
        if (key == 0 && img_arr[key].imageUrl != '') {
          config.swiper_height = 150;
          img_url = img_arr[key].imageUrl;
        }
      }
      config.list = img_arr;
      this.setData({
        config_info: config,
        is_loading: 1
      })
      // Swiper高度自适应
      app.getWindowSize(this, false, true, img_url, '', 'config_info.swiper_height');
    }
  },

  /**
   * 组件的方法列表
   */
  methods: {
    // 链接跳转
    linkJump: function(e) {
      let url = e.currentTarget.dataset.url;
      app.linkJumpDetection(url);
    }
  }
})