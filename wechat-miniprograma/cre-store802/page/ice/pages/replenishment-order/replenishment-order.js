const config = require('../../../../config');
const signature = require('../../../../util/signature.js');
const app = getApp();
// pages/jh/jh.js
Page({
  data:{
    list:[],
    page:0,
    hasMore:true
  },
  onLoad:function(){
    if (!signature.checkLogin()) {
      wx.redirectTo({
        url: '/page/ice/pages/login/login',
      })
      return;
    }
    let oldList = this.data.list;
    let page =this.data.page;
    this.list(page,oldList)
  },
  calling: function () {
    wx.makePhoneCall({
      phoneNumber: '021-66290029',
      success: function () {
        console.log("拨打电话成功！")
      },
      fail: function () {
        console.log("拨打电话失败！")
      }
    })
  },
  confirmOrder:function(e){
    wx.showModal({
      title: '温馨提醒',
      content: '您确定已经收到配送物料了吗？',
      success:(res)=>{
        if(res.confirm){
          let repid = e.currentTarget.dataset.repid;
          let index = e.currentTarget.dataset.index;
          wx.request({
            url: config.materialUrl,
            data: {
              wid: wx.getStorageSync('jwt').wid,
              token: config.token,
              repid: repid,
              type: 'confirm'
            },
            success: (res) => {
              if (res.data.code == 0) {
                wx.showToast({
                  title: '订单确认完成',
                })
                let list = this.data.list;
                list[index].status = 4;
                this.setData({ list: list })
              }
            }
          })
        }
      }
    })
  },
  //上拉触底分页
  onReachBottom:function(){
    if(!this.data.hasMore) return false;
    let oldList =this.data.list;
    let page = this.data.page;
    this.list(page,oldList)
  },
  //get order list
  list:function(page,oldList){
    wx.request({
      url: config.materialUrl,
      data: {
        wid: wx.getStorageSync('jwt').wid,
        token: config.token,
        page: page,
        type: 'list'
      },
      success: (res) => {
        if (res.data.code == 0) {
          if (res.data.list.length == 20) {
            this.setData({ list: oldList.concat(res.data.list), page: page + 1, hasMore: true })
          } else {
            this.setData({ list: oldList.concat(res.data.list), page: page, hasMore: false })
          }
        } else {
          wx.showToast({
            title: '服务器正忙',
          })
        }
      }
    })
  }
  
})