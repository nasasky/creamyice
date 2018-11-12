const app = getApp();
const signature = require('../../common/signature.js');
const config = require('../../config.js');
Page({

  /**
   * 页面的初始数据
   */
  data: {
    //支付成功取单号
    or_number: [],
    orid: 0,
    display: false,
    modalHidden: false,
    boxHidden: false,
    nameCheck: false,
  },

  modal: {
    "username": "",
    "password": ""
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    if (!options.type) {
      wx.hideShareMenu()
    }
    if (options.type) {
      this.setData({ display: true })
    }
    wx.request({
      url: config.ordersUrl,
      data: {
        wid: wx.getStorageSync('wid') || app.globalData.account_id,
        token: config.token,
        orid: options.orid,
        type: 'youzhi'
      },
      success: (res) => {
        if (res.data.code == 0) {
          this.setData({
            or_number: res.data.or_number,
            orid: options.orid,
            display: options.type ? true : false
          })
        }
      }
    })
  },
  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function (options) {
    let that = this;
    if (options.from === 'button') {
      console.log(options.target)
    }
    return {
      title: '克瑞米艾冰淇淋 送您红包啦！',
      path: '/page/feature/feature?type=share&fromid=' + wx.getStorageSync('wid') + '&identi=' + that.data.orid,
      imageUrl: 'https://partner.creamyice.com/we/img/share.jpg',
      success: (res) => {
        console.log('分享成功')
        wx.hideShareMenu()
        this.setData({ display: false })
      },
      fail: (res) => {
        console.log('分享失败')
        wx.hideShareMenu()
        this.setData({ display: false })
      }
    }
  },
  cancleShare: function () {
    wx.hideShareMenu()
    this.setData({ display: false })
  }
})
