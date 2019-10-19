const app = getApp();

Page({
  data: {
    
  },
  
  onLoad: function () {
    wx.reLaunch({
      url: '/pages/diyview/diyview?name=NC_ARTICLE_H5_DETAIL',
    })
  },
});