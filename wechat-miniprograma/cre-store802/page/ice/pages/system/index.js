const config = require('../../../../config');
const app = getApp();
const signature = require('../../../../util/signature.js');
Page({
  openToast: function() {
    wx.showToast({
      title: '已完成',
      icon: 'success',
      duration: 3000
    });
  },
  openLoading: function() {
    wx.showToast({
      title: '系统操作中',
      icon: 'loading',
      duration: 3000
    });
  },
  /** 
  callinge: function() {
    var that = this;
    wx.scanCode({
      success: (res) => {
        wx.request({
          url: config.host + '/scan.php',
          data: {
            code: res.result,
            wid: wx.getStorageSync('jwt').wid,
            token: config.token
          },
          success: function(res) {
            // 提示信息
            wx.showToast({
              title: res.data.msg,
              icon: 'success',
              duration: 2000
            });
            that.updateData();
          }
        })
      }
    })
  },**/
  data: {
    sweety: 0,
    machines: [],
    status: ["待机", "制冷", "清洗", "保鲜", "解冻", "巴士杀菌", "再生", "重操作", "自动出料", "离线", "未授权"],
    index: 0,
    hasUserInfo: false
  },
  clear: function() {
    this.setData({
      hasUserInfo: false,
      userInfo: {}
    })
  },
  /**
   * 监听页面加载
   */
  onLoad: function(e) {
    if (!signature.checkLogin()) {
      wx.redirectTo({
        url: '/page/ice/pages/login/login',
      })
      return;
    }
    this.updateData();
  },
  onPullDownRefresh: function() {
    this.updateData();
  },
  updateData: function() {
    var that = this;
    wx.request({
      url: config.machineUrl,
      data: {
        wid: wx.getStorageSync('jwt').wid,
        token: config.token
      },
      success: function(res) {
        //res.data.machines.shift();
        that.setData({
          machines: res.data.machines
        });
      },
      complete: function() {
        wx.hideNavigationBarLoading();        
        wx.stopPullDownRefresh();      
      }
    });
  },
})