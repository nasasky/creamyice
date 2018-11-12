const config = require("../../../../config");
const app = getApp();
const signature = require('../../../../util/signature.js');
Page({

  /**
   * 页面的初始数据
   */
  data: {
    bill: [],
    bill_total: 0,
    machine: {},
    bill_amount: 0,
    month: 5,
    year: 2018
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (e) {
    var that = this;
    wx.request({
      url: config.billDetailUrl,
      data: {
        wid: wx.getStorageSync('jwt').wid,
        sn: e.sn,
        year: e.year,
        month: e.month,
        token: config.token
      },
      success: function (res) {
        if (res.data.machine === undefined || res.data.machine.length == 0) {
          res.data.machine = false;
        }
        that.setData({
          bill: res.data.bill,
          bill_total: res.data.bill_total,
          machine: res.data.machine,
          bill_amount: res.data.bill_amount,
          year: e.year,
          month: e.month
        });
      }
    })
  }
})