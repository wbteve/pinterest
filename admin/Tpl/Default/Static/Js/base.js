var ajaxError = false;

(function($){
	$.ajaxError = function(msg){
		ajaxError = true;
		if(!msg)
			msg = AJAX_ERROR;
		$(".ajax-loading").html(msg).addClass("ajax-error").stop(true).show();
		$(".ajax-loading").css({"opacity":1}).fadeOut(3000);
	};
	
	$.getStringLength=function(str)
	{
		str = $.trim(str);
		
		if(str=="")
			return 0; 
			
		var length=0; 
		for(var i=0;i <str.length;i++) 
		{ 
			if(str.charCodeAt(i)>255)
				length+=2; 
			else
				length++; 
		}
		
		return length;
	}
	
	$.getLengthString=function(str,length,isSpace)
	{
		if(arguments.length < 3)
			var isSpace = true; 
		
		if($.trim(str)=="")
			return "";
		
		var tempStr="";
		var strLength = 0;
		
		for(var i=0;i <str.length;i++) 
		{
			if(str.charCodeAt(i)>255)
				strLength+=2;
			else
			{
				if(str.charAt(i) == " ")
				{
					if(	isSpace)
						strLength++;	
				}
				else
					strLength++;
			}
				
			if(length >= strLength)
				tempStr += str.charAt(i);
		}
		
		return tempStr;
	}
	
	$.windowCenter=function(obj)
	{
		var windowWH = $.getWindowWH();
		var windowWidth=windowWH[0];
		var windowHeight=windowWH[1];
		var objWidth=obj.width();
		var objHeight=obj.height();
		var objTop=tooltipTop + $.getBodyScrollTop();
		var objLeft=(windowWidth - objWidth ) / 2;
		obj.css({position:"absolute",display:"block","z-index":1000,top:objTop,left:objLeft});
	}
	
	$.getBodyScrollTop=function(){
        var scrollPos; 
        if (typeof window.pageYOffset != 'undefined') { 
            scrollPos = window.pageYOffset; 
        } 
        else if (typeof document.compatMode != 'undefined' && 
            document.compatMode != 'BackCompat') { 
            scrollPos = document.documentElement.scrollTop; 
        } 
        else if (typeof document.body != 'undefined') { 
            scrollPos = document.body.scrollTop; 
        } 
        return scrollPos;
    }
	
	$.getWindowWH = function(){
		var width=$.support.cssFloat ? $(document.documentElement).width() : $(window).width();
		var height=$.support.cssFloat ? $(document.documentElement).height() : $(document).height();
		return [width,height];
	}
	
	$.checkRequire = function(value){
		var reg = /.+/;
        return reg.test($.trim(value));
	}
	
	$.minLength = function(value, length , isByte) {
		var strLength = $.trim(value).length;
		if(isByte)
			strLength = $.getStringLength(value);
			
		return strLength >= length;
	};
	
	$.maxLength = function(value, length , isByte) {
		var strLength = $.trim(value).length;
		if(isByte)
			strLength = $.getStringLength(value);
			
		return strLength <= length;
	};
	
	$.rangeLength = function(value, minLength,maxLength, isByte) {
		var strLength = $.trim(value).length;
		if(isByte)
			strLength = $.getStringLength(value);
			
		return strLength >= minLength && strLength <= maxLength;
	}
	
	$.checkMobilePhone = function(value){
		return /^(13\d{9}|18\d{9}|15\d{9})$/i.test($.trim(value));
	}
	
	$.checkPhone = function(val){
  		var flag = 0;
		val = $.trim(val);
  		var num = ".0123456789/-()";
  		for(var i = 0; i < (val.length); i++)
		{
    		tmp = val.substring(i, i + 1);
    		if(num.indexOf(tmp) < 0)
      			flag++;
 		}
  		if(flag > 0)
			return true;
		else
			return false;
	}
	
	$.checkEmail = function(val){
		var reg = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/; 
		return reg.test(val);
	};
	
	$.checkUrl = function(val){
		var reg = /^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/; 
        return reg.test(val);
	};
	
	$.checkCurrency = function(val){
		var reg = /^\d+(\.\d+)?$/; 
        return reg.test(val);
	};
	
	$.checkNumber = function(val){
		var reg = /^\d+$/; 
        return reg.test(val);
	};
	
	$.checkInteger = function(val){
		var reg = /^[-\+]?\d+$/; 
        return reg.test(val);
	};
	
	$.checkDouble = function(val){
		var reg = /^[-\+]?\d+(\.\d+)?$/; 
        return reg.test(val);
	};
	
	$.checkEnglish = function(val){
		var reg = /^[A-Za-z]+$/; 
        return reg.test(val);
	};
	
	$.checkQQMsn = function(val){
		var reg = /^[1-9]*[1-9][0-9]*$|^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/; 
        return reg.test(val);
	};
})(jQuery);

jQuery(function($){
	$(".ajax-loading").bind('ajaxSend',function(){
		$(".ajax-loading").removeClass("ajax-error").html(AJAX_LOADING).stop(true).show();
		$(".ajax-loading").css({"opacity":1});
	});
	
	$(".ajax-loading").bind('ajaxError',function(){
		$.ajaxError();
	});
	
	$(".ajax-loading").bind('ajaxSuccess',function(){
		if(!ajaxError)
			$(".ajax-loading").hide();
		
		ajaxError = false;
	});
	
	$(".tabs-title .tt-item").click(function(){
		$(".tabs-title .tt-item").removeClass('active');
		$(this).addClass('active');
		$(".tabs-body .tabs-item").removeClass('tabs-active');
		$(".tabs-body .tabs-item[rel='"+ this.getAttribute('rel') +"']").addClass('tabs-active');
	});

	$(".table-form tr > th:first-child").addClass('first');
	
	$('a').focus(function(){
		this.blur();
	});
	
	$(".handle-btns .link-button").hover(function(){
		$(this).addClass("link-button-hover");
	},function(){
		$(this).removeClass("link-button-hover");
	});
	$(".handle-btns .img-button").hover(function(){
		$(this).addClass("img-button-hover");
	},function(){
		$(this).removeClass("img-button-hover");
	});
	
	$(".label_box input").live('change',function(){
		if(this.checked)
			$(this).parent().addClass('active');
		else
			$(this).parent().removeClass('active');
	});
});

function checkAll(id)
{
	$("#" + id + " tbody tr:not([disabled]) input[name='key']").each(function(){
		if(this.checked)
			this.checked = false;
		else
			this.checked = true;
	});
}

function addData(obj)
{
	var fun = function(){
		location.href = APP+'?'+VAR_MODULE+'='+CURR_MODULE+'&'+VAR_ACTION+'=add';
	};
	
	setTimeout(fun,1);
}

function editData(obj,id,pk)
{
	if(isNaN(id))
		id = $("#" + id + " input:checked[name='key']").eq(0).val();
		
	if(!id)
		return false;
		
	if(pk == null)
		pk = 'id';
	
	var fun = function(){
		location.href = APP+'?'+VAR_MODULE+'='+CURR_MODULE+'&'+VAR_ACTION+'=edit&'+pk+'=' + id;
	};
	
	setTimeout(fun,1);
}

function removeImg(obj,id,field,relMod)
{
	var query = new Object();
	query.id = id;
	query.field = field;
	query.rel_mod = relMod;
	
	$.ajax({
		url: APP + '?' + VAR_MODULE + '=' + CURR_MODULE + '&' + VAR_ACTION + '=deleteImg',
		type:"POST",
		cache: false,
		data:query,
		dataType:"json",
		success: function(result){
			if(result.isErr == 0)
				$(obj).parent().remove();
			else
				$.ajaxError(result.content);
		}
	});
}

function removeData(obj,id,pk)
{
	var ids =  new Array();
	
	if(isNaN(id))
	{
		$("#" + id + " input:checked[name='key']").each(function(){
			ids.push(this.value);
		});
	}
	else
	{
		ids.push(id);
		var parent = $(obj).parent().parent();
		id = parent.parent().parent().attr('id');
	}
	
	ids = ids.join(',');
	if(ids == '')
		return false;
	
	if(!window.confirm(CONFIRM_DELETE))
		return false;
	
	var query = new Object();
	query.id = ids;
	
	$.ajax({
		url: APP + '?' + VAR_MODULE + '=' + CURR_MODULE + '&' + VAR_ACTION + '=remove',
		type:"POST",
		cache: false,
		data:query,
		dataType:"json",
		success: function(result){
			if(result.isErr == 0)
			{
				$("#" + id + " tbody tr input[name='key']").each(function(){
					if((',' + ids + ',').indexOf(',' + this.value + ',') != -1)
					{
						parent = $(this).parent().parent();
						this.checked = false;
						$('td span,td a',parent).each(function(){
							if (typeof(this.onclick) == 'function' && this.onclick.toString() != '')
							{
								if(this.onclick.toString().indexOf('toggleStatus') != -1)
								{
									var img = $('img',this).get(0);
									img.src = img.src.replace('status','disabled');
								}
								
								this.onclick = '';
							}
						});
						
						parent.attr({"disabled":true,"title":ALREADY_REMOVE});
						$("td",parent).attr({"disabled":true}).addClass('disabled');
						$("td *",parent).attr({"disabled":true}).addClass('disabled');
					}
					
					if($("#" + id + " tbody tr:not([disabled])").length == 0)
						location.reload(true);
				});
			}
			else
				$.ajaxError(result.content);
		}
	});
}