/**
 * 小程序配置文件
 */
var host = "https://partner.creamyice.com/ver3"

var config = {

    // 下面的地址配合云端 Server 工作
    host,

    // 登录地址，用于建立会话
    loginUrl: `${host}/login.php`,
    
    //甜蜜度接口
    sweetyUrl: `${host}/sweety.php`,

    //账单及账单详情接口
    billUrl:`${host}/stat.php`,
    billDetailUrl:`${host}/stat_detail.php`,
    
    //融合对账单
    fuseUrl:`${host}/fuse.php`,
    fuseDetailUrl:`${host}/fuse_detail.php`,
    
    //商户获取取单号
    numbersUrl:`${host}/numbers.php`,
    productUrl:`${host}/product.php`,

    //获取店铺机器及扫码绑定系统
    machineUrl:`${host}/machine.php`,
    //补货地址
    replenishmentUrl: `${host}/replenishment.php`,
    //收货地址页面
    materialUrl:`${host}/material.php`,
    //run.php
    runUrl:`${host}/run.php`,
    //修改名称
    namingUrl:`${host}/naming.php`,
    //调用接口token令牌
    token: '19837e4d1739579b41f5a76de9b555c8',
};

module.exports = config
