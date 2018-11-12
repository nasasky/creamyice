//引入公共配置文件
const config = require('../../config.js');
// 获取全局应用程序实例对象
const app = getApp();
Page({

  /**
   * 页面的初始数据
   */
  data: {
    storeList: [],
    date: '',
    userInfo: {},
    hasUserInfo: false,
    page: 1,
    size: 4,
    hasMore: true,
    hasLoad: true,
    display: false,
    identi: null,
    fromid: null,
    new_coupons: []
  }, 
  /** 
   * 监听页面显示函数
   */
  onLoad: function (options) {
    let wid = wx.getStorageSync('wid') || -1;
    if (options.type && options.type == 'share' && wid > 0) {
      wx.request({
        url: config.sendCouponUrl,
        data: {
          wid: wid,
          token: config.token,
          type: 'getRandCoupons',
          identi: options.identi,
          fromid: options.fromid
        },
        success: (res) => {
          if (res.data.code == 1 || res.data.code == 2) {
            this.setData({ display: true, identi: options.identi, fromid: options.fromid, new_coupons: res.data.new_coupons })
          } else {
            console.log('服务器异常')
          }
        }
      })
    }
    this.setData({ hasLoad: false });
    this.getStore(wid, 1);
  },
  /**
   * 获取商店列表
   */
  getStore: function (wid, page) {
    var that = this;
    var size = this.data.size;
    var oldStoreList = this.data.storeList;
    wx.getLocation({
      type: 'gcj02',
      success: (res) => {
        let longitude = res.longitude;
        let latitude = res.latitude;
        wx.request({
          url: config.storeUrl,
          data: {
            wid: wid,
            token: config.token,
            page: page,
            size: size,
            longitude: longitude,
            latitude: latitude,
            type: 'location'
          },
          success: (res) => {
            if (res.data.code == 0) {
              that.setData({
                storeList: oldStoreList.concat(res.data.list),
                date: res.data.date,
                page: page,
                hasLoad: true,
                hasMore: true
              })
            } else if (res.data.code == -1) {
              that.setData({ hasLoad: true, hasMore: false })
            }
          }
        })
      },
      fail: (res) => {
        wx.request({
          url: config.storeUrl,
          data: {
            wid: wid,
            token: config.token,
            page: page,
            size: size,
            type: 'noLocation'
          },
          success: (res) => {
            if (res.data.code == 0) {
              that.setData({
                storeList: oldStoreList.concat(res.data.list),
                date: res.data.date,
                page: page,
                hasLoad: true,
                hasMore: true
              })
            } else if (res.data.code == -1) {
              that.setData({ hasLoad: true, hasMore: false })
            }
          }
        })
      }
    })
  },
  /**
   * 监听用户上拉触底，加载更多
   */
  onReachBottom: function () {
    if (this.data.hasMore) {
      let wid = -1;
      let page = parseInt(this.data.page);
      let nextPage = page + 1;
      this.setData({ hasLoad: false });
      this.getStore(wid, nextPage);
    }
  },
  onPullDownRefresh: function () {
    let wid = -1;
    this.setData({ storeList: [], hasLoad: false })
    this.getStore(wid, 1)
  },
  /**
   * 监听用户分享事件
   */
  onShareAppMessage: function (res) {
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
  },
  dontReceive: function () {
    this.setData({ display: false, identi: null, fromid: null, new_coupons: [] })
  },
  receive: function () {
    this.setData({ display: false, identi: null, fromid: null, new_coupons: [] })
    /**if (this.data.new_coupons && this.data.new_coupons.length > 0) {
      wx.request({
        url: config.sendCouponUrl,
        data: {
          wid: wid,
          token: config.token,
          type: 'insertCustomerCoupons',
          new_coupons: JSON.stringify(this.data.new_coupons),
          identi: this.data.identi,
          fromid: this.data.fromid
        },
        success: (res) => {
          this.setData({ display: false, identi: null, fromid: null, new_coupons: [] })
          wx.showToast({
            title: '红包领取成功，可进入个人中心查看',
          })
        }
      })
    }**/
  },
  onHide: function () {
    this.setData({ display: false, identi: null, fromid: null, new_coupons: [] })
  }
})