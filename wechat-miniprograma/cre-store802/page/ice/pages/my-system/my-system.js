var config = require('../../../../config');
Page({
  openToast: function () {
    wx.showToast({
      title: '已通过',
      icon: 'success',
      duration: 3000
    });
  },
  openToast2: function () {
    wx.showToast({
      title: '已拒绝',
      icon: 'success',
      duration: 3000
    });
  },
  openToast3: function () {
    wx.showToast({
      title: '已禁用',
      icon: 'success',
      duration: 3000
    });
  },
  openToast4: function () {
    wx.showToast({
      title: '已删除',
      icon: 'success',
      duration: 3000
    });
  },
  openLoading: function () {
    wx.showToast({
      title: '数据加载中',
      icon: 'loading',
      duration: 3000
    });
  }
});
