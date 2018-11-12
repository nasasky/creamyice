var config = require('../../../../config');
var app = getApp();
Page({
  data: {
    company:'',
    name:'',
    mobile:'',
    address:'',
    material:[],
    machines:[],
    machinesIndex:0,
    radioItems: [
      { name: '星期二', checked: 'true'},
      { name: '星期五', }
    ],
    radioSelect:''
  },

  /**
   * 通过当前日期设置配送日期周二和周五
   */
  setDate:function(query){
    var my_date =new Date();
    var day =my_date.getDay();
    //var query = 5; // 我要查的是星期五
    var offset = query - day; // 计算出差几天
    offset = offset <= 0 ? offset + 7 : offset;

    my_date = my_date.getTime(); // 拿到当前的时间戳

    var future = my_date + offset * 24 * 3600 * 1000; // 计算出星期五的时间戳

    future = new Date(future); // 转成时间对象
    
    var futureYear =future.getFullYear();
    var futureMonth = future.getMonth() + 1; // 获取星期五的月份
    var futureDate = future.getDate();      // 获取星期五的日期

    futureDate = futureDate < 10 ? '0' + futureDate : futureDate.toString(); // 如果日期小于10则补个0
    var date = futureYear+'-'+futureMonth+'-'+futureDate;
    return date;
  },
  /**
   * onLoad监听页面初次启动
   */
  onLoad:function(e){
     this.getMaterial();
     var tuesday =this.setDate(2);
     var friday =this.setDate(5);
     this.setData({
        radioItems:[
          {name:'星期二'+'  '+tuesday,value:0,checked:'true'},
          {name:'星期五'+'  '+friday,value:1}
        ],
        radioSelect:'星期二'+'  '+tuesday
     })
  },

  /**
   * 获取配料种类列表
   */
  getMaterial:function(){
    var that =this;
    wx.request({
      url: config.materialUrl,
      data: {
        wid: wx.getStorageSync('jwt').wid,
        token:config.token
      },
      success: function (res) {
        that.setData({
          material:res.data.material,
          machines:res.data.machines
        });
      }
    });
  },
  /** 获取输入框选择框内容开始 */
  bindCountryChange: function (e) {
    let curindex = e.target.dataset.current;
    this.data.material[curindex].index = e.detail.value;
    this.setData({
      material: this.data.material
    });
  },
  bindCompanyName: function(e){
     this.setData({company:e.detail.value});
  },
  //获取单选按钮的配送日期事件
  radioChange: function (e) {
    this.setData({
      radioSelect:e.detail.value
    })
  },
  bindName:function(e){
    this.setData({ name: e.detail.value });
  },
  bindMobile:function(e){
    this.setData({mobile: e.detail.value });
  },
  bindAddress:function(e){
    this.setData({address: e.detail.value });
  },
  bindSelect:function(e){
    this.setData({
      machinesIndex:e.detail.value
    })
  },
/**  获取内容结束 */

  /** 确定补货提交事件 */
   openAlert: function () {
    if (this.data.name == '') {
      wx.showToast({
        title: '请填写收货人',
        icon: 'none',
        duration: 1000,
        success: function (res) { },
        fail: function (res) { },
        complete: function (res) { },
      });
      return false;
    };
    if (this.data.mobile == '' || this.data.mobile.length < 8 || this.data.mobile.length > 12) {
      wx.showToast({
        title: '手机号不正确',
        icon: 'none',
        duration: 1000,
        success: function (res) { },
        fail: function (res) { },
        complete: function (res) { },
      });
      return false;
    };
    if (this.data.address == '') {
      wx.showToast({
        title: '请输入收货地址',
        icon: 'none',
        duration: 1000,
        success: function (res) { },
        fail: function (res) { },
        complete: function (res) { },
      });
      return false;
    };
    var data =this.data;
    wx.showModal({
      content: '你确定要补货吗？',
      success: function (res) {
        /**
         * 用户确定补货和取消补货
         */
        if (res.confirm) {
          var myDate = new Date();
          wx.request({
            url:  config.replenishmentUrl +'?wid=' + wx.getStorageSync('jwt').wid+'&token='+config.token,
            data: {
              wid: wx.getStorageSync('jwt').wid, name: data.name, company: data.company, 
              store: data.machines[data.machinesIndex].name,
              address: data.address, mobile: data.mobile, material: JSON.stringify(data.material),date:data.radioSelect
            },
            header: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            method:'POST',
            success: function (res) {
              wx.showToast({
                title: res.data.msg,
                icon: 'success',
                duration: 2000
              });
              if(res.data.code == 0){
                 wx.navigateTo({
                   url: '/page/ice/pages/replenishment-list/replenishment-list',
                 })
              }
            }
          });
        }else{
          wx.switchTab({
            url: '/page/ice/index',
          })
        }
      }
    });
  }
});