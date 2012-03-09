jQuery(function($){
	$(".module-item").change(function(){
		var parent = $(this).parent().parent().parent().parent();
		if(this.checked)
		{
			$('.select-all,.action-item',parent).attr({'disabled':true,'checked':false});
		}
		else
		{
			$('.select-all,.action-item',parent).attr({'disabled':false});
		}
	});
	
	$(".select-all").change(function(){
		var parent = $(this).parent().parent().parent();
		if(this.checked)
		{
			$('.action-item',parent).attr({'checked':true});
		}
		else
		{
			$('.action-item',parent).attr({'checked':false});
		}
	});
	
	$(".action-item").change(function(){
		var parent = $(this).parent().parent().parent();
		if($(".action-item:not([checked])",parent).length == 0)
		{
			$('.select-all',parent).attr({'checked':true});
		}
		else
		{
			$('.select-all',parent).attr({'checked':false});
		}
	});
});