jQuery(function($){
	$("#albumTitle,#albumTags,#albumContent").focus(function() {
		$(this).siblings(".error_icon").hide();
		$(this).siblings(".ok_icon").hide();
		$(this).addClass("h");
		$(this).next(".info_tip").show();
	});
	
	$("#albumTitle,#albumTags,#albumContent").blur(function() {
		$(this).removeClass("h");
		$(this).next(".info_tip").hide();
	});
	
	$("#albumTitle").blur(function(){
		checkTitle(this);
	});
	
	$("#albumContent").blur(function(){
		checkContent(this);
	}).focus(function(){
		var len = 1000 - $.getStringLength($.trim(this.value));
		$(this).next(".info_tip").html('介绍内容长度不能超过500个汉字<br>(您还可以输入<span class="ct_count">'+ len +'</span>个字符)。');
	}).keyup(function(){
		var len = 1000 - $.getStringLength($.trim(this.value));
		if(len < 0)
			return false;
		else
			$(".ct_count").html(len);
	});
	
	$("#albumTags").blur(function(){
		checkTags(this);
	});
	
	$(".fashion_element span").click(function(){
		var tagInput = $('#albumTags');
		var tagValue = tagInput.val();
		tagValue = tagValue.replace('　',' ');
		tagValue = tagValue.replace(/ +/g,' ');
		tagValue = ' ' + $.trim(tagValue) + ' ';
		if($(this).hasClass('active'))
		{
			tagValue = tagValue.replace(' ' + $(this).html() + ' ',' ');
			$(this).removeClass('active');
		}
		else
		{
			tagValue += $(this).html();
			$(this).addClass('active');
		}
		
		tagValue = $.trim(tagValue);
		tagInput.val(tagValue);
		checkTags(tagInput.get(0));
	});
	
	$("#albumForm").submit(function(){
		if(!checkTitle($("#albumTitle").get(0)))
			return false;
			
		if(!checkTags($("#albumTags").get(0)))
			return false;
		
		if(!checkContent($("#albumContent").get(0)))
			return false;
	});
});

function AlbumUpdateUserFollow(obj,result)
{
	var parent = $(obj).parent();
	if(result.status == 1)
	{
		parent.html('<span class="followed">已关注</span>');
	}
}

function checkTitle(obj)
{
	var title = $.trim(obj.value);
	if(!$.checkRequire(title))
	{
		$(obj).siblings(".error_icon").html('请输入标题').show();
		return false;
	}
	
	if(!$.maxLength(title,60,true))
	{
		$(obj).siblings(".error_icon").html('标题必须小于30个汉字').show();
		return false;
	}
	
	$(obj).siblings(".ok_icon").show();
	return true;
}

function checkContent(obj)
{
	var content = $.trim(obj.value);
	if(!$.maxLength(content,1000,true))
	{
		$(obj).siblings(".error_icon").html('介绍必须小于500个汉字').show();
		return false;
	}
	
	if($.checkRequire(content))
		$(obj).siblings(".ok_icon").show();
	return true;
}

function checkTags(obj)
{
	$(obj).siblings(".ok_icon").hide();
	$(obj).siblings(".error_icon").hide();
	var maxCount = obj.getAttribute("maxCount");
	var tags = $.trim(obj.value);
	
	tags = tags.replace('　',' ');
	tags = tags.replace(/ +/g,' ');
	tagValue = ' ' + $.trim(tags) + ' ';
	tags = $.trim(tags).split(' ');
	
	$(".fashion_element span").each(function(){
		if(tagValue.indexOf(' ' + $(this).html() + ' ') > -1)
			$(this).addClass('active');
		else
			$(this).removeClass('active');
	});
	
	if(tags.length > maxCount)
	{
		$(obj).siblings(".error_icon").html('时尚元素最多可设置'+ maxCount +'个').show();
		return false;
	}
	
	if($.checkRequire(tags))
		$(obj).siblings(".ok_icon").show();
	return true;
}