/**
 * 小程序公共验证文件
 */
var signature = {
   checkLogin:()=>{
     let jwt = wx.getStorageSync('jwt');
     let d = new Date();
     let curtime = d.getTime() / 1000;
     if(jwt.wid && jwt.time > curtime){
       return true;
     }
     return false;
   },
   //返回日期格式为2018/04/20 08:23:40的时间
   getCustomDateTime:(rType)=>{
     let d=new Date();
     let year =d.getFullYear();
     let month =d.getMonth() + 1;
     if(month < 10) month ='0'+month;
     let day =d.getDate();
     if(day < 10) day ='0'+day;
     let hours =d.getHours();
     if(hours < 10) hours ='0'+hours;
     let minute =d.getMinutes();
     if(minute < 10) minute ='0'+minute;
     let seconds =d.getSeconds();
     if(seconds < 10) seconds ='0'+seconds;
     //根据参数判断返回数据格式
     if(rType == 'date'){
       return year+'/'+month+'/'+day;
     }
     if(rType == 'time'){
       return hours+':'+minute+':'+seconds;
     }
     if(rType == 'dateTime'){
       return year + '/' + month + '/' + day + ' ' + hours + ':' + minute + ':' + seconds;
     }
   }
}

module.exports = signature