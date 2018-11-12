var config = require('../../../../config');
var app = getApp();
// pages/jh/jh.js
Page({
  data: {
    material: [],
    list:[],
    repid:0,
    minusStatuses: ['disabled', 'disabled', 'normal', 'normal', 'disabled'],
    total: 0
  },
  bindMinus: function (e) {
    var index = parseInt(e.currentTarget.dataset.index);//得到下标
    var num = this.data.material[index].num;
    // 如果只有1件了，就不允许再减了
    if (num > 0) {
      num--;
    }
    // 只有大于一件的时候，才能normal状态，否则disable状态
    var minusStatus = num <= 0 ? 'disabled' : 'normal';
    // 购物车数据
    var material = this.data.material;
    material[index].num = num;
    // 按钮可用状态
    var minusStatuses = this.data.minusStatuses;
    minusStatuses[index] = minusStatus;
    // 将数值与状态写回
    this.setData({
      material: material,
      minusStatuses: minusStatuses
    });
    this.sum()
  },
  bindPlus: function (e) {
    var index = parseInt(e.currentTarget.dataset.index);

    var num = this.data.material[index].num;
    // 自增
    num++;

    // 只有大于一件的m时候，才能normal状态，否则disable状态
    var minusStatus = num <= 1 ? 'disabled' : 'normal';
    // 购物车数据
    var material = this.data.material;
    material[index].num = num;
    material[index].selected = true;
    // 按钮可用状态
    var minusStatuses = this.data.minusStatuses;
    minusStatuses[index] = minusStatus;
    console.log(minusStatuses[index])
    // 将数值与状态写回
    this.setData({
      material: material,
      minusStatuses: minusStatuses
    });
    this.sum()
  },
  //总价
  sum: function (e) {
    var material = this.data.material;
    // 计算总金额
    var total = 0;
    for (var i = 0; i < material.length; i++) {
        total += parseInt(material[i].num);    
    }
    // 写回经点击修改后的数组
    this.setData({
      total: total
    });
  },
  onLoad: function (options) {
    let repid = options.repid;
    wx.request({
      url: config.materialUrl,
      data: {
        wid: wx.getStorageSync('jwt').wid,
        token: config.token,
        repid: repid,
        type: 'queryOne'
      },
      success: (res) => {
        if (res.data.code == 0) {
          this.setData({ material: res.data.material,list:res.data.list,repid:repid,total:1 })
        } else {
          wx.showToast({
            title: '服务器异常'
          })
        }
      }
    })
  },
  toSubmit: function () {
    if (this.data.total > 0) {
      let material = this.data.material;
      let repid =this.data.repid
      wx.request({
        url: config.materialUrl,
        data:{
          wid:wx.getStorageSync('jwt').wid,
          token:config.token,
          repid:repid,
          material: JSON.stringify(material),
          type:'update'
        },
        success:(res)=>{
          if(res.data.code == 0){
            wx.showToast({
              title: '修改成功',
            })
            setTimeout(function(){
              wx.redirectTo({
                url: '../replenishment-order/replenishment-order',
              })
            },1000)
          }else{
             wx.showToast({
               title: '服务器正忙',
             })
          }
        }
      })
      
    }
    return false;
  }
})