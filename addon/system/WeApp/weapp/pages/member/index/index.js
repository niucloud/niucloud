const app = getApp();

Page({
  data: {
    
  },
  
  onLoad: function () {
    wx.reLaunch({
      url: '/pages/diyview/diyview?name=DIYVIEW_MEMBER',
    })
  },
})
