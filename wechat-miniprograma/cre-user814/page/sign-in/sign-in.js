const app = getApp();
const config = require('../../config.js');
Page({
  data: {
    mobile: '',
    code: '', //这是用户输入的验证码
    noteCode: '', //这是服务器发送的短信验证码
    time: 120,
    hasCode: true
  },
  /**
   * 监听页面加载
   */
  onLoad: function () {

  },
  /**
   * 监听页面显示
   */
  onShow: function () {

  },
  /**
   * 获取输入框手机号
   */
  mobileInput: function (e) {
    //验证手机号合法性
    let mobile = parseInt(e.detail.value);
    if (!this.isPhoneAvailable(mobile)) {
      wx.showToast({
        title: '手机号不合法',
        icon: 'success',
        duration: 2000
      })
      return false;
    }
    this.setData({
      mobile: mobile
    })
  },
  /**
   * 手机号码正则验证
   */
  isPhoneAvailable: function (poneInput) {
    var myreg = /^[1][3,4,5,7,8][0-9]{9}$/;
    if (!myreg.test(poneInput)) {
      return false;
    } else {
      return true;
    }
  },
  /**
   * 点击获取验证码事件
   */
  getNoteCode: function () {
    let mobile = this.data.mobile;
    if (!this.isPhoneAvailable(mobile) || !this.data.hasCode) {
      wx.showToast({
        title: '手机号不合法',
      })
      return false;
    } else {
      //手机号合法，发送apiNote请求向用户发送手机验证码
      wx.request({
        url: config.noteUrl,
        data: {
          token: config.token,
          mobile: mobile
        },
        method: 'POST',
        header: {
          'content-type': 'application/x-www-form-urlencoded'
        },
        success: (res) => {
          if (res.data.code == 0) {
            let result = res.data.result;
            if (result.code == 200) {
              this.setData({ noteCode: result.obj, hasCode: false });
              wx.showToast({
                title: '验证码发送成功',
              });
              let timer;
              let time = this.data.time;
              if (time > 0) {
                timer = setInterval(() => {
                  --time;
                  if (time <= 0) {
                    this.setData({ time: 120, hasCode: true });
                    clearInterval(timer);
                  }
                  this.setData({ time: time });
                }, 1000);
              } else {
                clearInterval(timer);
                this.setData({ hasCode: true })
              }
            } else {
              wx.showToast({
                title: '验证码发送失败',
              })
            }
          }
        }
      })
    }
  },
  /**
   * 用户输入验证码事件
   */
  inputCode: function (e) {
    this.setData({ code: e.detail.value });
  },
  openToast: function () {
    wx.showToast({
      title: '已完成',
      icon: 'success',
      duration: 3000
    });
  },
  /**
   * 用户点击登录事件
   */
  openLoading: function () {
    let code = this.data.code;
    let mobile = this.data.mobile;
    let noteCode = this.data.noteCode;
    if (this.data.mobile == '') {
      wx.showToast({
        title: '请输入手机号',
      })
      return false;
    }
    if (code == '') {
      wx.showToast({
        title: '请输入验证码',
      })
      return false;
    }
    if (noteCode == '') {
      return false;
    }
    if (code != noteCode) {
      wx.showToast({
        title: '验证码错误',
      })
      return false;
    }
    //发起网络请求
    wx.request({
      url: config.loginUrl + '?wid=' + app.globalData.account_id + '&token=' + config.token,
      data: {
        token: config.token,
        mobile: mobile,
        wid: app.globalData.account_id
      },
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      method: 'POST',
      success: (res) => {
        if (res.data.code == 0) {
          let mo_user = res.data.mo_user;
          app.globalData.hasMobileBind = true;
          app.globalData.userInfo = mo_user;
          app.globalData.account_id = mo_user.id;
          wx.setStorageSync('wid', mo_user.id);
          wx.switchTab({
            url: '/page/feature/feature',
          })
        }
      }
    })
  }
});