const config = require('../../config.js');
const signature = require('../../util/signature.js')
var wxCharts = require('../../util/wxcharts.js');
var app = getApp();
var lineChart = null;
Page({
  complete: function () {
    wx.hideNavigationBarLoading();
    wx.stopPullDownRefresh();
  },
  calling: function () {
    wx.makePhoneCall({
      phoneNumber: '8615801817001',
      success: function () {
        //console.log("拨打电话成功！")
      },
      fail: function () {
        //console.log("拨打电话失败！")
      }
    })
  },
  callinge: function () {
    wx.scanCode({
      success: (res) => {
        console.log(res)
      }
    })
  },
  data: {
    machineIndex: 0,
    sweety: 0,
    datetime: '',
    userInfo: { avatarUrl: 'resources/touxiang.png' },
    hasUserInfo: false,
    menus: ["近30天"],
    activeIndex: 0,
    sliderOffset: 0,
    sliderLeft: 0,
    hasLoad: true
  },
  bindCountryChange: function (e) {
    var that = this;
    this.setData({
      machineIndex: e.detail.value
    })
    var myDate = new Date();
    wx.request({
      url: config.sweetyUrl,
      data: {
        sn: that.data.machines[e.detail.value],
        wid: wx.getStorageSync('jwt').wid,
        token: config.token
      },
      success: function (res) {
        that.setData({
          datetime: signature.getCustomDateTime('dateTime'),
          month: myDate.getMonth() + 1,
          sweety: res.data.sweety,
          this_month: res.data.this_month,
          categories: res.data.categories,
          last_month_stat: res.data.last_month_stat,
          this_month_stat: res.data.this_month_stat
        });
        that.updateData();
      }
    });
  },
  getUserInfo: function () {
    this.setData({hasLoad:false})
    var that = this
    if (app.globalData.hasLogin === false) {
      wx.login({
        success: _getUserInfo
      })
    } else {
      _getUserInfo()
    }

    function _getUserInfo() {
      var myDate = new Date();
      wx.getUserInfo({
        success: function (res) {
          app.globalData.hasUserInfo = true;
          app.globalData.userInfo = res.userInfo;
          that.setData({
            hasUserInfo: true,
            userInfo: res.userInfo
          });
          wx.request({
            url: config.sweetyUrl,
            data: {
              wid: wx.getStorageSync('jwt').wid,
              token: config.token
            },
            success: function (res) {
              app.globalData.machine_name = res.data.machine_names[0];
              app.globalData.address = res.data.address[0];
              that.setData({
                datetime: signature.getCustomDateTime('dateTime'),
                month: myDate.getMonth() + 1,
                sweety: res.data.sweety,
                this_month: res.data.this_month,
                categories: res.data.categories,
                machines: res.data.machines,
                machine_names: res.data.machine_names,
                last_month_stat: res.data.last_month_stat,
                this_month_stat: res.data.this_month_stat,
                hasLoad:true
              });
              that.updateData();
            }
          });
        },
        fail:(res)=>{
          wx.request({
            url: config.sweetyUrl,
            data: {
              wid: wx.getStorageSync('jwt').wid,
              token: config.token
            },
            success: function (res) {
              app.globalData.machine_name = res.data.machine_names[0];
              app.globalData.address = res.data.address[0];
              that.setData({
                datetime: signature.getCustomDateTime('dateTime'),
                month: myDate.getMonth() + 1,
                sweety: res.data.sweety,
                this_month: res.data.this_month,
                categories: res.data.categories,
                machines: res.data.machines,
                machine_names: res.data.machine_names,
                last_month_stat: res.data.last_month_stat,
                this_month_stat: res.data.this_month_stat,
                hasLoad:true
              });
              that.updateData();
            }
          });
        }
      })
    }
  },
  clear: function () {
    this.setData({
      hasUserInfo: false,
      userInfo: {}
    })
  },
  touchHandler: function (e) {
    // console.log(lineChart.getCurrentDataIndex(e));
    lineChart.showToolTip(e, {
      // background: '#7cb5ec',
      format: function (item, category) {
        return category + ':' + item.data + '个'
      }
    });
  },
  createSimulationData: function () {
    var categories = [];

    var data = [];
    for (var i = 0; i < 31; i++) {
      categories.push(i + 1);
      data.push(0);
    }

    return {
      categories: categories,
      data: data,
    }
  },

  updateData: function () {
    var that = this;
    // console.log(that.data.this_month);
    // console.log(that.data.categories);
    var series = [{
      name: '30天',
      data: that.data.this_month,
      format: function (val, name) {
        return val.toFixed(0);
      }
    }];
    lineChart.updateData({
      categories: that.data.categories,
      series: series
    });
  },
  onLoad: function (e) {
    if (signature.checkLogin()) {
      var that = this;
      that.getUserInfo();
      /**setTimeout(function () {
        that.getUserInfo();
      }, 1500);**/
      var windowWidth = 320;
      try {
        var res = wx.getSystemInfoSync();
        windowWidth = res.windowWidth;
      } catch (e) {
        console.log('getSystemInfoSync failed!');
      }

      var simulationData = this.createSimulationData();
      lineChart = new wxCharts({
        canvasId: 'sweetyCanvas',
        type: 'line',
        categories: simulationData.categories,
        animation: true,
        background: '#f5f5f5',
        series: [{
          name: '30天',
          data: simulationData.data,
          format: function (val, name) {
            return val.toFixed(0);
          }
        }],
        xAxis: {
          disableGrid: true
        },
        yAxis: {
          title: '甜蜜数(个)',
          format: function (val) {
            return val.toFixed(0);
          },
          min: 0
        },
        width: windowWidth,
        height: 200,
        dataLabel: false,
        dataPointShape: true,
        extra: {
          lineStyle: 'curve'
        }
      });
    } else {
      wx.redirectTo({
        url: '/page/ice/pages/login/login',
      })
    }
  },

  onShow: function () {
    if (signature.checkLogin()) {
      // 页面显示
      var span = wx.getSystemInfoSync().windowWidth / this.data.menus.length + 'px';
      this.setData({
        itemWidth: this.data.menus.length <= 2 ? span : '160rpx'
      });
    } else {
      wx.redirectTo({
        url: '/page/ice/pages/login/login',
      })
    }
  },
  tabChange: function (e) {
    var i = e.currentTarget.dataset.index;
    this.setData({
      activeIndex: i
    });
  },
  onPullDownRefresh: function () {
    var that = this;
    var myDate = new Date();
    wx.request({
      url: config.sweetyUrl,
      data: {
        sn: that.data.machines[that.data.machineIndex],
        wid: wx.getStorageSync('jwt').wid,
        token: config.token
      },
      header: {
        'content-type': 'application/json'
      },
      success: function (res) {
        that.setData({
          datetime: signature.getCustomDateTime('dateTime'),
          month: myDate.getMonth() + 1,
          sweety: res.data.sweety,
          this_month: res.data.this_month,
          categories: res.data.categories,
          machines: res.data.machines,
          machine_names: res.data.machine_names,
          last_month_stat: res.data.last_month_stat,
          this_month_stat: res.data.this_month_stat
        });
        that.updateData();
      },
    });
  }
})