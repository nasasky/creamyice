const config =require('../../config.js');
var sliderWidth = 96; // 需要设置slider的宽度，用于计算中间位置
const app =getApp();
Page({
  data: {
    tabs: ["未使用(0)", "已使用(0)", "已过期(0)"],
    counts:[],
    activeIndex: 1,
    sliderOffset: 0,
    sliderLeft: 0,
    coupons:[],
    totalMoney:12,
    totalNum:1
  },
  onLoad: function (options) {
    if(options.type == 'pay'){
       this.setData({type:'pay',totalMoney:options.totalMoney,totalNum:options.totalNum})
       this.quryUse(options.totalMoney);
    }else{
      var that = this;
      wx.getSystemInfo({
        success: function (res) {
          that.setData({
            sliderLeft: (res.windowWidth / that.data.tabs.length - sliderWidth) / 2,
            sliderOffset: res.windowWidth / that.data.tabs.length * that.data.activeIndex,
            hidden:false
          });
        }
      });
      this.getCoupons(this.data.activeIndex);
    }
  },
  /**
   * 获取用户满减可用优惠券
   */
  quryUse:function(totalMoney){
    wx.request({
      url: config.couponUrl,
      data: {
        wid: wx.getStorageSync('wid'),
        token: config.token,
        type: 'queryUse',
        totalMoney:totalMoney
      },
      success: (res) => {
        if (res.data.code == 0) {
          this.setData({ coupons: res.data.coupons })
        } else if (res.data.code == -1) {
          this.setData({ coupons: [] })
        } else {
          wx.showToast({
            title: '服务器正忙',
          })
        }
      }
    })
  },
  /**
   * 获取用户所有的优惠券
   */
  getCoupons:function(status){
     wx.request({
       url: config.couponUrl,
       data:{
         wid:wx.getStorageSync('wid'),
         token:config.token,
         status:status,
         type:'queryCoupons'
       },
       success:(res)=>{
         if(res.data.code == 0){
           this.setData({coupons:res.data.coupons,tabs:res.data.counts,hidden:true})
         }else if(res.data.code == -1){
            this.setData({coupons:[],hidden:true})
         }else{
           this.setData({hidden:true})
           wx.showToast({
             title: '服务器正忙',
           })
         }
       }
     })
  },
  tabClick: function (e) {
    this.setData({
      sliderOffset: e.currentTarget.offsetLeft,
      activeIndex: e.currentTarget.id,
      hidden:false,
      coupons:[]
    });
    this.getCoupons(e.currentTarget.id);
  },
  radioChange:function(e){
     let str =e.detail.value;
     let arr = str.split(',');
     let cuscouponid = arr[0];
     let redPack = arr[1];
     let totalMoney =this.data.totalMoney;
     let totalNum = this.data.totalNum;
    wx.redirectTo({
      url: '/page/confirm-payment/confirm-payment?type=fromCoupon&cuscouponid='+cuscouponid+'&redPack='+redPack+'&totalMoney='+totalMoney+'&totalNum='+totalNum,
    })
  }
  
});