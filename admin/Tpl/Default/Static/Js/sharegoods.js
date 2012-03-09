$(document).ready(function(){
	$("input[name='share_btn']").bind("click",function(){
		var kw = $("input[name='search_shop']").val();	
		if($.trim(kw)=='')
		{
			alert(PLEASE_INPUT_KW);
			return;
		}
		$.ajax({
			url: APP+'?'+VAR_MODULE+'='+CURR_MODULE+'&'+VAR_ACTION+'=searchShop&kw='+kw,
			type:"POST",
			cache: false,
			dataType:"json",
			success: function(result){
				if(result.shop_count==0)
				{
					alert(NO_SEARCH_SHOP);
				}
				else if(result.shop_count>20)
				{
					alert(RESULT_TOO_MUCH);
				}
				else
				{
					var rs = "<option value='0'>"+NO_SHOP+"</option>";
					for(i=0;i<result.shop_list.length;i++)
					{
						rs+="<option value='"+result.shop_list[i]['shop_id']+"'>"+result.shop_list[i]['shop_name']+"</option>";
					}
					$("select[name='shop_id']").html(rs);
				}
			}
		});
		//搜索
	});
});