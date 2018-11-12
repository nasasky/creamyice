var config = require('../../../../config');
var app = getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    bill: [],
    machine_names: [],
    machineIndex: 0,
    machines: [],
    hasLoad: true
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var that = this;
    this.setData({hasLoad:false})
    wx.request({
      url: config.fuseUrl,
      data: {
        wid: wx.getStorageSync('jwt').wid,
        token: config.token
      },
      success: function (res) {
        that.setData({
          bill: res.data.bill,
          machine_names: res.data.machine_names,
          machines: res.data.machines,
          hasLoad:true
        })
      }
    })
  },

  /**
   * 获取不同机器账单改变事件
   */
  bindCountryChange: function (e) {
    var that = this;
    this.setData({
      machineIndex: e.detail.value
    })
    var myDate = new Date();
    wx.request({
      url: config.fuseUrl,
      data: {
        sn: that.data.machines[e.detail.value],
        wid: wx.getStorageSync('jwt').wid,
        token: config.token
      },
      success: function (res) {
        that.setData({
          bill: res.data.bill
        });
      }
    });
  }

});