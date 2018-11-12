var config = require('../../../../config');
var app = getApp();

Page({
  data: {
    coupons: [],
  },
  onLoad: function() {
    this.onPullDownRefresh();
  },
  onPullDownRefresh: function () {
    var that = this;
    var myDate = new Date();
    wx.request({
      url: 'https://' + config.host + '/we/coupon.php',
      data: { wid: wx.getStorageSync('jwt').wid},
      header: {
        'content-type': 'application/json'
      },
      success: function (res) {
        that.setData({
          coupons: res.data.coupons,
        });
        //console.log(that.data.coupons);
      },
    });
  },
});