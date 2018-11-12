// page/ice/pages/go-stat/go-stat.js
var config =require("../../../../config");
var app =getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    machine:{},
    weixin:[],
    bill:[],
    bill_amount:0,
    bill_total:0,
    bill_total39:0,
    bill_total42:0,
    bill_total43:0

  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (e) {
     console.log(e);
     var that=this;
     wx.request({
       url: 'https://'+config.host+"/we/stat.php",
       data:{
         wid: wx.getStorageSync('jwt').wid,
         sn:e.sn
       },
       success:function(res){
         console.log(res.data);
         that.setData({
           
         });
       }
     })
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

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
  
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
  
  }
})