const config = require('../../../../config');
const app = getApp();
Page({
  data: {
    sn: '0000000000',
    newname: '',
    menus: ["近7天", "近30日", "近30日"],
    activeIndex: 0,
    sliderOffset: 0,
    sliderLeft: 0,
    sweety: 0,
    machine: [],
    machines: [],
    status: ["待机", "制冷", "清洗", "保鲜", "解冻", "巴士杀菌", "再生", "重操作", "自动出料", "离线","未授权"],
    warnings: {0:"无报警", 1:"无转速信号报警", 2:"皮带打滑报警",
      3:"高压开关报警(HH)", 4:"料缸温度传感器开路(AL)", 5:"料缸温度传感器短路(AH)",
      6:"料缸温度传感器开路(bL)", 7:"料缸温度传感器短路(bH)",
      20:"缺料", 21:"推杆电机疑似故障", 22:"热循环升温阶失败",
      23:"热循环降温阶失败", 24:"冻缸自动保护", 25:"无料",
      255:""},
    index: 0,
    hasUserInfo: false
  },
  onLoad: function (options) {
    var that = this;
    that.setData({
      sn: options.sn
    });
    that.updateData();
  },
  setName: function(e) {
    var that = this;
    that.setData({
      newname: e.detail.value
    });
  },
  openToast: function () {
    var that = this;
    var name = this.data.newname;
    if(name.length<3)
    {
      wx.showToast({
        title: '名字太短',
        icon: 'success',
        duration: 1000
      });
    }
    else
    {
      wx.request({
        url: config.namingUrl,
        data: { 
          sn: that.data.sn, name: that.data.newname ,
          wid: wx.getStorageSync('jwt').wid,
          token:config.token
        },
        header: {
          'content-type': 'application/json'
        },
        success: function (res) {
          if(res.data.status == 'ok'){
            wx.showToast({
              title: '修改成功',
              icon: 'success',
              duration: 3000
            });
          }else{
            wx.showToast({
              title: '修改失败',
              icon: 'none',
              duration: 3000
            });
          }
        }
      });
    }  
  },
  openLoading: function () {
    wx.showToast({
      title: '系统操作中',
      icon: 'loading',
      duration: 3000
    });
  },
  onShow: function () {
    // 页面显示
    var span = wx.getSystemInfoSync().windowWidth / this.data.menus.length + 'px';
    this.setData({
      itemWidth: this.data.menus.length <= 2 ? span : '160rpx'
    });
  },
  tabChange: function (e) {
    var index = e.currentTarget.dataset.index;
    this.setData({
      activeIndex: index
    });
  },

  updateData: function () {
    var that = this;
    wx.request({
      url: config.machineUrl,
      data: { sn: that.data.sn, wid: wx.getStorageSync('jwt').wid,token:config.token},
      header: {
        'content-type': 'application/json'
      },
      success: function (res) {
        that.setData({
          sweety: res.data.sweety,
          machine: res.data.machine,
          props: res.data.props,
          timestamps: res.data.timestamps,
          dateTime: res.data.dateTime
        });
        console.log(res.data)
      },
      complete: function () {
        wx.hideNavigationBarLoading();
        wx.stopPullDownRefresh();
      }
    });
  },
  refresh: function(e) {
    var that = this;
    that.run(-1);
  },
  run: function (cmd) {
    var that = this;

    wx.showToast({
      title: '系统操作中',
      icon: 'loading',
      duration: 3000
    });

    wx.request({
      url: config.runUrl,
      data: { wid: wx.getStorageSync('jwt').wid,token:config.token, sn: that.data.sn, cmd: cmd},
      header: {
        'content-type': 'application/json'
      },
      success: function (res) {
        setTimeout(function () {
          that.updateData();
        }, 2000);
      }
    });
  },
  idle: function (e) {
    var that = this;
    that.run(0)
  },
  cold: function (e) {
    var that = this;
    that.run(1)
  },
  clean: function (e) {
    var that = this;
    that.run(2)
  },
  fresh: function (e) {
    var that = this;
    that.run(3)
  },
  thaw: function(e) {
    var that = this;
    that.run(4)
  },
  onPullDownRefresh: function () {
    this.updateData();
  },
   openRefrigerationfirm: function () {
     var that = this;
    wx.showModal({
      title: '',
      content: '您确认需要马上执行制冷？',
      confirmText: "确认",
      cancelText: "取消",
      success: function (res) {
        if (res.confirm) {
          that.cold();
          console.log('用户点击确定制冷')
        } else {
          console.log('用户点击取消制冷')
        }
      }
    });
  },
   openCleanfirm: function () {
     var that = this;
     wx.showModal({
       title: '',
       content: '您确认需要马上执行清洗？',
       confirmText: "确认",
       cancelText: "取消",
       success: function (res) {
         if (res.confirm) {
           that.clean();
           console.log('用户点击确定清洗')
         } else {
           console.log('用户点击取消清洗')
         }
       }
     });
   },
   openFreshfirm: function () {
     var that = this;
     wx.showModal({
       title: '',
       content: '您确认需要马上执行保鲜？',
       confirmText: "确认",
       cancelText: "取消",
       success: function (res) {
         if (res.confirm) {
           that.fresh();
           console.log('用户点击确定清洗')
         } else {
           console.log('用户点击取消清洗')
         }
       }
     });
   },
   openRefreshfirm: function () {
     var that = this;
     wx.showModal({
       title: '',
       content: '您确认需要马上执行刷新？',
       confirmText: "确认",
       cancelText: "取消",
       success: function (res) {
         if (res.confirm) {
           that.refresh();
           console.log('用户点击确定刷新')           
         } else {
           console.log('用户点击取消刷新')
         }
       }
     });
   },
   openStandbyfirm: function () {
     var that = this;
     wx.showModal({
       title: '',
       content: '您确认需要马上执行待机？',
       confirmText: "确认",
       cancelText: "取消",
       success: function (res) {
         if (res.confirm) {
           that.idle();
           console.log('用户点击确定待机')
         } else {
           console.log('用户点击取消待机')
         }
       }
     });
   },
   openThawfirm: function () {
     var that = this;
     wx.showModal({
       title: '',
       content: '您确认需要马上执行解冻？',
       confirmText: "确认",
       cancelText: "取消",
       success: function (res) {
         if (res.confirm) {
           that.thaw();
           console.log('用户点击确定解冻')
         } else {
           console.log('用户点击取消解冻')
         }
       }
     });
   }
})



