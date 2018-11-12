const config = require('../../../../config');
const signature =require('../../../../util/signature.js')
Page({
  data: {
    inputShowed: false,
    inputVal: "",
    tabs: ["已领取(0)", "未领取(0)","已过期(0)"],
    activeIndex: 1,//同时用于状态值处理后使用
    numbers:[],
    date:'2018-05-05',
    page:1,
    size:6,
    nextPage:2,
    hasLoad:true,
    hasMore:true
  },
 /**
  * 监听页面加载
  */
  onLoad: function () {
    if(signature.checkLogin()){
      var that = this;
      this.setData({hasLoad:false})
      this.getNumbers(1, that.data.size);
    }else{
      wx.redirectTo({
        url: '/page/ice/pages/login/login',
      })
    }
  },
  /**
  * 确定按钮事件
  openConfirm: function (e) {
    let d = e.currentTarget.dataset;
    let that =this;
    wx.showModal({
      title: '取单号：' + d.number,
      content: '亲，请认真核对用户的取单号，以免出现不必要的纠纷！',
      confirmText: "确认无误",
      cancelText: "取消",
      success: (res) => {
        //商户点击确定发起网络请求
        if (res.confirm) {
          wx.request({
            url: config.numbersUrl,
            data: {
              wid: wx.getStorageSync('jwt').wid,
              token: config.token,
              number: d.number,
              count: d.count,
              orid: d.orid,
              sn: d.sn,
              addr: d.addr,
              type: 'confirm'
            },
            success: (res) => {
              if (res.data.code == 0) {
                //成功之后主动刷新页面
                that.setData({numbers:[]})
                that.getNumbers(1, that.data.size);
              } else {
                wx.showToast({
                  title: '服务器正忙',
                })
              }
            }
          })
        } else {
          console.log(res)
        }
      }
    });
  },*/

  /**
  * 确定按钮事件
  */
  openConfirm: function (e) {
    let d = e.currentTarget.dataset;
    let that = this;
    wx.showModal({
      title: '取单号：' + d.number,
      content: '亲，请核对取单号,以免出现不必要的纠纷！',
      confirmText: "确认无误",
      cancelText: "取消",
      success: (res) => {
        //商户点击确定发起网络请求
        if (res.confirm) {
          wx.request({
            url: config.numbersUrl,
            data: {
              wid: wx.getStorageSync('jwt').wid,
              token: config.token,
              orid: d.orid,
              sn:d.sn,
              detail: JSON.stringify(d.detail),
              type: 'confirm2'
            },
            success: (res) => {
              if (res.data.code == 0) {
                //成功之后主动刷新页面
                that.setData({ hasLoad:false,numbers: [] })
                that.getNumbers(1, that.data.size);
              } else {
                wx.showToast({
                  title: '服务器正忙',
                })
              }
            }
          })
        } else {
          console.log(res)
        }
      }
    });
  },
  /**
   * 获取单号列表
   */
  getNumbers: function (page, size) {
    wx.request({
      url: config.numbersUrl,
      data: {
        token: config.token,
        wid: wx.getStorageSync('jwt').wid,
        page: page,
        size: size,
        status: this.data.activeIndex,
        type: 'getMy'
      },
      success: (res) => {
        if (res.data.code == 0) {
          this.setData({ numbers:res.data.numbers,tabs:res.data.tabs,hasLoad:true,date:res.data.date})
        } else if (res.data.code == -1) {
          this.setData({tabs:res.data.tabs,numbers:-1,hasLoad:true})
        } else {
          this.setData({ hasLoad: true })
          wx.showToast({
            title: '服务器开小差了，请稍后再试',
          })
        }
      }
    })
  },
  /**
   * 下拉加载更多事件
   */
  onReachBottom:function(){
    if(!this.data.hasMore) return;
    this.setData({ hasLoad: false })
    let nextPage = parseInt(this.data.nextPage);
    let oldNumbers = this.data.numbers;
    wx.request({
      url: config.numbersUrl,
      data: {
        token: config.token,
        wid: wx.getStorageSync('jwt').wid,
        page: nextPage,
        size: this.data.size,
        status: this.data.activeIndex,
        type: 'getMy'
      },
      success: (res) => {
        if (res.data.code == 0) {
          this.setData({ numbers: oldNumbers.concat(res.data.numbers), 
          nextPage: nextPage + 1 ,tabs:res.data.tabs,hasLoad:true,hasMore:true})
        } else if (res.data.code == -1) {
          this.setData({hasMore:false,hasLoad:true})
        } else {
          wx.showToast({
            title: '服务器开小差了，请稍后再试',
          })
        }
      }
    })
  },
  /**
   * tab切换状态事件
   */
  tabClick: function (e) {
    this.setData({
      hasLoad:false,
      sliderOffset: e.currentTarget.offsetLeft,
      activeIndex: e.currentTarget.id,
      numbers:[],
      hasMore:true,

    });
    var that =this;
    this.getNumbers(that.data.page,that.data.size)
  }
});