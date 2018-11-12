const config = require('../../config.js');
const app = getApp();
const signature = require('../../common/signature.js');
Page({

  /**
   * 页面的初始数据
   */
  data: {
    display: false,
    userInfo: {},
    count:0
  },
  /**
   * 生命周期函数--监听页面显示
   */
  onLoad: function () {
    if (signature.mo_login(app)) {
      this.setData({ display: true, userInfo: app.globalData.userInfo })
      
    }
    this.getCouponCount();
  },
  /**
   * 获取可用优惠券总数
   */
  getCouponCount:function(){
    wx.request({
      url: config.couponUrl,
      data:{
        wid: wx.getStorageSync('wid') || app.globalData.account_id,
        token:config.token,
        type:'queryCount'
      },
      success:(res)=>{
        if(res.data.code == 0){
          let count=parseInt(res.data.count);
          this.setData({count:count});
        }
      }
    })
  }
})