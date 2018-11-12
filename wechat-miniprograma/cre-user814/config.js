/**
 * 小程序配置文件
 */
var host = "https://partner.creamyice.com/ver3"

var config = {

  // 下面的地址配合云端 Server 工作
  host,

  // 登录地址，用于建立会话
  loginUrl: `${host}/customer.php`,
  
  // 查询商店列表页面用于首页展示
  storeUrl: `${host}/store.php`,
  storeDetailUrl:`${host}/store_detail.php`,
  
  // 测试的请求地址，用于测试会话
  requestUrl: `${host}/testRequest.php`,
  checkPayUrl: `${host}/check_pay.php`,
  // 测试的信道服务接口
  tunnelUrl: `${host}/tunnel.php`,

  // 生成支付订单的接口
  paymentUrl: `${host}/wlpay.php`,

  // 发送模板消息接口
  templateMessageUrl: `${host}/templateMessage.php`,

  // 上传文件接口
  uploadFileUrl: `${host}/upload.php`,

  // 下载示例图片接口
  downloadExampleUrl: `${host}/ice_shopping.png`,

  //评论接口
  commentUrl: `${host}/comment.php`,

  //查询用户订单接口
  ordersUrl: `${host}/orders.php`,

  //查询用户优惠券接口
  couponUrl:`${host}/coupon.php`,
  sendCouponUrl:`${host}/sendCoupon.php`,
  
  //MD5（wanglong）用于接口加密校验
  token:'19837e4d1739579b41f5a76de9b555c8'
};

module.exports = config
