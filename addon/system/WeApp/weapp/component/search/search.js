const app = new getApp();

Component({
  /**
   * 组件的属性列表
   */
  properties: {
    config: Object,
    index: Number,
    scroll_top: Number
  },

  /**
   * 组件的初始数据
   */
  data: {
    config_info: {
      background_color: 'rgba(0,0,0,.6)',
      left_img_url: null,
      left_link: null,
      right_img_url: null,
      right_link: null,
    },
    has_background_color: false
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
      this.setData({
        config_info: config,
        is_loading: 1
      })
    },
    
    'scroll_top': function (scroll_top) {
      let has_background_color = this.data.has_background_color;
      if (scroll_top == 0) {
        if (has_background_color) {
          this.setData({
            has_background_color: false
          })
        }
      } else {
        if (!has_background_color) {
          this.setData({
            has_background_color: true
          })
        }
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
    },
  }
})
