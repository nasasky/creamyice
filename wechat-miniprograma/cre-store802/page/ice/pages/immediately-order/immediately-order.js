const config = require('../../../../config');
const signature = require('../../../../util/signature.js');
const app = getApp()
Page({
  data: {
    courDate: [{
      name: '周二',
      checked: true
    },
    {
      name: '下周二',
      checked: false
    }
    ],
    courTime: '',
    material: [],
    machineName: '超级管理员',
    telName: false,
    telephone: false,
    address: '地址默认',
    message: '',
    status: false
  },
  onLoad: function () {
    if (!signature.checkLogin()) {
      wx.redirectTo({
        url: '/page/ice/pages/login/login',
      })
      return;
    }
    try {
      let material = wx.getStorageSync('material');
      if (material) {
        let courDate = this.getTuesday()
        this.setData({
          material: material,
          machineName: app.globalData.machine_name,
          courDate: courDate,
          courTime: courDate[0].name,
          address: app.globalData.address
        });

        return;
      }
      wx.navigateBack({})
    } catch (e) {
      wx.showToast({
        title: '服务器异常，请稍后再试',
      })
      wx.navigateBack({})
    }
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
  submitOrder: function () {
    wx.request({
      url: config.materialUrl,
      data: {
        wid: wx.getStorageSync('jwt').wid,
        token: config.token,
        type: 'status'
      },
      success: (res) => {
        if (res.data.code == 0) {
          if (res.data.status == 0) {
            let telName = this.data.telName;
            let telephone = this.data.telephone;
            let material = this.data.material;
            let courTime = this.data.courTime;
            let message = this.data.message || '';
            let machineName = this.data.machineName;
            let address = this.data.address;
            if (telephone && telName && courTime && material) {
              wx.request({
                url: config.materialUrl,
                data: {
                  wid: wx.getStorageSync('jwt').wid,
                  token: config.token,
                  telName: telName,
                  telephone: telephone,
                  courTime: courTime,
                  message: message,
                  material: JSON.stringify(material),
                  machineName: machineName,
                  address: address,
                  type: 'add'
                },
                success: (res) => {
                  if (res.data.code == 0) {
                    wx.removeStorageSync('material')
                    wx.showToast({
                      title: '补货成功',
                    })           
                    setTimeout(function(){
                      wx.redirectTo({
                        url: '/page/ice/pages/replenishment-order/replenishment-order',
                      })
                    },1000)                          
                  } else {
                    wx.showToast({
                      title: '服务器正忙，请稍后再试',
                    })
                  }
                }
              })
            }
          }else{
            wx.showModal({
              title: '温馨提示',
              content: '您当前有订单尚未完成，暂时不能补货，可去我的补货单查看！',
              confirmText:'去查看',
              cancelText:'回主页',
              success: function (res) {
                if (res.confirm) {
                  wx.redirectTo({
                    url: '/page/ice/pages/replenishment-order/replenishment-order',
                  })
                } else if (res.cancel) {
                  wx.switchTab({
                    url: '/page/ice/index',
                  })
                }
              }
            })
          }
        }else{
          wx.showToast({
            title: '服务器正忙',
          })
        }
      }
    })
  },
  getTuesday: function () {
    //获取时间段
    var weekByDate = new Date();
    //获取日期字符串对应的时间戳
    var timestamp = new Date(weekByDate).getTime();
    //获取星期几
    var currentDay = new Date(weekByDate).getDay();

    //获取时间戳
    var firstTuesday, secondTuesday;
    if (currentDay < 2) {
      //获取该天前一个星期六的时间戳
      firstTuesday = timestamp + (2 - currentDay) * 24 * 60 * 60 * 1000;
      //获取该天后一个星期五的时间戳
      secondTuesday = timestamp + (2 - currentDay + 7) * 24 * 60 * 60 * 1000;
    } else {
      //获取该天前一个星期六的时间戳
      firstTuesday = timestamp + (2 - currentDay + 7) * 24 * 60 * 60 * 1000;
      //获取该天后一个星期五的时间戳
      secondTuesday = timestamp + (2 - currentDay + 14) * 24 * 60 * 60 * 1000;
    }
    var courDate = new Array(2);
    courDate[0] = {
      name: getNowFormatDate(firstTuesday),
      checked: true
    }
    courDate[1] = {
      name: getNowFormatDate(secondTuesday),
      checked: false
    }
    return courDate;
    //获取年月日字符串
    function getNowFormatDate(timestamp) {
      var year = new Date(timestamp).getFullYear();
      var month = new Date(timestamp).getMonth() + 1;
      var date = new Date(timestamp).getDate();
      if (month >= 1 && month <= 9) {
        month = "0" + month;
      }
      if (date >= 0 && date <= 9) {
        date = "0" + date;
      }
      var dateByTime = year + "-" + month + "-" + date;
      return dateByTime;
    }
  },
  radioChange: function (e) {
    this.setData({
      courTime: e.detail.value
    })
  },
  inputName: function (e) {
    this.setData({
      telName: e.detail.value
    })
  },
  inputTelephone: function (e) {
    this.setData({
      telephone: e.detail.value
    })
  },
  inputMessage: function (e) {
    this.setData({
      message: e.detail.value
    })
  }
})