const config = require('../../../../config');
const app =getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    showTopTips: false,
    account:'',
    password:'', 
    hidden:true,
    isAgree:true
  },
  /**
   * 监听页面加载
   * 判断缓存里面是否有账号密码
   */
  onLoad:function(){
     let remember =wx.getStorageSync('remember');
     if(remember){
       this.setData({account:remember.account,password:remember.password})
     }
  },
  /**
   * 账号输入事件
   */
  bindAccount:function(e){
    let account =e.detail.value;
    this.setData({account:account})
  },

  /**
   * 密码输入事件
   */
  bindPassword:function(e){
     let password =e.detail.value;
     this.setData({password:password})
  },

  /**
   * 点击登录事件
   */
  showTopTips:function(e){
    let account =this.data.account;
    let password =this.data.password;
    if(!account){
      wx.showToast({
        title: '请输入账号',
      })
      return false;
    }
    if(!password){
      wx.showToast({
        title: '请输入密码',
      })
      return false;
    }
    if(this.data.isAgree){
      wx.setStorageSync('remember', {account:account,password:password})
    }else{
      wx.clearStorageSync('remember')
    }
    this.setData({hidden:false})
    //账号密码都填写时，发起网络请求，验证账号密码是否正确
    wx.request({
      url: config.loginUrl,
      data:{
        wid: -1,
        token:config.token,
        account:account,
        password:'Cre'+password
      },
      success:(res)=>{
        if(res.data.code == 0 || res.data.code == 1){
          this.setData({hidden:true,account:'',password:''})
          app.globalData.account_id = res.data.wid;
          app.globalData.userLogin =true;
          wx.setStorageSync('jwt', { wid: res.data.wid, time: res.data.time, intervalTime: res.data.intervalTime});
          //表示账号密码匹配，用户登陆成功
          wx.switchTab({
            url: '/page/ice/index',
          })
        }else if(res.data.code == -1){
          this.setData({hidden:true})
          wx.showToast({
            title: '账号或密码错误'
          })
        }else{
          this.setData({ hidden: true })
          wx.showToast({
            title: '服务器正忙',
          })
        }
      }
    })
  },
  bindAgreeChange: function (e) {
    this.setData({
      isAgree: !!e.detail.value.length
    });
  }
})