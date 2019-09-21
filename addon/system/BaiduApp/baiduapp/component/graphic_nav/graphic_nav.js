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
    config_info: {},
    is_loading: 0
  },

  /**
   * 数据监听
   */
  observers: {
    'config': function (config) {
      let is_loading = this.data.is_loading;
      if (is_loading == 1) {
        return false;
      }

      let img_size_arr = [];
      let img_arr = app.checkEmpty(config.list, []);
      // 图片路径处理
      for (let key in img_arr) {
        img_arr[key].imageUrl = app.checkEmpty(img_arr[key].imageUrl, '');
        img_arr[key].imageUrl = app.IMG(img_arr[key].imageUrl);

        img_size_arr[key] = {
          str: 'config_info.list[' + key + '].img_size',
          img_url: img_arr[key].imageUrl
        };
      }
      let item_width = 100 / img_arr.length;
      config.list = img_arr;
      config.item_width = item_width;

      this.setData({
        config_info: config,
        is_loading: 1
      })

      // 以图片原始尺寸限制最大高度
      for (let index in img_size_arr) {
        app.getWindowSize(this, true, false, img_size_arr[index].img_url, img_size_arr[index].str);
      }
    }
  },

  /**
   * 组件的方法列表
   */
  methods: {
    // 链接跳转
    linkJump: function (e) {
      let url = e.currentTarget.dataset.url;
      app.linkJumpDetection(url);
    }
  }
})