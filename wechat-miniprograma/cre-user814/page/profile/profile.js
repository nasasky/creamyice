var sliderWidth = 96; // 需要设置slider的宽度，用于计算中间位置
const app =getApp();
const signature =require('../../common/signature.js');
const config =require('../../config.js');
Page({
  data: {
    tabs: ["全部订单", "未完成","已完成", "已评价"],
    activeIndex: 1,
    sliderOffset: 0,
    sliderLeft: 0,
    orders:true,
    display:false, //用户没有登录此页面不显示
    status:2, //默认已付款账单
    comment:false,
    hidden:true
  },
  onLoad: function () { 
    let that=this;  
    wx.getSystemInfo({
      success: function (res) {
        that.setData({
          sliderLeft: (res.windowWidth / that.data.tabs.length - sliderWidth) / 2,
          sliderOffset: res.windowWidth / that.data.tabs.length * that.data.activeIndex
        });
      }
    });
      this.getUserOrders(this.data.status);
  
  },
  /**
   * 获取用户账单
   */
  getUserOrders:function(status){
    //let status =this.data.status;
    this.setData({ hidden: false })
     wx.request({
       url: config.ordersUrl,
       data:{
         wid:wx.getStorageSync('wid'),
         token:config.token,
         status:status
       },
       success:(res)=>{
         if(res.data.code == 0){
           this.setData({ orders: res.data.orders,hidden:true })
         }else{
           this.setData({hidden:true,orders:false})
         }
       }
     })
  },
  /**
   * 点击tab选项卡事件
   */
  tabClick: function (e) {
    this.setData({
      sliderOffset: e.currentTarget.offsetLeft,
      activeIndex: e.currentTarget.id,
      status:parseInt( e.currentTarget.dataset.status_id ) + 1,
      orders:[],
      hidden:false
    });
    this.getUserOrders(parseInt( e.currentTarget.dataset.status_id )+ 1 );
  },
  /**
   * 当状态为已评价时，点击查看评价详情事件
   */
  queryCommetnsDetail:function(event){
      wx.navigateTo({
        url: '/page/evaluate/evaluate',
      })
  }
});