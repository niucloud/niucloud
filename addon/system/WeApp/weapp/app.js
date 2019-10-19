var md5 = require('/utils/md5.js');

App({

  /**
   * 全局变量
   */
  globalData: {
    site_base_url: "{{url}}", //服务器url
    site_id: '{{site_id}}', //站点ID
    app_key: '{{app_key}}', //私钥 与后台基础设置中API安全秘钥相同，谨慎修改
    base_copy_right: {
      is_load: 1, //是否加载版权信息 (请求失败后加载此配置)
      default_logo: '/images/index/logo_copy.png', //版权LOGO图 (请求失败后加载此配置)
      technical_support: '山西牛酷信息科技有限公司', //版权技术支持 (请求失败后加载此配置)
    },
    get_user_ip: 1, // 是否允许获取用户IP（订单支付使用）,需添加 pv.sohu.com 为 request合法域名
    copy_right: {
      is_load: -1 // (无需填写)
    },
    title: '{{site_title}}', //title (请求失败后使用的title, 请求成功则使用后台配置title)
    wx_info: '{}', //用户信息 (无需填写)
    session_key: '', //小程序参数 (无需填写)
    openid: '', //小程序用户唯一标识 (无需填写)
    token: '', //用户标识 (无需填写)
    sourceid: '', //推广ID (无需填写)
    is_login: 0, //是否登录 (无需填写)
    is_logout: 0, //是否退出登录 (无需填写)
    is_first_bind: 0, //是否第一次绑定会员 (无需填写)
    base_default_img: {
      is_use: 1, // (无需填写)
      value: {
        default_goods_img: '/images/common/default_goods.png', //默认商品图
        default_headimg: '/images/common/default_headimg.png', // 默认用户头像
      }
    },
    default_img: {
      is_use: -1
    }, //是否使用默认图 (无需填写),
    addon_is_exit: {
      is_use: -1,
      is_exit_fx: 0,
      is_exit_pintuan: 0,
      is_exit_bargain: 0,
      is_exit_presell: 0,
    }, //基础配置 (无需填写)
    web_site_info: {}, //基础配置 (无需填写)
    has_diy_view: {}, // (无需填写)
    tab_parm: '', //订单返回参数 (无需填写)
    tab_type: '', //订单返回类型 (无需填写)
    login_count: 0, //登录次数 (无需填写)
    is_login_request: 0, //是否正在进行登录/注册请求 (无需填写)
    is_yet_login: 0, //是否已经登录 (无需填写)
    current_address: '', //保存当前地址 (无需填写)
  },

  //app初始化函数
  onLaunch: function() {
    let that = this;

    const updateManager = wx.getUpdateManager()
    updateManager.onCheckForUpdate(function(res) {
      // 请求完新版本信息的回调
      //console.log(res.hasUpdate)
    })

    updateManager.onUpdateReady(function() {
      wx.showModal({
        title: '更新提示',
        content: '新版本已经准备好，是否重启应用？',
        success: function(res) {
          if (res.confirm) {
            // 新的版本已经下载好，调用 applyUpdate 应用新版本并重启
            updateManager.applyUpdate()
          }
        }
      })

    })
    updateManager.onUpdateFailed(function() {
      // 新的版本下载失败
      wx.showModal({
        title: '更新提示',
        content: '新版本下载失败',
        showCancel: false
      })
    })

    //that.baseLogin();
  },

  onReady() {},

  onShow() {},

  onHide() {},

  onError(msg) {},

  /**
   * 封装请求函数
   */
  sendRequest: function(param, customSiteUrl) {
    let that = this;
    let site_id = that.globalData.site_id;
    let site_base_url = that.globalData.site_base_url
    let data = param.data || {};
    let header = param.header;
    let requestUrl;

    data.is_applet = 1;
    data.token = that.globalData.token;
    data.app_key = that.globalData.app_key;
    data.site_id = site_id;

    if (param.method == '' || param.method == undefined) {
      param.method = 'POST';
    }
    if (customSiteUrl) {
      requestUrl = customSiteUrl + param.url;
    } else {
      requestUrl = site_base_url + site_id + '?s=/api/index/get/method/' + param.url + '/version/1.0';
    }

    if (param.method) {
      if (param.method.toLowerCase() == 'post') {
        header = header || {
          'content-type': 'application/x-www-form-urlencoded;'
        }
      } else {
        if (customSiteUrl) {
          data = this._modifyPostParam(data);
        } else {
          data = {
            param: JSON.stringify(data),
            method: param.url
          }
        }
      }
      param.method = param.method.toUpperCase();
    }

    wx.request({
      url: requestUrl,
      data: data,
      method: param.method || 'GET',
      header: header || {
        'content-type': 'application/json'
      },
      success: function(res) {
        //请求失败
        if (res.statusCode && res.statusCode != 200) {
          that.hideToast();
          that.showModal({
            content: '系统繁忙，请求超时...',
            url: '/pages/index/index'
          })
          typeof param.successStatusAbnormal == 'function' && param.successStatusAbnormal(res.data);
          return;
        }

        if (!customSiteUrl && typeof res.data == 'string' && JSON.parse(res.data)) {
          res.data = JSON.parse(res.data);
        }
        typeof param.success == 'function' && param.success(res.data);
        let code = res.data.code;
        let message = res.data.message;
        if (code == -9999) {
          //未登录执行isNotLogin(code);
          that.isNotLogin(code);
          //未登录
          wx.showModal({
            title: '提示',
            content: '未登录 !',
            showCancel: false,
            success: function(res) {
              wx.reLaunch({
                url: '/pages/member/member/member',
              })
            }
          });

        } else if (code == -7777) {
          that.showModal({
            content: res.data.title,
          })
        } else if (code == -50) {
          //参数错误|数据异常
          that.showModal({
            content: message,
            url: '/pages/index/index'
          })
        } else if (code == -20) {
          //越权行为
          wx.switchTab({
            url: '/pages/member/member/member',
          })
        } else if (code == -10) {
          //数据异常
          that.showModal({
            content: message,
            code: -10,
          })
        }
        //console.log(res);
      },
      fail: function(res) {
        that.hideToast();
        typeof param.fail == 'function' && param.fail(res.data);
        that.showModal({
          content: '系统繁忙，请求超时...', //错误信息: res.errMsg
          url: '/pages/index/index'
        })
      },
      complete: function(res) {
        param.hideLoading || that.hideToast();
        typeof param.complete == 'function' && param.complete(res.data);
      }
    });
  },

  /**
   * 修改POST参数
   */
  _modifyPostParam: function(obj) {
    let query = '';
    let name, value, fullSubName, subName, subValue, innerObj, i;

    for (name in obj) {
      value = obj[name];

      if (value instanceof Array) {
        for (i = 0; i < value.length; ++i) {
          subValue = value[i];
          fullSubName = name + '[' + i + ']';
          innerObj = {};
          innerObj[fullSubName] = subValue;
          query += this._modifyPostParam(innerObj) + '&';
        }
      } else if (value instanceof Object) {
        for (subName in value) {
          subValue = value[subName];
          fullSubName = name + '[' + subName + ']';
          innerObj = {};
          innerObj[fullSubName] = subValue;
          query += this._modifyPostParam(innerObj) + '&';
        }
      } else if (value !== undefined && value !== null) {
        query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
      }
    }

    return query.length ? query.substr(0, query.length - 1) : query;
  },

  /**
   * 微信提示弹框
   */
  showToast: function(param) {
    wx.showToast({
      title: param.title,
      icon: param.icon,
      duration: param.duration || 1500,
      mask: true,
      success: function(res) {
        typeof param.success == 'function' && param.success(res);
      },
      fail: function(res) {
        typeof param.fail == 'function' && param.fail(res);
      },
      complete: function(res) {
        typeof param.complete == 'function' && param.complete(res);
      }
    })
  },

  /**
   * 隐藏加载提示
   */
  hideToast: function() {
    wx.hideToast();
  },

  /**
   * 模态提示框
   */
  showModal: function(param) {
    let that = this;
    wx.showModal({
      title: param.title || '提示',
      content: param.content,
      showCancel: param.showCancel || false,
      cancelText: param.cancelText || '取消',
      cancelColor: param.cancelColor || '#000000',
      confirmText: param.confirmText || '确定',
      confirmColor: param.confirmColor || '#3CC51F',
      success: function(res) {
        if (res.confirm) {
          typeof param.confirm == 'function' && param.confirm(res);
          let pages = getCurrentPages();
          if (param.url != '' && param.url != undefined) {
            wx.switchTab({
              url: param.url,
            })
          } else if (param.code == -10) {
            wx.navigateBack({
              delta: 1
            })
          }

        } else {
          typeof param.cancel == 'function' && param.cancel(res);
        }
      },
      fail: function(res) {
        typeof param.fail == 'function' && param.fail(res);
      },
      complete: function(res) {
        typeof param.complete == 'function' && param.complete(res);
      }
    })
  },

  getsite_base_url: function() {
    return this.globalData.site_base_url;
  },

  getSessionKey: function() {
    return this.globalData.session_key;
  },

  setSessionKey: function(session_key) {
    this.globalData.session_key = session_key;
  },

  setOpenid: function(openid) {
    this.globalData.openid = openid;
  },

  setUnionId: function(unionid) {
    let wx_info = JSON.parse(this.globalData.wx_info);
    wx_info.unionid = unionid;
    this.globalData.wx_info = JSON.stringify(wx_info);
  },

  setWxInfo: function(wx_info) {
    let default_wx_info = JSON.parse(wx_info);
    default_wx_info.unionid = default_wx_info.unionId == undefined ? '' : default_wx_info.unionId;
    this.globalData.wx_info = JSON.stringify(default_wx_info);
  },

  setToken: function(token) {
    this.globalData.token = token;
    if (token != '' && token != undefined) {
      this.globalData.is_login = 1;
    }
  },

  setTabParam: function(tab_parm) {
    this.globalData.tab_parm = tab_parm;
  },

  setTabType: function(tab_type) {
    this.globalData.tab_type = tab_type;
  },

  setCopyRight: function(copy_right) {
    this.globalData.copy_right = copy_right;
  },

  setLoginRequest: function(is_login_request) {
    this.globalData.is_login_request = is_login_request;
  },

  setSourceId: function(sourceid) {
    this.globalData.sourceid = sourceid;
  },

  /**
   * 界面弹框
   */
  showBox: function(that, content, time = 1500) {
    setTimeout(function callBack() {
      that.setData({
        prompt: content
      });
    }, 200)
    setTimeout(function callBack() {
      that.setData({
        prompt: ''
      });
    }, time + 200)
  },

  /**
   * 登录
   */
  baseLogin: function() {
    let that = this;

    //退出检测
    let is_logout = that.globalData.is_logout;
    if (is_logout == 1) {
      that.wechatLogin();
      return;
    }

    wx.login({
      success: function(res) {
        that.sendRequest({
          url: "System.Login.getWechatBasicInfo",
          data: {
            code: res.code
          },
          success: function(wechat_res) {
            let code = wechat_res.code;
            if (code == 0) {
              let wx_info = JSON.parse(wechat_res.data);
              if (wx_info.openid == '' || wx_info.openid == undefined || wx_info.openid == null) {
                that.showModal({
                  content: '小程序配置错误: ' + res.errMsg, //错误信息: res.errMsg
                })
              }
              that.setSessionKey(wx_info.session_key);
              that.setOpenid(wx_info.openid);
              if (wx_info.unionid != undefined && wx_info.unionid != '') {
                that.setUnionId(wx_info.unionid);
              }
              that.getUserSetting();
            }
          }
        });
      }
    });
  },

  /**
   * 是否授权
   */
  getUserSetting: function() {
    let that = this;
    wx.getSetting({
      success: function(res) {
        if (res.authSetting['scope.userInfo']) {
          wx.getUserInfo({
            success: function(user_info) {
              if (user_info) {
                that.login(user_info.encryptedData, user_info.iv);
              }
            }
          })
        }
      }
    });
  },

  login: function(encryptedData, iv) {
    let that = this;
    let sessionKey = that.getSessionKey();
    that.sendRequest({
      url: "System.Login.getWechatParticularInfo",
      data: {
        sessionKey: sessionKey,
        encryptedData: encryptedData,
        iv: iv
      },
      success: function(res) {

        let data = res.data;
        let code = data.code;
        if (code == 0) {
          if (data.data) {
            that.setWxInfo(data.data);
            that.wechatLogin();
          }
        }
      }
    });
  },

  wechatLogin: function() {
    let that = this;
    let openid = that.globalData.openid;
    let wx_info = that.globalData.wx_info;
    let sourceid = that.globalData.sourceid;
    //防止重复请求登录/注册
    let is_login_request = that.globalData.is_login_request;
    if (is_login_request == 1) {
      return false;
    }

    that.setLoginRequest(1);
    that.sendRequest({
      url: "System.Login.wechatLogin",
      data: {
        openid: openid,
        wx_info: wx_info,
        sourceid: sourceid
      },
      success: function(res) {
        let code = res.code;
        //console.log(res);
        if (code == 0 || code == 10) {

          //登录/注册成功
          that.setToken(res.data.token);
          that.globalData.is_logout = 0;
          that.isNotLogin(1);
          that.setSourceId('');
        } else if (code == 20) {

          //微信非自动注册,进行强制会员绑定
          that.globalData.is_first_bind = 1;
          //获取绑定信息
          let info = JSON.parse(wx_info);
          //替换用户头像object_name, 昵称object_name
          info.headimgurl = info.avatarUrl;
          info.nickname = info.nickName;
          delete info.avatarUrl;
          delete info.nickName;
          wx_info = JSON.stringify(info);

          let bind_message_info = {
            is_bind: 1,
            info: wx_info,
            token: {
              openid: openid
            },
          };
          if (info.unionid != undefined && info.unionid != null && info.unionid != '') {
            bind_message_info.wx_unionid = info.unionid;
            bind_message_info.token.openid = '';
          }

          that.globalData.bind_message_info = JSON.stringify(bind_message_info);
        }
      }
    });
  },

  /**
   * 图片路径处理
   */
  IMG: function(img) {
    let base = this.globalData.site_base_url;
    img = img == undefined ? '' : img;
    img = img == 0 ? '' : img;

    if (img.indexOf('http://') == -1 && img.indexOf('https://') == -1 && img != '') {
      if (img.indexOf('static/') == 0) {
        img = base + 'public/' + img;
      } else {
        img = base + 'attachment/' + img;
      }
    }
    return img;
  },

  /**
   * 视频路径处理
   */
  VIDEO: function(video) {
    let base = this.globalData.site_base_url;
    video = video == undefined ? '' : video;
    video = video == 0 ? '' : video;

    if (video.indexOf('http://') == -1 && video.indexOf('https://') == -1 && video != '') {
      if (img.indexOf('static/') == 0) {
        video = base + 'public/' + video;
      } else {
        video = base + 'attachment/' + video;
      }
    }
    return video;
  },

  /**
   * 获取屏幕宽高
   */
  getWindowSize: function(obj = null, set_size = false, set_swiper = false, img_url = '', param_size_str = '', param_swiper_str = '') {
    let that = this;
    let mobile_width = this.globalData.mobile_width;
    let mobile_height = this.globalData.mobile_height;

    if (mobile_width == undefined || mobile_height == undefined) {
      //获取屏幕宽高
      mobile_width = 0;
      mobile_height = 0;
      wx.getSystemInfo({
        success: function(res) {
          mobile_width = res.windowWidth;
          mobile_height = res.windowHeight;
          that.globalData.mobile_width = mobile_width;
          that.globalData.mobile_height = mobile_height;

          if (obj != null) {
            obj.setData({
              mobile_width: mobile_width,
              mobile_height: mobile_height
            })

            if ((set_size || set_swiper) && img_url != '') {
              that.setSwiperHeight(obj, set_size, set_swiper, img_url, param_size_str, param_swiper_str);
            }
          }
        },
      })
    } else {
      if (obj != null) {

        obj.setData({
          mobile_width: mobile_width,
          mobile_height: mobile_height
        })

        if ((set_size || set_swiper) && img_url != '') {
          that.setSwiperHeight(obj, set_size, set_swiper, img_url, param_size_str, param_swiper_str);
        }
      }
    }
  },

  /**
   * 根据图片高度获取轮播区块高度
   */
  setSwiperHeight: function(obj, set_size, set_swiper, img_url, param_size_str, param_swiper_str) {
    let mobile_width = this.globalData.mobile_width;
    if (mobile_width > 0 && mobile_width != undefined) {
      wx.getImageInfo({
        src: img_url,
        success: function(res) {
          if (res.width > 0 && res.width != undefined) {

            let img_width = res.width;
            let img_height = res.height;
            let rate = mobile_width / img_width;
            let height = img_height * rate;
            let param = {};
            // 图片尺寸
            if (set_size && param_size_str != '') {
              param[param_size_str] = {
                width: img_width,
                height: img_height
              }
            }
            // 自适应后的高度
            if (set_swiper && param_swiper_str != '') {
              param[param_swiper_str] = height
            }

            if (set_size != '' || set_swiper) {
              obj.setData(param);
            }
          } else {
            console.log('图片信息获取失败.')
          }
        },
        fail: function(e) {
          console.log('图片信息获取失败: ' + img_url);
          console.log(e);
        }
      })
    } else {
      console.log('屏幕宽度获取失败.')
    }
  },

  /**
   * 商品、用户头像默认图
   * obj 调用对象
   * load_info 是否调用load_info回调方法
   * load_info_param load_info回调方法参数
   */
  defaultImg: function(obj = null, load_info = false, load_info_param = null) {
    let that = this;
    let default_img = that.globalData.default_img;

    if (default_img.is_use == -1) {
      that.sendRequest({
        url: "System.Config.defaultImages",
        data: {},
        success: function(res) {
          let code = res.code;
          let data = res.data;
          if (code == 0) {
            default_img = data;
            default_img.value.default_goods_img = that.IMG(default_img.value.default_goods_img); //默认商品图处理
            default_img.value.default_headimg = that.IMG(default_img.value.default_headimg); //默认用户头像处理
          } else {
            default_img = that.globalData.base_default_img;
          }
          that.globalData.default_img = default_img;
          if (obj != null) {
            obj.setData({
              default_img: default_img
            })
            that.getCurrentTime(obj, load_info, load_info_param);
          }
        }
      });
    } else {
      if (obj != null) {
        obj.setData({
          default_img: default_img
        })
        that.getCurrentTime(obj, load_info, load_info_param);
      }
    }
  },

  /**
   * 获取当前时间
   */
  getCurrentTime: function(obj = null, load_info = false, load_info_param = null) {
    let that = this;

    that.sendRequest({
      url: "System.Config.getCurrentTime",
      data: {},
      success: function(res) {
        let code = res.code;
        let data = res.data;
        if (code == 0) {
          let current_time = data.current_time;
          if (obj != null) {
            obj.setData({
              current_time: current_time
            })
            if (load_info) {
              if (load_info_param != null) {
                obj.loadInfo(load_info_param);
              } else {
                obj.loadInfo();
              }
            }
          }
        }
      }
    });
  },

  /**
   * 获取当前用户uid
   */
  getMemberId: function(obj = null, callback = '', param = null) {
    let that = this;
    let token = that.globalData.token;
    let uid = that.globalData.uid;
    uid = that.checkEmpty(uid, 0);
    token = that.checkEmpty(token, '');

    if (uid == 0 && token != '') {
      that.sendRequest({
        url: 'System.Member.memberInfo',
        success: function(res) {
          if (res.code == 0) {
            let member_info = that.checkEmpty(res.data, {});
            let user_info = that.checkEmpty(member_info.user_info, {});
            uid = that.checkEmpty(user_info.uid, 0);
            that.globalData.uid = uid;

            if (that.checkEmpty(obj, null) != null) {
              obj.setData({
                uid: uid
              })
              if (that.checkEmpty(callback, '') != '') {
                if (that.checkEmpty(param, null) != null) {
                  obj[callback](param);
                } else {
                  obj[callback]();
                }
              }
            }
          }
        }
      })
    } else {
      if (that.checkEmpty(obj, null) != null) {
        obj.setData({
          uid: uid
        })
        if (that.checkEmpty(callback, '') != '') {
          if (that.checkEmpty(param, null) != null) {
            obj[callback](param);
          } else {
            obj[callback]();
          }
        }
      }
    }
  },

  /**
   * 基础配置
   */
  webSiteInfo: function(obj = null, set_title = false) {
    let that = this;
    let web_site_info = that.globalData.web_site_info;

    if (web_site_info.website_id == undefined) {
      that.sendRequest({
        url: "System.Config.webSite",
        data: {},
        success: function(res) {
          let code = res.code;
          let data = res.data;

          if (code == 0) {
            that.globalData.web_site_info = data;

            if (set_title && data != undefined && data.title != undefined && data.title != '') {
              that.globalData.title = data.title;
              wx.setNavigationBarTitle({
                title: data.title,
              })
            }
            if (obj != null) {
              obj.setData({
                web_site_info: web_site_info
              })
            }
          } else {
            let title = that.globalData.title;
            wx.setNavigationBarTitle({
              title: title,
            })
          }
          //console.log(res);
        }
      })
    } else {
      if (obj != null) {
        obj.setData({
          web_site_info: web_site_info
        })
      }
      if (set_title && web_site_info.title != undefined && web_site_info.title != '') {
        wx.setNavigationBarTitle({
          title: web_site_info.title,
        })
      }
    }
  },

  /**
   * 版权加载
   */
  copyRightLoad: function(obj = null) {
    let that = this;
    let copy_right = that.globalData.copy_right;
    // 已加载版权信息无需重复加载
    if (copy_right.is_load == -1) {
      that.sendRequest({
        url: "System.Config.copyRight",
        data: {},
        success: function(res) {
          if (res.code == 0) {
            copy_right = res.data;

            if (copy_right.is_load == 1 && (copy_right.bottom_info.copyright_companyname != '' || copy_right.bottom_info.copyright_logo != '')) {
              let img = copy_right.bottom_info.copyright_logo;
              copy_right.default_logo = that.IMG(img);
              copy_right.technical_support = copy_right.bottom_info.copyright_companyname;
            } else {
              copy_right.default_logo = '/images/index/logo_copy.png';
              copy_right.technical_support = '山西牛酷信息科技有限公司　提供技术支持';
            }
          } else {
            // 版权信息加载失败使用默认版权信息
            copy_right = that.globalData.base_copy_right;
          }
          that.setCopyRight(copy_right);

          if (obj != null) {
            obj.setData({
              copy_right: copy_right
            })
          }
        }
      })
    } else {
      if (obj != null) {
        obj.setData({
          copy_right: copy_right
        })
      }
    }
  },

  /**
   * 插件功能检测
   */
  addonIsExit: function(obj = null, call_back = false, method = '') {
    let that = this;
    let addon_is_exit = that.globalData.addon_is_exit;
    if (addon_is_exit.is_use == -1) {
      that.sendRequest({
        url: "System.Config.addonIsExit",
        data: {},
        success: function(res) {
          let code = res.code;
          let data = res.data;
          if (code == 0) {
            addon_is_exit = that.checkEmpty(data, '');
            if (addon_is_exit != '') {
              addon_is_exit.is_use = 1;
              that.globalData.addon_is_exit = addon_is_exit;
              if (obj != null) {
                obj.setData({
                  addon_is_exit: addon_is_exit
                })
                if (call_back && method == 'loadPromoterInfo') {
                  if (addon_is_exit.is_exit_fx == 1) {
                    obj.loadPromoterInfo();
                  }
                }
              }
            }
          }
        }
      });
    } else {
      if (obj != null) {
        obj.setData({
          addon_is_exit: addon_is_exit
        })
        if (call_back && method == 'loadPromoterInfo') {
          if (addon_is_exit.is_exit_fx == 1) {
            obj.loadPromoterInfo();
          }
        }
      }
    }
  },

  /**
   * 消息通知配置
   */
  noticeConfig: function(obj = null) {
    let that = this;

    that.sendRequest({
      url: "System.Config.noticeConfig",
      success: function(res) {
        let code = res.code;
        let data = res.data;
        if (code == 0) {
          let notice_config = data;

          if (obj != null) {
            obj.setData({
              notice_config: notice_config
            })
          }
        }
      }
    })
  },

  // 微页面检测
  hasDiyView: function(type = '') {
    let that = this;
    let has_diy_view = that.globalData.has_diy_view;
    has_diy_view[type] = that.checkEmpty(has_diy_view[type], {
      is_open: 0,
      is_load: 0
    });
    if (has_diy_view[type].is_load == 0) {
      that.sendRequest({
        url: 'System.Config.defaultDiyViewIsExit',
        data: {
          type: 2,
          template_type: 'index',
        },
        success: function(res) {
          if (res.code == 0) {
            if (res.data == 1) {
              wx.reLaunch({
                url: '/pages/diyview/diyview?template_type=' + type,
              })
            }
            has_diy_view[type] = {
              is_load: 1,
              is_open: res.data,
            }
            that.globalData.has_diy_view = has_diy_view;
          }
        }
      });
    } else {
      if (has_diy_view[type].is_open == 1) {
        wx.reLaunch({
          url: '/pages/diyview/diyview?template_type=' + type,
        })
      }
    }
  },

  // 获取当前用户IP
  getUserIp: function(obj = null) {
    let that = this;
    let get_user_ip = that.globalData.get_user_ip;

    that.sendRequest({
      data: {},
      success: function(res) {
        try {
          var user_ip = res.split(' ')[4].replace('"', '').replace('"', '').replace(',', '');
          if (obj != null) {
            obj.setData({
              buyer_ip: user_ip
            })
          }
        } catch (e) {
          console.log('获取用户IP失败： ');
          console.log(e);
        }
      }
    }, 'https://pv.sohu.com/cityjson?ie=utf-8');
  },

  /**
   * 已点击
   */
  clicked: function(that, parm) {
    let d = {};
    d[parm] = 1;
    that.setData(d);
  },

  /**
   * 状态重置
   */
  resetStatus: function(that, parm) {
    let d = {};
    d[parm] = 0;
    that.setData(d);
  },

  /**
   * 清理计时器
   */
  clearTimer: function(obj) {
    try {
      let timer_array = obj.data.timer_array;
      for (let index in timer_array) {
        if (timer_array[index].timer != null && timer_array[index].timer != undefined) {
          let timer = timer_array[index].timer;
          clearInterval(timer);
        }
      }
      obj.setData({
        timer_array: []
      })
    } catch (e) {
      console.log(e);
    }
  },

  /**
   * 获取验证码
   */
  verificationCode: function(obj) {
    let key = this.globalData.openid;
    this.sendRequest({
      url: 'System.Config.getVertification',
      data: {
        key: key
      },
      success: function(res) {
        if (res.code == 0) {
          obj.setData({
            code: ' data:image/png;base64,' + res.data
          })
        }
      }
    });
  },

  /**
   * 验证码验证
   */
  checkVerificationCode: function(obj, code, commit_type = 'commit', reset_falg = '') {
    let that = this;
    let key = that.globalData.openid;
    this.sendRequest({
      url: 'System.Login.checkVertification',
      data: {
        key: key,
        code: code
      },
      success: function(res) {
        if (res.code == 0) {
          if (res.data.code == 0) {
            if (commit_type == 'commit') {
              obj.commit();
            } else if (commit_type == 'getOutCode') {
              obj.getOutCode();
            } else if (commit_type == 'checkOutCode') {
              obj.checkOutCode();
            }
            that.restStatus(obj, reset_falg);
          } else {
            that.showBox(obj, '验证码不一致');
            that.verificationCode(obj);
            that.restStatus(obj, reset_falg);
          }
        }
      }
    });
  },

  /**
   * 检测手机号是否存在
   */
  checkHasMobile: function(obj, mobile, action_type = '', has_flag = false, reset_falg = '', commit_type = '', verify_code = '') {
    let that = this;

    that.sendRequest({
      url: "System.Member.checkMobile",
      data: {
        mobile: mobile,
      },
      success: function(res) {
        let code = res.code;
        let data = res.data;

        if (code == 0) {
          let message = has_flag ? '该手机号已被注册' : '当前手机号还未注册';
          if (data === has_flag) {
            that.showBox(obj, message);
            that.restStatus(obj, reset_falg);
          } else {
            if (action_type == 'checkVerificationCode') {
              that.checkVerificationCode(obj, verify_code, commit_type, reset_falg);
            } else if (action_type == 'getOutCode') {
              obj.getOutCode();
            }
          }
        } else {
          that.restStatus(obj, reset_falg);
        }
      }
    });
  },

  /**
   * 动态码验证
   */
  checkOutCode: function(obj, code, type = 'mobile', account = '', reset_falg = '') {
    let that = this;
    let key = that.globalData.openid;
    url = type == 'mobile' ? 'System.Login.checkRegisterMobileCode' : 'checkRegisterEmailCode';
    let param = {
      key: key,
      code: code
    };
    if (type == 'mobile') {
      param.send_mobile = account;
    } else {
      param.send_email = account;
    }

    this.sendRequest({
      url: url,
      data: param,
      success: function(res) {
        if (res.code == 0) {
          if (res.data.code == 0) {
            obj.commit();
          } else {
            that.showBox(obj, res.data.message);
            that.restStatus(obj, reset_falg);
          }
        }
      }
    });
  },

  /**
   * 退出登录
   */
  logout: function(e) {
    let that = this;
    that.globalData.is_logout = 1;
    that.globalData.is_login = 0;
    that.globalData.token = '';
    that.setLoginRequest(0);
    that.globalData.current_address = '';
    that.globalData.is_yet_login = 0;
    wx.reLaunch({
      url: '/pagesother/pages/login/login/login',
    })
  },

  isNotLogin: function(code) {
    var util = require("/utils/util.js");
    let that = this;

    if (code == 1) {

      let current_address = that.globalData.current_address;

      if (current_address == '' || that.globalData.is_yet_login == 1) {
        return false;
      }
      that.globalData.is_yet_login = 1;
      if (current_address == '/pages/goods/cart/cart' || current_address == '/pages/member/member/member') {
        wx.switchTab({
          url: current_address
        })
      } else {
        wx.navigateTo({
          url: current_address,
        })
      }
    }
    if (code == '-9999') {
      //获取当前页
      let url_param = util.getCurrentPageUrlWithArgs();
      that.globalData.current_address = '/' + url_param;
      that.globalData.is_yet_login = 0;
    }
  },

  /**
   * 数组数据检测
   */
  checkArray: function(obj) {
    if (obj != undefined && obj != null && obj[0] != undefined) {
      return true;
    } else {
      return false;
    }
  },

  /**
   * 变量检测
   */
  checkEmpty: function(param, empty) {
    if (param == undefined || param == null || param == 0 || param == 'null' || (typeof param == 'string' && param.replace(/(^\s*)|(\s*$)/g, '') == '')) {
      return empty;
    } else {
      return param;
    }
  },

  /**
   * 获取验证正则 (仅支持手机号、邮箱、是否包含中文验证)
   */
  getRegex: function(param, type) {
    if (param == null || param == undefined || typeof param != 'string') {
      return false;
    }
    let regex = '';
    if (type == 'mobile') {
      regex = /^1([38][0-9]|4[579]|5[0-3,5-9]|6[6]|7[0135678]|9[89])\d{8}$/;

    } else if (type == 'email') {
      regex = /^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/;

    } else if (type == 'chinese_characters') {
      regex = /.*[\u4e00-\u9fa5]+.*$/;

    } else if (type == 'empty') {
      regex = /[\S]+/;
    }
    try {
      return param.search(regex) == -1 ? false : 1;
    } catch (e) {
      console.log('正则验证异常！');
      console.log(e);
      return false;
    }
  },

  /**
   * 跳转链接识别
   */
  linkJumpDetection: function(url) {
    if (this.checkEmpty(url, '') != '') {
      if (url.indexOf('http://') == 0 || url.indexOf('https://') == 0) {
        wx.navigateTo({
          url: '/pages/webpage/webpage?web_url=' + url,
        })
      } else if (url == '/pages/index/index' || url == '/pages/goods/goodsclassificationlist/goodsclassificationlist' || url == '/pages/goods/cart/cart' || url == '/pages/member/member/member') {
        wx.switchTab({
          url: url,
        })
      } else if (url.indexOf('/pages') == 0) {
        wx.navigateTo({
          url: url,
        })
      }
    }
  },
})