/*后台登陆*/
$(function(){
	
	$.each($("input"),function(){
		var obj = $(this);
		obj.mouseover(function(){obj.addClass('onFocus')}).mouseout(function(){obj.removeClass('onFocus')});
		});	
	var checkObj = $("#checkImage");
	checkObj.mouseover(function(){checkObj.addClass('onFocus')}).mouseout(function(){checkObj.removeClass('onFocus')});
	});
	
/*左边菜单js*/
(function($){   
$.fn.leftMenu=function(options){
var defaults = {
	sibling:'h4',
	nextSibling:'ul',
	};
	var opt = $.extend(defaults,options);
	var $currentThis = $(this);
	return $currentThis.find(opt.sibling).each(function(){
		var $this = $(this);
		$this.click(function(e){
			e.preventDefault();
			$currentThis.find(opt.sibling).removeClass('on');
			$currentThis.find(opt.nextSibling).slideUp();
			$this.addClass("on");
			$this.next().slideDown();
		});
		$this.next().find('a').click(function(){
				$this.next().find('li').removeClass('on');
				$(this).parent().addClass('on');
		});
	});
};
})(jQuery); 
  
$(function() {
	var browser=navigator.appName;
	$("#cleft-id").leftMenu();
	resizeWindows('#bezel-id');
});
function outLogin(){
	location.href='login.html';
} 

/* 自动适应窗体高度  */
function resizeWindows(id){
	var obj = $(id);
	var mainHeight = obj.height();
	//-parseInt($("#"+id).css("margin-top"))*2
	//获取主题的边框高度
	var boderWidth = isNaN(parseInt(obj.css("border-width")))? 0:parseInt(obj.css("border-width"));
	//获取~上外边距
	var marginTop = isNaN(parseInt(obj.css("margin-top")))? 0:parseInt(obj.css("margin-top"));
	//获取~下外边距
	var marginBottom =isNaN(parseInt(obj.css("margin-bottom")))? 0:parseInt(obj.css("margin-bottom"));
	var lastHeight = $(window).height()-boderWidth-marginTop-marginBottom;
	if(mainHeight>=lastHeight){
		lastHeight = mainHeight;
	}
	obj.height(lastHeight);
}

/*处理modal删除框*/ 
function dodel(id){
	$('#showDel').attr('href',delUserUrl+"&id="+id);
	
}