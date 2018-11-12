var config = require('../../../../config');
var app = getApp();
Page({
  data: {
    coupons: [],
    stats: [],
    bill_total: 0,
    bill_final: 0,
    bill_promo: 0,
    tabs: ["大杯", "中杯", "小杯"],
    activeIndex: 1,
    sliderOffset: 0,
    sliderLeft: 0
  },
  onLoad: function () {
    var that = this;
    wx.getSystemInfo({
      success: function (res) {
        that.setData({
          sliderLeft: (res.windowWidth / that.data.tabs.length - sliderWidth) / 2,
          sliderOffset: res.windowWidth / that.data.tabs.length * that.data.activeIndex
        });
      }
    });
  },
  tabClick: function (e) {
    this.setData({
      sliderOffset: e.currentTarget.offsetLeft,
      activeIndex: e.currentTarget.id
    });
  },
  onLoad: function() {
    this.onPullDownRefresh();
  },
  onPullDownRefresh: function () {
    var that = this;
    var myDate = new Date();
    wx.request({
      url: 'https://' + config.host + '/we/stat.php',
      data: { wid: wx.getStorageSync('jwt').wid, y: 2017, m: 11, sn: '2017090004' },
      header: {
        'content-type': 'application/json'
      },
      success: function (res) {
        that.setData({
          coupons: res.data.coupons,
          stats: res.data.stats,
          year: res.data.year,
          month: res.data.month,
          days: res.data.days,
          bill_total: res.data.bill_total,
          bill_final: (res.data.bill_total * 8 + 1000).toFixed(2),
          bill_promo: 0,
          machine: res.data.machine
        });
      },
    });
  },


  showTopTips: function () {
    var that = this;
    this.setData({
      showTopTips: true
    });
    setTimeout(function () {
      that.setData({
        showTopTips: false
      });
    }, 3000);
  },
  radioChange: function (e) {
    console.log('radio发生change事件，携带value值为：', e.detail.value);

    var that = this;
    this.setData({
      bill_promo: 1000,
      bill_final: (that.data.bill_total * 8 - 1000 + 1000).toFixed(2)
      
    });
    console.log(that.data.bill_total);
  },

  formReset: function (e) {
    console.log('reset coupon');
    var that = this;
    this.setData({
      bill_promo: 0,
      bill_final: (that.data.bill_total * 8 + 1000).toFixed(2)
    });
  },
  
});