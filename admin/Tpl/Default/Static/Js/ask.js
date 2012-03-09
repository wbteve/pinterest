$(document).ready(function(){
	$("form").bind("submit",function(){
		if($("input[name='name']").val()=='')
		{
			alert(NAME_EMPTY_TIP);
			$("input[name='name']").focus();
			return false;
		}
	});
});