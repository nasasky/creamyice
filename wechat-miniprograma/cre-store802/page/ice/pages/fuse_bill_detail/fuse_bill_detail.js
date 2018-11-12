const config = require("../../../../config");
const app =getApp();
const signature =require('../../../../util/signature.js');
Page({

  /**
   * 页面的初始数据
   */
  data: {
    bill:[],
    machine:{},
    weixin:[],
    bill_amount:0,
    bill_total43:0,
    hasLoad:true
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (e) {
    var that=this;
    this.setData({hasLoad:false})
    wx.request({
      url: config.fuseDetailUrl,
      data:{
        wid:wx.getStorageSync('jwt').wid,
        sn:e.sn,
        year:e.year,
        month:e.month,
        token:config.token
      },
      success:function(res){
        if (res.data.machine === undefined || res.data.machine.length == 0) {
          res.data.machine =false;
        }
        that.setData({
          bill:res.data.bill,
          machine:res.data.machine,
          weixin:res.data.weixin,
          bill_amount:res.data.bill_amount,
          bill_total43:res.data.bill_total43,
          year:e.year,
          month:e.month,
          hasLoad:true
        });
      }
    })
  }
})