/**
 * 小程序公用登录验证文件
 */

var signature = {
  mo_login: function(app){
      let wid =wx.getStorageSync('wid');
      if (!app.globalData.hasMobileBind) {
        wx.showModal({
          title: '提示',
          content: '请先去登陆',
          success: (res) => {
            if (res.confirm) {
              wx.redirectTo({
                url: '/page/sign-in/sign-in',
              })
            } else {
              wx.switchTab({
                url: '/page/feature/feature',
              })
            }
          }
        })
        return false;
      }
      return true;
  },
  custom_setStorage:function(k, v, t) {
    wx.setStorageSync(k, v)
    var seconds = parseInt(t);
    if (seconds >0) {
      var timestamp = Date.parse(new Date());
      timestamp = timestamp / 1000 + seconds;
      wx.setStorageSync(k + dtime, timestamp + "")
    } else {
      wx.removeStorageSync(k + dtime)
    }
  },
  custom_getStorage: function(k, def) {
    var deadtime = parseInt(wx.getStorageSync(k + dtime))
    if (deadtime) {
      if (parseInt(deadtime) < Date.parse(new Date()) /1000) {
        if (def) { return def; }
        else { return; }
      }
    }
    var res = wx.getStorageSync(k);
    if (res) {
      return res;
    } else {
      return def;
    }
  }

}

module.exports = signature;