$(function(){
 	/*弹出框popover触发*/
      $(".popover-js").popover();
      $(".tooltip-js").tooltip();

      /*获取省份*/
      $('#getProvince').click(
            function(){
                  var div=$('#downlist');
                  
                  /*增加ul的dropdown-menu*/
                  var button=$(this);
                  var ul="<ul class='dropdown-menu' role='menu' id='first-list'></ul>";
                  button.after(ul);

                  /*通过id获取上面的ul对象*/
                  var firstList=$('#first-list');

                  $.ajax({
                        type:"POST",
                        dataType:"json",
                        url:"./index.php?r=city/getProvince",
                        success:function(data){
                              $.each(data,function(n,value){
                                    var li="<li class='dropdown-submenu jsProvince'  value='"+value.cid+"' ><a href='#' >"+value.name+"</a></li>";
                                    firstList.append(li);
                              })
                        }

                  })
            }
      )

      /*获取市级列表*/
      $(document).on('mouseenter','.jsProvince',function(){
            if($(this).has('#second-list')){
                  $('#second-list').remove();
            }
            var ul="<ul class='dropdown-menu' role='menu' id='second-list'></ul>";
            $(this).append(ul);
            var secondList=$('#second-list');

            $province_id=$(this).attr("value");
            $.ajax({
                  type:"POST",
                  data:{province_id:$province_id},
                  dataType:"json",
                  url:"./index.php?r=city/getCity",
                  success:function(data){
                       $.each(data,function(n,value){
                             var li="<li class='dropdown-submenu jsCity'  value='"+value.cid+"' ><a href='#' >"+value.name+"</a></li>";
                             secondList.append(li);
                         })
                  }
            })
            }
      )

      /*获取区级列表*/
      $(document).on('mouseenter','.jsCity',function(){
            if($(this).has("#third-list")){
                  $('#third-list').remove();
            }
            var ul="<ul class='dropdown-menu' role='menu' id='third-list'></ul>";
            $(this).append(ul);
            var thirdList=$('#third-list');

            $city_id=$(this).attr('value');
             $.ajax({
                  type:"POST",
                  data:{city_id:$city_id},
                  dataType:"json",
                  url:"./index.php?r=city/getCounty",
                  success:function(data){
                       $.each(data,function(n,value){
                             var li="<li class='dropdown-submenu jsCounty'  value='"+value.cid+"' ><a href='#' >"+value.name+"</a></li>";
                             thirdList.append(li);
                         })
                  }
            })

      })
 })
