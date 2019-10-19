const app = new getApp();

Component({
  /**
   * 组件的属性列表
   */
  properties: {
    config: Object,
    index: Number,
  },

  /**
   * 组件的初始数据
   */
  data: {
    config_info: {},
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
      this.setData({
        config_info: config,
        is_loading: 1
      })
    },
  },

  /**
   * 组件的方法列表
   */
  methods: {
    // 链接跳转
    linkJump: function(e) {
      let url = e.currentTarget.dataset.url;
      app.linkJumpDetection(url);
    },
  }
})