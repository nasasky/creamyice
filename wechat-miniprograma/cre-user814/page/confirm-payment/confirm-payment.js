const config = require('../../config.js');
const app = getApp();
const signature = require('../../common/signature.js');
Page({
  data: {
    store: '',
    totalMoney: 12,
    sn: '',
    img: '/resources/home1.png',
    zone: '安徽',
    address: '',
    totalNum: 1,
    shopCar: [],
    radioItems: [
      { name: '微信支付', value: '1', checked: true }
    ],
    discount: 0,
    payMoney: 12,
    cuscouponid: 0,
    fullReduce: 0,
    redPack: 0,
    is_share:false,
    type:0
  },
  /**
   * onLoad 监听页面初次加载函数
   */
  onLoad: function (options) {
    try {
      let shopCar = wx.getStorageSync('shopCar');
      let storeInfo = wx.getStorageSync('storeInfo')
      let fullReduce = 0;
      if(storeInfo.type == 1){
        if (options.totalMoney >= storeInfo.full) {
          fullReduce = parseInt(storeInfo.reduce);
        }
        this.setData({
          full: storeInfo.full,
          reduce: storeInfo.reduce});
      }else if(storeInfo.type == 2){
        if (options.totalNum > 1) {
          fullReduce = 7.5;
        }
      }
      this.setData({
        store: storeInfo.store,
        sn: storeInfo.sn,
        totalMoney: options.totalMoney,
        address: storeInfo.address,
        zone: storeInfo.zone,
        totalNum: options.totalNum,
        shopCar: shopCar,
        fullReduce: fullReduce,
        is_share: storeInfo.is_share,
        type: storeInfo.type
      });
     
      //setData by from type
      if (options.type) {
        let discount = parseInt(options.redPack) + fullReduce;
        this.setData({ redPack: options.redPack, cuscouponid: options.cuscouponid, discount: discount})
      } else {
        this.getCoupons(options.totalMoney, fullReduce)
      }
    } catch (e) {
      wx.navigateBack({
        delta: 1
      })
    }
  },

  /**
   * onShow 监听页面显示
   */
  onShow:function(){
    let wid = wx.getStorageSync('wid') || app.globalData.account_id;
    let is_share = this.data.is_share;
    wx.request({
      url: config.checkPayUrl,
      data: {
        wid: wid,
        token: config.token,
      },
      success: (res) => {
        if (res.data.code == 0) {
          let orid = res.data.numbers.id;
          let or_number = res.data.numbers.number;
          let clock = res.data.numbers.clock;
          if (is_share) {
            wx.reLaunch({
              url: '/page/count-down/count-down?orid='+orid+'&or_number='+or_number+'&type=share&clock='+clock
            })
          } else {
            wx.reLaunch({
              url: '/page/count-down/count-down?orid=' + orid + '&or_number=' + or_number + '&type=no_share&clock='+clock
            })
          }
        }
      }
    })
  },
  /**
   * getCoupons获取用户状态为0的优惠券，表示未使用的
   */
  getCoupons: function (totalMoney, fullReduce) {
    wx.request({
      url: config.couponUrl,
      data: {
        wid: wx.getStorageSync('wid') || app.globalData.account_id,
        token: config.token,
        status: 0,
        totalMoney: totalMoney,
        type: 'queryMax'
      },
      success: (res) => {
        if (res.data.code == 0) {
          let discount = parseInt(res.data.redPack) + fullReduce;
          this.setData({ redPack: res.data.redPack, cuscouponid: res.data.couponId, discount: discount})
        }
        else {
          this.setData({ redPack: 0, cuscouponid: 0, discount: fullReduce })
        }
      }
    })
  },
  /**
   * 绑定去支付事件
   */
  submitOrder: function (event) {
    var wid = wx.getStorageSync('wid') || app.globalData.account_id;
    var is_share = this.data.is_share;
    var shopCar = this.data.shopCar;
    //去支付先获取code，后端获取openid发起网络请求将必须参数传到后端
    wx.request({
      url: config.paymentUrl + '?token=' + config.token + '&wid=' + wid,
      data: {
        store: this.data.store,
        totalNum: this.data.totalNum,
        address: this.data.address,
        totalMoney: (this.data.totalMoney - this.data.discount) > 0 ? (this.data.totalMoney - this.data.discount) : 1,
        sn: this.data.sn,
        zone: this.data.zone,
        cuscouponid: this.data.cuscouponid,
        discount: this.data.discount,
        shopCar: JSON.stringify(shopCar)
      },
      method: 'POST',
      header: {
        'content-type': 'application/x-www-form-urlencoded'
      },
      success: (res) => {
        if (res.data.code == 0) {
          let orid = res.data.sales_id;
          wx.requestPayment({
            appid: res.data.payData.appId,
            timeStamp: res.data.payData.timeStamp,
            nonceStr: res.data.payData.nonceStr,
            package: res.data.payData.package,
            signType: 'MD5',
            paySign: res.data.payData.paySign,
            success: (res) => {
              console.log(res)            
            },
            fail: (res) => {
              console.log(res)
              //用户点击取消支付默认停留在当前页面    
            },
            complete: (res) => {
              console.log(res)
              if (res.errMsg === 'requestPayment:ok'){
                wx.removeStorageSync('shopCar')
                wx.removeStorageSync('storeInfo')            
                wx.request({
                  url: config.paymentUrl,
                  data: {
                    wid: wid,
                    token: config.token,
                    type: 'update',
                    status: 2,
                    orid: orid
                  },
                  success: (res) => {
                    let or_number = res.data.number;
                    if (res.data.code == 0) {
                      if (is_share) {
                        wx.reLaunch({
                          url: '/page/count-down/count-down?orid=' + orid + '&or_number=' + or_number + '&type=share&clock=60'
                        })
                      } else {
                        wx.reLaunch({
                          url: '/page/count-down/count-down?orid=' + orid + '&or_number=' + or_number + '&type=no_share&clock=60'
                        })
                      }
                    } else {
                      wx.showToast({
                        title: '服务器忙不过来了',
                      })
                    }
                  }
                })
              }
            }
          })
        }
      }
    })
  }


});
