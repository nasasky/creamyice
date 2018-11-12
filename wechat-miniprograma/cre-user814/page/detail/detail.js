const config = require('../../config.js');
// 获取全局应用程序实例对象
const app = getApp();

Page({

  /**
   * 页面的初始数据
   */
  data: {
    product: [],
    comment: {},
    hasComment: false,
    list: {},
    buycar_num: 0,
    // buycar
    totalMoney: 0,
    chooseAll: false,
    hasLoad: false,
    is_share:false
  },
  shopping: function(e) {
    var index = parseInt(e.currentTarget.dataset.id);
    var selected = this.data.product[index].selected;
    var product = this.data.product;
    var totalMoney = this.data.totalMoney;
    var buycar_num = this.data.buycar_num;
    product[index].selected = !selected;
    if (product[index].selected) {
      if(product[index].num == 0) product[index].num =1;
      totalMoney += Number(product[index].price * product[index].num);
      buycar_num += Number(product[index].num);
    } else {
      totalMoney -= Number(product[index].price * product[index].num);
      buycar_num -= Number(product[index].num);
    }
    totalMoney = Number(totalMoney).toFixed(2);
    this.setData({
      product: product,
      totalMoney: Number(totalMoney),
      buycar_num: buycar_num
    });
  },
  toSubmit: function() {
    let product = this.data.product;
    let shopCar = [];
    let i = 0;
    for (var index in product) {
      if (product[index].selected && product[index].num > 0) {
        shopCar[i] = product[index];
        i++;
      }
    }
    if (shopCar.length == 0) {
      wx.showToast({
        title: '请选择产品',
      })
      return;
    }
    try {
      wx.setStorageSync('shopCar', shopCar)
      let list = this.data.list
      wx.setStorageSync('storeInfo', list)
      let totalMoney = this.data.totalMoney
      let totalNum = this.data.buycar_num
      wx.navigateTo({
        url: '../confirm-payment/confirm-payment?totalMoney=' + totalMoney + '&totalNum=' + totalNum,
      })
    } catch (e) {
      wx.showToast({
        title: '购买异常',
      })
    }
  },
  numAdd: function(e) {
    var index = parseInt(e.currentTarget.dataset.id);
    var product = this.data.product;
    if (this.data.list.enable == 1 && this.data.list.is_dis && this.data.list.is_business && this.data.list.is_status && product[index].is_have) {
      
      if (product[index].num < 10) {
        
        product[index].num = Number(product[index].num) + 1;
        product[index].selected = true;
        var totalMoney = 0, buycar_num = 0;
        for (var i = 0, len = product.length; i < len; i++) {
          if (product[i].selected) {
            totalMoney += product[i].num * product[i].price;
            buycar_num += product[i].num;
          }
        }

        totalMoney = Number(totalMoney).toFixed(2);
        this.setData({
          product: product,
          totalMoney: Number(totalMoney),
          buycar_num: buycar_num
        });
      }
    }
  },
  numReduce: function(e) {
    var index = parseInt(e.currentTarget.dataset.id);
    var product = this.data.product;
    if (this.data.list.enable == 1 && this.data.list.is_dis && this.data.list.is_business && this.data.list.is_status && product[index].is_have) {
      if (product[index].num > 0) {
        product[index].selected =true;
        if (product[index].num == 1){
          product[index].selected = false;
        }
        product[index].num = Number(product[index].num) - 1;    
        var totalMoney=0, buycar_num=0;
        for(var i=0,len =product.length;i<len;i++){
          if(product[i].selected){
            totalMoney +=product[i].num * product[i].price;
            buycar_num +=product[i].num;
          }
        }
        totalMoney = Number(totalMoney).toFixed(2);
        this.setData({
          product: product,
          totalMoney: Number(totalMoney),
          buycar_num: buycar_num
        });
      }
    }
  },
  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function(options) {
    this.setData({
      sn: options.sn
    });
    this.getDetail(options.sn);
    this.getComment(options.sn);
  },
  /**
   * 获取用户评论最新的一条
   */
  getComment: function(sn) {
    let wid = wx.getStorageSync('wid') || -1
    wx.request({
      url: config.commentUrl,
      data: {
        wid: wid,
        token: config.token,
        sn: sn,
        type: 'selectone'
      },
      success: (res) => {
        if (res.data.code == 0) {
          this.setData({
            comment: res.data.comment,
            hasComment: true
          })
        } else {
          this.setData({
            hasComment: false
          })
        }
      }
    })
  },
  /**
   * 根据sn获取店铺详细信息和产品详细信息
   */
  getDetail: function(sn) {
    wx.getLocation({
      type: 'gcj02',
      success: (res) => {
        let latitude = res.latitude;
        let longitude = res.longitude;
        wx.request({
          url: config.storeDetailUrl,
          data: {
            wid: wx.getStorageSync('wid') || -1,
            token: config.token,
            sn: sn,
            type: 'location',
            latitude: latitude,
            longitude: longitude
          },
          success: (res) => {
            this.setData({
              product: res.data.product,
              list: res.data.list,
              hasLoad: true
            })
          }
        })
      },
      fail: (res) => {
        wx.request({
          url: config.storeDetailUrl,
          data: {
            wid: wx.getStorageSync('wid') || -1,
            token: config.token,
            sn: sn,
            type: 'noLocation'
          },
          success: (res) => {
            console.log(res.data)
            this.setData({
              product: res.data.product,
              list: res.data.list,
              hasLoad: true
            })
          }
        })
      }
    })
  },
  calling: function() {
    wx.makePhoneCall({
      phoneNumber: '02166291031',
      success: function() {
        //console.log("拨打电话成功！")
      },
      fail: function() {
        //console.log("拨打电话失败！")
      }
    })
  },
  /**
   * 绑定去查看地图函数
   */
  goMap: function(e) {
    let longitude = parseFloat(this.data.list.longitude);
    let latitude = parseFloat(this.data.list.latitude);
    wx.openLocation({
      latitude: latitude,
      longitude: longitude,
      name: this.data.list.store,
      address: this.data.list.address
    })
  },
  /**
   * 监听用户分享事件
   */
  onShareAppMessage: function(res) {
    return {
      title: '克瑞米艾冰淇淋',
      path: '/page/feature/feature',
      success: (res) => {
        console.log('分享成功')
      },
      fail: (res) => {
        console.log('分享失败')
      }
    }
  }
})