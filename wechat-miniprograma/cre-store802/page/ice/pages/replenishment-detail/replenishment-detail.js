var config = require('../../../../config');
var app = getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    array:[]
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (e) {
    var that =this;
    wx.request({
      url:  config.replenishmentUrl + '?wid=' + wx.getStorageSync('jwt').wid+'&token='+config.token,
      data: {
        wid: app.globalData.account_id, purchase_id:e.id
      },
      header: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      method: 'POST',
      success: function (res){
        
         that.setData({
            array:res.data.lists
         });
      }
    })
  }
})