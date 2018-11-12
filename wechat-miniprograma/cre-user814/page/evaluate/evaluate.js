const config =require('../../config.js');
const app =getApp();
var sliderWidth = 96; // 需要设置slider的宽度，用于计算中间位置

Page({
  data: {
    tabs: ["全部(562)", "口感不错(562)", "味道纯正(1212)", "分量足(1212)", "商家服务好(1212)", "性价比高(12121)", "购买方便(1121)",  "口感一般(11212)"],
    activeIndex: 0,
    sliderOffset: 0,
    sliderLeft: 0,
    page:1,
    size:10,
    count:9999,
    comments:[],
    sn:'',
    citemsid:0,
    head_img:'',
    hidden:true
  },
  onLoad: function (options) {
    var sn = options.sn || 0;
    this.setData({ sn: sn, head_img: app.globalData.userInfo})
    var that = this;
    wx.getSystemInfo({
      success: function (res) {
        that.setData({
          sliderLeft: (res.windowWidth / that.data.tabs.length - sliderWidth) / 2,
          sliderOffset: res.windowWidth / that.data.tabs.length * that.data.activeIndex
        });
      }
    });
    this.getCommentsItems(sn);
    this.getCommentsDetails(0,sn,this.data.page,this.data.size);
  },
  /**
   * 获取评论选项卡conmments_items
   */
  getCommentsItems:function(sn){
     wx.request({
       url: config.commentUrl,
       data:{
         token:config.token,
         wid:wx.getStorageSync('wid') || -1,
         type:'query',
         dtype:1,
         sn:sn
       },
       success:(res)=>{
         if(res.data.code == 0){
           this.setData({tabs:res.data.items})
         }else{
           wx.showToast({
             title: '服务器正忙',
             success:(res)=>{
               wx.navigateBack({
                 delta:1
               })
             }
           })
         }
       }
     })
  },
  /**
   * 获取所有评论详情列表
   */
  getCommentsDetails:function(id,sn,page,size){
    var oldComments = this.data.comments;
    this.setData({hidden:false})
     wx.request({
       url: config.commentUrl,
       data:{
         token:config.token,
         wid:wx.getStorageSync('wid') || -1,
         selects_id:id,
         sn:sn,
         page:page,
         size:size,
         type:'selectall'
       },
       success:(res)=>{
         if(res.data.code == 0){
           this.setData({comments:oldComments.concat(res.data.comments),page:page,hidden:true})
         }else{
           this.setData({hidden:true})
         }
       }
     })
  },
  /**
   * tab切换事件
   */
  tabClick: function (e) {
    console.log(e)
    this.setData({
      sliderOffset: e.currentTarget.offsetLeft,
      activeIndex: e.currentTarget.id,
      citemsid:e.currentTarget.dataset.citemsid,
      page:1,
      comments:[]
    });
    this.getCommentsDetails(e.currentTarget.dataset.citemsid,this.data.sn,this.data.page,this.data.size)
  },
  /**
   * 上拉触底加载更多评论
   */
  onReachBottom:function(){
    let page =this.data.page;
    let nextPage =parseInt(page)+1;
    let size =this.data.size;
    let citemsid =this.data.citemsid;
    this.getCommentsDetails(citemsid,this.data.sn,nextPage,size);
  }
});