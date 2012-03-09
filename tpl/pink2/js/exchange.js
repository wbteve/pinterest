var goodsID = 0;
var consigneeWebBox = null;
jQuery(function($){
	$(".exchangeBtn").click(function(){
		if(!$.Check_Login())
			return false;
		
		goodsID = this.getAttribute("goodsID");
		goodsType = this.getAttribute("goodsType");
		if(goodsType == 0)
		{
			var readyFun = function(weebox){
				weebox.dc.append($("#consignee"));
				$("#consignee").show();
				weebox.dc.height(weebox.dc.get(0).scrollHeight);
			};
			
			if(consigneeWebBox == null)
				consigneeWebBox = $.weeboxs.open('',{boxid:'exchange_box',contentType:'text',draggable:false,showButton:false,title:"商品兑换",width:591,onready:readyFun,isCloseToHide:true});
			else
				consigneeWebBox.show();
			$("#submitConsignee").unbind('click');
			$("#submitConsignee").bind('click',function(){
				consigneeWebBox.close();
				exchangeHandler(true);
			});
		}
		else
			exchangeHandler(false);
	});
});

function exchangeHandler(isConsignee)
{
	if(goodsID == 0)
		return false;
	
	var query = new Object();
	query.id = goodsID;
	
	if(isConsignee)
	{
		query.address = $("#c-address").val();
		query.email = $("#c-email").val();
		query.zip = $("#c-zip").val();
		query.mobile = $("#c-mobile-phone").val();
		query.fax = $("#c-fax-phone").val();
		query.fix = $("#c-fix-phone").val();
		query.qq = $("#c-qq").val();
		query.memo = $("#c-memo").val();
	}
	
	$.ajax({
		type:"POST",
		url: SITE_PATH+"services/service.php?m=exchange&a=submit",
		data:query,
		cache:false,
		dataType:"json",
		success: function(result){
			alert(result.msg);
		}
	});
}