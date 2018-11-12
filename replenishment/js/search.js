$(function(){
      $('#btnNumber').click(function(){
    var number = $('#byNumber').val();
    if(number.length == 10){
      $.get('./search.php', {type:'number',number:number}, function(res){
        if(res.code == 0 && res.list.constructor.toString().indexOf('Object') > -1){
          var list = res.list;
          var htmls = getHtmls(list);
          $('tbody').html(htmls);
          $('#paginate').html('<ul class="pagination"><li><a href="#" style="color: green;font-weight:bold;">1</a></li></ul>')
        }else{
          $('tbody').html('<tr class="odd gradeX"><td align="center" colspan="10">Sorry search orders by number and return result is empty</td></tr>')
          $('#paginate').html('<ul class="pagination"><li><a href="#" style="color: green;font-weight:bold;">1</a></li></ul>')
        }
      },'json')
    }else{
      alert('order number parameter illegality!')
    }
  })
  $('#dateButton').click(function(){
    var dateStart = $('#dateStart').val();
    if(dateStart == ''){
      alert('查询起始日期不能为空');
      return false;
    }
    var dateEnd = $('#dateEnd').val();
    if(dateEnd  == ''){
      alert('查询截至日期不能为空');
       return false;
    }
    var startTime = new Date(dateStart);
    var endTime = new Date(dateEnd);
    if( startTime.getTime() > endTime.getTime() ){
      alert('搜索截至日期必须大于或等于搜索日期');
      return false;
    }
    $.get('./search.php', {type:'date',start:dateStart,end:dateEnd}, function(res){
      console.log(res)
      if(res.code == 0 && res.list.length > 0){
        var list = res.list;
        var htmls;
        for(var i=0,len=list.length;i<len;i++){
          htmls += getHtmls(list[i]);
        }
        $('tbody').html(htmls);
        $('#paginate').html('<ul class="pagination"><li><a href="#" style="color: green;font-weight:bold;">1</a></li></ul>')
      }else{
        $('tbody').html('<tr class="odd gradeX"><td align="center" colspan="10">Sorry search orders by date and return result is empty</td></tr>')
        $('#paginate').html('<ul class="pagination"><li><a href="#" style="color: green;font-weight:bold;">1</a></li></ul>')
      }
    },'json')
  })
  
  function getHtmls(list){
    var status,details,button,htmls;
    if(list.status < 3){
       details = '<a onclick=\"details('+list.wid+','+list.id+',\''+list.cour_date+'\')\" onmouseleave="divHide()" id=\"det_'+list.id+'\">'+list.detail+'</a>';
    }else{
        details = '<span id=\"det_'+list.id+'\">'+list.detail+'</span>';
    }
    switch(list.status){
         case '1':
         status = '<span style="color:red;">订单申请</span>';
         break;
         case '2':
         status = '<span style="color:#13F9ED;">审核通过</span>';
         break;
         case '3':
         status = '<span style="color:#57D1E7;">正在配送</span>';
         break;
         case '4':
         status = '订单完成';
         break;
         default:
         status = '订单异常';
         break;
    }
    if(list.status == 1) {
      button ='<a href="modify-replenishment.php?repid='+list.id+'" style="padding-right:5px;"><button class="btn btn-danger" type="button">待审核</button></a>';
    }else if(list.status == 2){
      button = '<a href="modify-replenishment.php?repid='+list.id+'" style="padding-right:5px;"><button class="btn" style="background-color:#13F9ED;" type="button">待配送</button></a>';
    }else if(list.status == 3) {
      button = '<button class="btn" style="background-color: #787182;" type="button">配送中...</button>';
    } else {
      button ='<button class="btn" type="button">已完成</button>';
    }
    htmls ='<tr class="odd gradeX"><td><input type="checkbox" class="select-down" value="'+list.id+'" /></td><td style="line-height: 30px;">'+list.number+'</td><td style="line-height: 30px;">'+status+'</td><td style="line-height: 30px;">'+list.cour_date+'</td><td style="line-height: 30px;">'+list.store+'</td><td class="hidden-phone" style="line-height: 30px;">'+details+'</td><td class="hidden-phone" style="line-height: 30px;">'+list.message+'</td><td class="hidden-phone" style="line-height: 30px;">'+list.name+'</td><td class="center hidden-phone" style="line-height: 30px;">'+list.telephone+'</td><td class="hidden-phone" style="line-height: 30px;">address</td><td class="hidden-phone" style="line-height: 30px;">apply_date</td><td class="hidden-phone" style="line-height: 30px;">'+button+'</td></tr>';
    return htmls;
  }
})