var wxCharts = require('../../../../util/wxcharts.js');
const config =require('../../../../config.js');
const signature =require('../../../../util/signature.js')

var app = getApp();
var lineChart = null;

Page({
  data: {
    machineIndex: 0,
    sweety: 0,
    datetime: '',
    menus: ["近30天"],
    activeIndex: 0,
    sliderOffset: 0,
    sliderLeft: 0,
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
    this.setData({
      name:e.name,
      address:e.address,
      starttime:e.starttime,
      endtime:e.endtime,
      sn:e.sn
    });
    this.getSweety(e.sn);
    var windowWidth = 320;
    try {
      var res = wx.getSystemInfoSync();
      windowWidth = res.windowWidth;
    } catch (e) {
      console.error('getSystemInfoSync failed!');
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
  },
  //get store sweety details by sn
  getSweety:function(sn){
    wx.request({
      url: config.sweetyUrl,
      data: { wid: wx.getStorageSync('jwt').wid, sn: this.data.sn || sn, token: config.token },
      header: {
        'content-type': 'application/json'
      },
      success:(res)=> {
        this.setData({
          datetime: signature.getCustomDateTime('dateTime'),
          sweety: res.data.sweety,
          this_month: res.data.this_month,
          categories: res.data.categories,
          machines: res.data.machines,
          machine_names: res.data.machine_names,
          last_month_stat: res.data.last_month_stat,
          this_month_stat: res.data.this_month_stat,
          hasLoad:true
        });
        this.updateData();
      }
    });
  },
  onShow: function () {
    // 页面显示
    var span = wx.getSystemInfoSync().windowWidth / this.data.menus.length + 'px';
    this.setData({
      itemWidth: this.data.menus.length <= 2 ? span : '160rpx'
    });
  },
  onPullDownRefresh: function () {
    var that = this;
    var myDate = new Date();
    wx.request({
      url: config.sweetyUrl,
      data: { sn: that.data.machines[that.data.machineIndex], wid: wx.getStorageSync('jwt').wid,token:config.token },
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