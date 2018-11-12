const app =getApp();
const config =require('../../config.js');
const signature =require('../../common/signature.js');
Page({

  /**
   * 页面的初始数据
   */
  data: {
    comments:{},
    or_number:''
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this.setData({or_number:options.or_number});
    wx.request({
      url: config.commentUrl,
      data:{
        wid: wx.getStorageSync('wid'),
        token:config.token,
        type:'queryOCDetail',
        orid:options.orid
      },
      success:(res)=>{
        if(res.data.code == 0){
          this.setData({
             comments:res.data.comments
          })
        }
      }
    })
  }
})