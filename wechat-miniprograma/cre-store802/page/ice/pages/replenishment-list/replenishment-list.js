var config = require('../../../../config');
var app = getApp();
// pages/jh/jh.js
Page({
  data: {
    material:[],
    minusStatuses: ['disabled', 'disabled', 'normal', 'normal', 'disabled'],
    total:0
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
    if (num == 0) material[index].selected = false;
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
  bindCheckbox: function (e) {
    //绑定点击事件，将checkbox样式改变为选中与非选中
    //拿到下标值，以在carts作遍历指示用
    var index = parseInt(e.currentTarget.dataset.index);
    //原始的icon状态
    var selected = this.data.material[index].selected;
    var material = this.data.material;
    // 对勾选状态取反
    material[index].selected = !selected;
    if(material[index].selected) material[index].num = 1;
    else material[index].num =0;
    // 写回经点击修改后的数组
    this.setData({
      material: material
    });
    this.sum()
  },
  //总价
  sum: function (e) {
    var material = this.data.material;
    // 计算总金额
    var total = 0;
    for (var i = 0; i < material.length; i++) {
      if (material[i].selected) {
        total += material[i].num;
      }
    }
    // 写回经点击修改后的数组
    this.setData({
      total:total
    });
  },
  onLoad: function (options) {
    wx.request({
      url: config.materialUrl,
      data:{
        wid:wx.getStorageSync('jwt').wid,
        token:config.token,
        type:'query'
      },
      success:(res)=>{
        if(res.data.code == 0){
          this.setData({material:res.data.material})
        }else{
          wx.showToast({
            title: '服务器异常'
          })
        }
      }
    })
  },
  toSubmit: function () {
    if(this.data.total > 0){
      let material = this.data.material;
      try{
        wx.setStorageSync('material', material)
        wx.navigateTo({
          url: '../immediately-order/immediately-order',
        })
      }catch (e){
         wx.showToast({
           title: '微信内部错误，请稍后再试！',
         })
      }
      return;
    }
    return false;
  }
})