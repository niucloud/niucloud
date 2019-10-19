const app = new getApp();

Page({

  /**
   * 页面的初始数据
   */
  data: {
    config_info: {
      logo: '',
    },
    username: '',
    password: '',
    login_flag: false
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    let that = this;

    app.copyRightLoad(that);
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {

  },
  // 弹框
  showBox: function (message, times = 1500) {
    this.triggerEvent('showbox', {
      message: message,
      times: times
    }, {})
  },

  // input组件键入数据
  inputValue: function (e) {
    let value = e.detail.value;
    let param = {};
    let key = e.currentTarget.dataset.type;

    if (app.checkEmpty(key) != '') {
      param[key] = value;
      this.setData(param);
    }
  },

  // 清除输入内容
  clear: function (e) {
    let clear_type = e.currentTarget.dataset.type;
    let param = {};
    let key = '';

    if (clear_type == 'username') {
      key = 'username';
    } else {
      key = 'password';
    }

    if (key != '') {
      param[key] = '';
      this.setData(param);
    }
  },

  // 登录
  login: function () {
    let that = this;
    let username = that.data.username;
    let password = that.data.password;
    let login_flag = that.data.login_flag;

    if (login_flag) {
      return false;
    }
    app.clicked(that, 'login_flag');

    if (username == '') {
      that.showBox('账号不能为空');
      app.resetStatus(that, 'login_flag');
      return false;
    }

    if (password == '') {
      that.showBox('密码不能为空');
      app.resetStatus(that, 'login_flag');
      return false;
    }

    app.sendRequest({
      url: 'System.Login.login',
      method: 'get',
      data: {
        username: username,
        password: password
      },
      success: function (res) {
        console.log(res);
        if (res.code == 0) {

        } else {
          that.showBox(res.message);
        }
      }
    })
  }
})