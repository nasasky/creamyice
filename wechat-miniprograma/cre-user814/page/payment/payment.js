const config =require('../../config.js');
const app =getApp();
Page({
  data: {
    img:'',
    sn:'',
    addr:'',
    name:'',
    price:12,
    zone:'安徽',
    description:'',
    total_price:12,
    address:'',
    store:'',
    // input默认是1  
    num: 1,
    // 使用data数据对象设置样式名  
    minusStatus: 'disabled',
    plusStatus:'normal',
    showTopTips: false,

    radioItems: [
      { name: '微信支付', value: '1', checked: true }
    ],
    checkboxItems: [
      { name: '￥12 郁金香雪吻', value: '0', checked: true },
      { name: '￥18 海牙圣杯', value: '1' }
    ],

    date: "2016-09-01",
    time: "12:01",

    countryCodes: ["+86", "+80", "+84", "+87"],
    countryCodeIndex: 0,

    countries: ["中国", "美国", "英国"],
    countryIndex: 0,

    accounts: ["微信号", "QQ", "Email"],
    accountIndex: 0,

    isAgree: false
  },
  /**
   * onLoad 监听页面初次加载
   */
  onLoad:function(options){
    let pro =options;
    this.setData({
      img: 'https://'+config.host+pro.img,
      sn: pro.sn,
      addr: pro.addr,
      name: pro.name,
      price: pro.price,
      total_price:pro.price,
      description:pro.description,
      address:pro.address,
      store:pro.store,
      zone:pro.zone
    })
  },
  /* 点击减号 */
  bindMinus: function () {
    var num = this.data.num;
    var price =parseInt(this.data.price);
    // 如果大于1时，才可以减  
    if (num > 1) {
      num--;
    }
    // 只有大于一件的时候，才能normal状态，否则disable状态  
    var minusStatus = num <= 1 ? 'disabled' : 'normal';
    var plusStatus = num >4 ? 'disabled' : 'normal';
    // 将数值与状态写回  
    this.setData({
      num: num,
      minusStatus: minusStatus,
      plusStatus:plusStatus,
      total_price:price*num
    });
  },
  /* 点击加号 */
  bindPlus: function () {
    var num = this.data.num;
    var price =parseInt(this.data.price);
    // 不作过多考虑自增1  
    if(num < 4){
      num++;
    }
    // 只有大于一件的时候，才能normal状态，否则disable状态  
    var plusStatus = num > 3 ? 'disabled' : 'normal';
    var minusStatus = num <=1?'disabled':'normal';
    // 将数值与状态写回  
    this.setData({
      num: num,
      plusStatus: plusStatus,
      minusStatus:minusStatus,
      total_price:price*num
    });
  },
  /* 输入框事件 */
  bindManual: function (e) {
    var num = parseInt(e.detail.value);
    var price =parseInt(this.data.price);
    if(num < 1) num =1;
    if(num > 3) num =4;
    // 将数值与状态写回  
    this.setData({
      num: num,
      total_price:num*price
    });
  }
});
