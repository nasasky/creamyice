var config = require('../../../../config');
var app = getApp();
Page({
  data: {
    
  },
  onLoad:function(){
     this.getProduct();
  },
  getProduct:function(){
    wx.request({
      url: config.productUrl,
      data:{
        wid: wx.getStorageSync('jwt').wid,
        token: config.token,
        type:'getPro'      
      },
      success:(res)=>{
         if(res.data.code == 0){
           this.setData({
             product:res.data.product
           })
         }
      }
    })
  },
  /**
   * modify place pto_list
   */
  modifyPro:function(type,pid,sn){
      wx.request({
        url: config.productUrl,
        data:{
          wid: wx.getStorageSync('jwt').wid,
          token:config.token,
          pid:pid,
          sn:sn,
          type:type
        },
        success:(res)=>{
          if(res.data.code == 0){
            wx.showToast({
              title: res.data.msg,
            })
            this.getProduct();
          }
        }
      })
  },
  downPro: function (e) {
    let pid = e.currentTarget.dataset.pid;
    let sn = e.currentTarget.dataset.sn;
    wx.showModal({
      title: '郁金香雪吻甜筒',
      content: '您确定要下架本产品？',
      confirmText: "确定",
      cancelText: "取消",
      success: (res)=> {
        if (res.confirm) {
          this.modifyPro('downPro',pid,sn);
        } else {
          console.log('取消')
        }
      }
    });
  },
  upPro: function (e) {
    let pid =e.currentTarget.dataset.pid;
    let sn =e.currentTarget.dataset.sn;
    console.log(sn)
    wx.showModal({
      title: '格罗宁黑松甜筒',
      content: '您确定要上架本产品？',
      confirmText: "确定",
      cancelText: "取消",
      success: (res)=> {
        if (res.confirm) {
          this.modifyPro('upPro',pid,sn);
        } else {
          console.log('取消')
        }
      }
    });
  },
});