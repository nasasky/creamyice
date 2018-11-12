const config = require('../../../../config');
const signature =require('../../../../util/signature.js');
const app =getApp()
Page({
  data:{
    userInfo: { avatarUrl: '/page/ice/resources/touxiang.png',nickName:'神秘的我' }
  },
  onLoad: function () {
    if (!signature.checkLogin()) {
      wx.redirectTo({
        url: '/page/ice/pages/login/login',
      })
      return;
    } 
    this.setData({ userInfo: app.globalData.userInfo || { avatarUrl: '/page/ice/resources/touxiang.png', nickName: '神秘的我' },machine_name:app.globalData.machine_name,wid:wx.getStorageSync('jwt').wid })
  },
  /**
   * 登出事件
   */
  logout:function(){
    wx.showModal({
      title: '退出登录',
      content: '你确定退出吗？',
      success: (res) => {
        //商户点击确定发起网络请求
        if (res.confirm) {
          wx.removeStorageSync('jwt')
          wx.redirectTo({
            url: '/page/ice/pages/login/login',
          })
        } else {
          console.log(res)
        }
      }
    })
  },
  onShow:function(){
    if(!signature.checkLogin()){
       wx.redirectTo({
         url: '/page/ice/pages/login/login',
       })
       return;
    }
  },
  openAlert: function () {
    wx.showModal({
      content: '温馨提示：商户无需申请提现，每月底克瑞米艾会联系你核对账单信息无误后，会在1-3个工作日自动汇款',
      showCancel: false,
      success: function (res) {
        if (res.confirm) {
          console.log('用户点击确定')
        }
      }
    });
  }
});



