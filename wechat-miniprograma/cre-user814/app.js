const config = require('./config.js');
App({

  /**
   * 当小程序初始化完成时，会触发 onLaunch（全局只触发一次）
   */
  onLaunch: function () {
    wx.login({
      success: res => {
        let code = res.code;
        this.globalData.code = code;
        wx.getUserInfo({
          lang: 'zh_CN',
          success: res => {
            this.globalData.hasLogin = true;
            this.globalData.userInfo = res.userInfo;
            //用户授权成功，发起网络请求登录
            this.userLogin(code, res.userInfo.nickname);
          },
          //用户授权失败，引导用户主动开启授权
          fail: (res) => {
            this.userLogin(code, '神秘的我');
          }
        })
      }
    })
  },

  /**
  * 小程序登录
  */
  login: function () {
    wx.login({
      success: res => {
        let code = res.code;
        this.globalData.code = code;
        wx.getUserInfo({
          lang: 'zh_CN',
          success: res => {
            this.globalData.hasLogin = true;
            this.globalData.userInfo = res.userInfo;
            //用户授权成功，发起网络请求登录
            this.userLogin(code,res.userInfo.nickname);
          },
          //用户授权失败，引导用户主动开启授权
          fail: (res) => {
            this.userLogin(code,'神秘的我');
          }
        })
      }
    })
  },

  /**
   * 用户登录
   */
  userLogin: function (code,nickname) {
    wx.request({
      url: config.loginUrl,
      data: {
        wid: -1,
        code: code,
        user: nickname,
        token: config.token
      },
      success: res => {
        if (res.data.code == 0) {
          let wid = res.data.wid;
          this.globalData.account_id = wid;
          try{
            wx.setStorageSync('wid', res.data.wid);
          }catch(e){
            wx.setStorageSync('wid', res.data.wid);
          }
          if(res.data.couponid == 0){
            wx.showModal({
              title: '克瑞米艾送您红包',
              content: res.data.desc,
              cancelText:'任性不要',
              cancelColor:'red',
              confirmText:'立即领取',
              success:(res)=>{
                if(res.confirm){
                   wx.request({
                     url: config.sendCouponUrl,
                     data:{
                       wid:wid,
                       token:config.token
                     },
                     success:(res)=>{
                        //do something
                     }
                   })
                }else if(res.cancle){
                   //do something
                }
              }
            })
          }
        }
      }
    })
  },
  /**
   * 设置全局数据以供使用
   */
  globalData: {
    hasLogin: false,
    userInfo: null,
    account_id: -1,
    code: '',
    hasMobileBind: true
  }
})

