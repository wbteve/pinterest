$(document).ready(function(){

	$("form").bind("submit",function(){
		if($.trim($("input[name='shop_name']").val())=='')
		{
			alert(NAME_EMPTY_TIP);
			$("input[name='shop_name']").focus();
			return false;
		}
		if($.trim($("input[name='shop_url']").val())=='')
		{
			alert(URL_EMPTY_TIP);
			$("input[name='shop_url']").focus();
			return false;
		}
	});

});