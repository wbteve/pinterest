jQuery(function($){
	var max_day = 29;
	for(max_day; max_day > 1;max_day--)
	{
		$("#valid_time").append('<option value ="' + max_day + '">' + max_day + '天</option>');
	}
	
	$(".epi_text,.epi_content").focus(function() {
		$(this).siblings(".epi_err").hide();
		$(this).siblings(".epi_ok").hide();
		$(this).addClass("h");
		$(this).next(".epi_tip").show();
	});
	
	$(".epi_text,.epi_content").blur(function() {
		$(this).removeClass("h");
		$(this).next(".epi_tip").hide();
	});
	
	$("#secondTitle").blur(function(){
		checkTitle(this);
	});
	
	$("#secondContent").blur(function(){
		checkContent(this);
	}).focus(function(){
		var len = 1000 - $.getStringLength($.trim(this.value));
		$(this).next(".epi_tip").html('请输入宝贝描述内容，长度不能超过500个汉字<br>(您还可以输入<span class="ct_count">'+ len +'</span>个字符)。');
	}).keyup(function(){
		var len = 1000 - $.getStringLength($.trim(this.value));
		if(len < 0)
			return false;
		else
			$(".ct_count").html(len);
	});
	
	$("#secondPrice").blur(function(){
		checkPrice(this);
	});
	
	$("#secondFare").blur(function(){
		checkFare(this);
	});
	
	$("#secondForm").submit(function(){
		if(!checkTitle($("#secondTitle").get(0)))
			return false;
		
		if(!checkContent($("#secondContent").get(0)))
			return false;
			
		if(!checkPic())
			return false;	
			
		if(!checkPrice($("#secondPrice").get(0)))
			return false;
			
		if(!checkFare($("#secondFare").get(0)))
			return false;
	});
});

function PicItemCheckFun()
{
	checkPic();
}

function checkTitle(obj)
{
	var title = $.trim(obj.value);
	if(!$.checkRequire(title))
	{
		$(obj).siblings(".epi_err").html('请输入宝贝名称').show();
		return false;
	}
	
	if(!$.maxLength(title,40,true))
	{
		$(obj).siblings(".epi_err").html('宝贝名称必须小于20个汉字').show();
		return false;
	}
	
	$(obj).siblings(".epi_ok").show();
	return true;
}

function checkContent(obj)
{
	var content = $.trim(obj.value);
	if(!$.checkRequire(content))
	{
		$(obj).siblings(".epi_err").html('请输入宝贝描述').show();
		return false;
	}
	
	if(!$.maxLength(content,1000,true))
	{
		$(obj).siblings(".epi_err").html('宝贝描述必须小于500个汉字').show();
		return false;
	}
	
	$(obj).siblings(".epi_ok").show();
	return true;
}

function checkPrice(obj)
{
	var price = $.trim(obj.value);
	if(!$.checkRequire(price))
	{
		$(obj).siblings(".epi_err").html('请输入宝贝单价').show();
		return false;
	}
	
	if(!$.checkPrice(price))
	{
		$(obj).siblings(".epi_err").html('请输入正确的单价').show();
		return false;
	}
	
	$(obj).siblings(".epi_ok").show();
	return true;
}

function checkFare(obj)
{
	var price = $.trim(obj.value);
	if(!$.checkRequire(price))
	{
		$(obj).siblings(".epi_err").html('请输入运费').show();
		return false;
	}
	
	if(!$.checkPrice(price))
	{
		$(obj).siblings(".epi_err").html('请输入正确的运费').show();
		return false;
	}
	
	$(obj).siblings(".epi_ok").show();
	return true;
}

function checkPic()
{
	$("#secondImgs .epi_err").hide();
	$("#secondImgs .epi_ok").hide();
	
	if($(".PUB_SHARTE_PIC").length == 0)
	{
		$("#secondImgs .epi_err").html('请上传图片').show();
		return false;
	}
	
	$("#secondImgs .epi_ok").show();
	return true;
}