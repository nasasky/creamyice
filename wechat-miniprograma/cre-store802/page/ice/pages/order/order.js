var config = require('../../../../config');
const signature =require('../../../../util/signature.js');
var timer = null;
Page({
  data: {
    inputShowed: false,
    inputVal: "",
    numbers: '',
    page: 1,
    nextPage: 2,
    // input默认是1  
    num: 1,
    count43:0,
    // 使用data数据对象设置样式名  
    minusStatus: 'disabled',
    hasLoad:true,
    hasMore:true  
  },
  
  onShow:function(){
     if(!signature.checkLogin()){
       wx.redirectTo({
         url: '/page/ice/pages/login/login',
       })
     }else{
       this.setData({ hasLoad: false, nextPage:2,page:1,hasMore:true})
       this.getNumbers(1);
       this.getCount43();
       this.countDown();
     }
  },
  /**
   * 获取单号列表
   */
  getNumbers: function (page) {
    wx.request({
      url: config.numbersUrl,
      data: {
        token: config.token,
        wid: wx.getStorageSync('jwt').wid,
        page: page,
        type: 'get'
      },
      success: (res) => {
        let nextPage = parseInt(this.data.nextPage);
        if (res.data.code == 0) {
          this.setData({ hasLoad:true,numbers: res.data.numbers,hasMore:true })
        } else if (res.data.code == -1) {
          this.setData({hasMore:false,hasLoad:true})
        } else {
          console.log(250)
        }
      }
    })
  },
  /**
   * 获取商户店铺自定义产品
   */
  getCount43:function(){
    wx.request({
      url: config.machineUrl,
      data: {
        wid: wx.getStorageSync('jwt').wid,
        token: config.token,
        type: 'getCount43'
      },
      success: (res) => {
        if (res.data.code == 0) {
          this.setData({ countList:res.data.countList,count43:res.data.count43})
        } else if (res.data.code == -1) {
          this.setData({ countList: [] })
        } else {
          console.log(res)
        }
      }
    })
  },
  /**
   * 定时器
   */
  countDown: function () {
    var that = this;
    timer = setInterval(function () {
      that.setData({nextPage:2,page:1})
      that.getNumbers(1)
    }, wx.getStorageSync('jwt').intervalTime);
  },
  /**
   * 搜索事件
   */
  inputTyping: function (e) {
    let num = e.detail.value;
    let wid = wx.getStorageSync('jwt').wid;
    if (num && num.length >= 4) {
      wx.request({
        url: config.numbersUrl,
        data: {
          wid: wid,
          token: config.token,
          number: num,
          type: 'query'
        },
        success: (res) => {
          if (res.data.code == 0) {
            this.setData({ numbers: res.data.numbers })
          } else if (res.data.code == -1) {
            wx.showToast({
              title: '没有该单号',
            })
            this.setData({ numbers: [] })
            this.countDown()
          } else {
            console.log(res)
          }
        }
      })
    } else {
      return num;
    }
  },
  /**
   * 确定按钮事件
   */
  openConfirm: function (e) {
    let d = e.currentTarget.dataset;
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
              detail:JSON.stringify(d.detail),
              type: 'confirm2'
            },
            success: (res) => {
              if (res.data.code == 0) {
                this.setData({
                  page:1,
                  nextPage:2
                })
                //成功之后主动刷新页面
                this.getNumbers(1)
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
   * 上拉触底分页事件
   * */
  onReachBottom: function () {
    if(!this.data.hasMore) return;
    let page = parseInt(this.data.nextPage);
    if (page > 2) clearInterval(timer);
    this.setData({hasLoad:false})
    wx.request({
      url: config.numbersUrl,
      data: {
        token: config.token,
        wid: wx.getStorageSync('jwt').wid,
        page: page,
        type: 'get'
      },
      success: (res) => {
        let nextPage = parseInt(this.data.nextPage);
        if (res.data.code == 0) {
          let oldNumbers = this.data.numbers;
          this.setData({ nextPage: nextPage + 1, hasMore: true, hasLoad: true, numbers: oldNumbers.concat(res.data.numbers)})
        } else if (res.data.code == -1) {
          this.setData({ nextPage: nextPage ,hasMore:false,hasLoad:true})
        } else {
          wx.showToast({
            title: '服务器开小差了，请稍后再试',
          })
        }
      }
    })
  },
  /**
   * 下拉刷新事件
   */
  onPullDownRefresh: function () {
    clearInterval(timer)
    this.setData({nextPage:2,page:1})
    this.countDown()
  },
  /**
   * 搜索框切换样式事件
   */
  showInput: function () {
    clearInterval(timer)
    this.setData({
      inputShowed: true
    });
  },
  hideInput: function () {
    this.setData({
      inputVal: "",
      inputShowed: false,
      nextPage:2,
      numbers:[]
    });
    this.getNumbers(1)
    this.countDown()
  },
  clearInput: function () {
    this.setData({
      inputVal: ""
    });
  },
  onHide:function(){
    clearInterval(timer);
  },
  /**
   * 商户确定取货事件
   */
  openConfirmb: function () {
    wx.showModal({   
      content: '可出料:1杯',
      confirmText: "确定",
      cancelText: "取消",
      success: (res)=> {
        if (res.confirm) {
          wx.request({
            url: config.machineUrl,
            data: {
              token: config.token,
              wid: wx.getStorageSync('jwt').wid,
              type: 'confirmCount43',
              sn:this.data.countList[0].sn,
              num:1
            },
            success: (res) => {
              if (res.data.code == 0) {
                wx.showToast({
                  title: '取货成功',
                })
                this.getCount43()
              } else if (res.data.code == -1) {
                wx.showToast({
                  title: '系统错误，请稍后再试',
                })
              } else {
                wx.showToast({
                  title: '服务器开小差了，请稍后再试',
                })
              }
            }
          })
        } else {
          console.log('取消')
        }
      }
    });
  }
  /**
   * socket connect
   */
});