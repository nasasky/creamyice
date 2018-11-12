const config =require('../../config.js');
const app =getApp();
const signature =require('../../common/signature.js');
Page({
  data: {
    showTopTips: false,
    flag2: 5,
    checkboxItems: [
      { name: '口感不错', value: '0', checked: true },
      { name: '味道纯正', value: '1' },
      { name: '分量足', value: '2' },
      { name: '商家服务好', value: '3' },
      { name: '性价比高', value: '4' },
      { name: '购买方便', value: '5' }
    ],
    Items:[],
    ids:[],
    textarea:'',
    sn:'',
    orid:0,
    comments_name:''
  },
  /**
   * 监听页面加载
   */
  onLoad:function(options){
    if(signature.mo_login(app)){
      wx.request({
        url: config.commentUrl,
        data:{
          token:config.token,
          wid: wx.getStorageSync('wid') || app.globalData.account_id,
          type:'query'
        },
        success:(res)=>{
          this.setData({Items:res.data.items,ids:res.data.ids,sn:options.sn,orid:options.orid,comments_name:options.comments_name})
        }
      })

    }
  },
  /**
   * 发表评价事件
   */
  showTopTips: function () {
    let textarea =this.data.textarea;
    let ids =this.data.ids;
    let sn =this.data.sn;
    let orid =this.data.orid;
    let comments_name =this.data.comments_name;
    if(textarea == ''){
      wx.showToast({
        title: '请填写评论内容',
      })
      return false;
    }
    wx.request({
      url: config.commentUrl,
      data:{
        ids: JSON.stringify(ids),
        wid:app.globalData.account_id,
        text:this.data.textarea,
        token:config.token,
        type:'add',
        sn:sn,
        orid:orid,
        comments_name:comments_name
      },
      success:(res)=>{
        if(res.data.code == 0){
          wx.showToast({
            title: '感谢您的评价',
          })
          wx.switchTab({
            url: '/page/feature/feature',
          })
        }else{
          wx.showToast({
            title: '亲，服务器正忙！',
          })
        }
      }
    })
  },
  radioChange: function (e) {
    //因为是个多维数组所以需要外层的索引
    let str = e.detail.value;
    let strArr =str.split(",",2);
    let ids =this.data.ids;
    ids[strArr[1]] =strArr[0];
    var Items = this.data.Items;

    for (var i =0 ,len = Items.length;i < len;++i){
      for(var j =0,lenth = Items[i].length;j < lenth;++j){
        Items[strArr[1]][j].checked = Items[strArr[1]][j].id == strArr[0];
      }
    }

    this.setData({
      Items: Items,
      ids:ids
    });
  },
  textareaInput:function(e){
     this.setData({textarea:e.detail.value})
  }
});