jQuery(function($){
	$(".topic_list li").hover(function(){
		$(this).addClass('h');
	},function(){
		$(this).removeClass('h');
	});
	
	$(".topic_list .tl_c .pic").hover(function(){
		var li = $(this).parent().parent();
		var html = $('.show_big_img',li).html();
		if(html.length > 10)
		{
			html = html.replace(/timgsrc/g,'src');
			html = '<div class="tl_pic_float">'+ html +'<i></i></div>';
			var offset = $(this).offset();
			var left = offset.left;
			var top = offset.top;
			$("body").append(html);
			$(".tl_pic_float").css({"top":top-128,"left":left-42});
		}
		
	},function(){
		$(".tl_pic_float").remove();
	});
});