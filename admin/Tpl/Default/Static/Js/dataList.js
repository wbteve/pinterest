jQuery(function($){
	$(document).mousemove(function(e){
		var obj = e.target;
		if ($(obj).attr('tagName').toLowerCase() == 'span' && 
			typeof(obj.onclick) == 'function' && 
			(obj.onclick.toString().indexOf('textEdit') != -1 ||
			 obj.onclick.toString().indexOf('numberEdit') != -1))
		{
			obj.title = CLICK_EDIT_CONTENT;
			$(obj).addClass('select');
			$(obj).one('mouseout',function(){
				$(obj).removeClass('select');
			});
		}
	});
});

function createInputEdit(obj,id,name,type)
{
	var module = obj.getAttribute('module');
	var val = $.trim($(obj).html());
	var input;
	
	if($("#"+module + "_" + name + "_" + id).length == 0)
	{
		var txt = document.createElement("INPUT");
		txt.id = module + "_" + name + "_" + id;
		txt.className = 'textinput';
		$(obj).parent().append(txt);
		input = $(txt);
		
		input.keypress(function(e){
			if (e.keyCode == 13)
			{
				this.blur();
				return false;
			}
		
			if (e.keyCode == 27)
			{
				$(obj).show();
				$(this).hide();
			}
		});
		
		input.blur(function(){
			if($.trim(this.value).length > 0)
			{
				var err = false;
				var value = $.trim(this.value);
				val = $.trim($(obj).html());
				
				if(type == 'number')
				{
					val = parseFloat(val);
					value = parseFloat(value);
					if(isNaN(value))
						err = true;
				}
				
				if(val == value || err)
				{
					$(obj).show();
					$(this).hide();
					return false;
				}
				
				submitEdit(obj,module,id,value,name);
			}
			else
			{
				$(obj).show();
				$(this).hide();
			}
		});
	}
	else
		input = $("#"+module + "_" + name + "_" + id);
		
	input.val(val);
	var width = $(obj).width() + 12;
	if(width > $(obj).parent().width() - 12)
		width = $(obj).parent().width() - 12;
	input.show();
	input.width(width).focus();
	$(obj).hide();
}

function textEdit(obj,id,name)
{
	createInputEdit(obj,id,name,'text')
}

function numberEdit(obj,id,name)
{
	createInputEdit(obj,id,name,'number')
}

function submitEdit(obj,module,id,val,name)
{
	var query = new Object();
	query.field = name;
	query.val = val;
	query.id = id;
	
	$.ajax({
		url: APP + '?' + VAR_MODULE + '=' + module + '&' + VAR_ACTION + '=editField',
		type:"POST",
		cache: false,
		data:query,
		dataType:"json",
		error: function(){
			$(obj).show();
			$("#"+module + "_" + name + "_" + id).hide();
		},
		success: function(result){
			if(result.isErr == 0)
				$(obj).html(result.content);
			else
				$.ajaxError(result.content);
			
			$(obj).show();
			$("#"+module + "_" + name + "_" + id).hide();
		}
	});
}

function sortBy(field,sort,action,ext)
{
	var url = location.href;
	url = url.replace(/(p=\d+?&)|(p=\d+?$)/g,'');
	var len = url.length;
	if(url.substr(len-1) == '&')
		url = url.substr(0,len-1);
	
	if(url.search(/_order=.+?&/g) > -1)
		url = url.replace(/_order=.+?&/g,'_order='+field+'&');
	else if(url.search(/_order=.+?$/g) > -1)
		url = url.replace(/_order=.+?$/g,'_order='+field);
	else
		url += '&_order='+field;
	
	if(url.search(/_sort=.+?&/g) > -1)
		url = url.replace(/_sort=.+?&/g,'_sort='+sort+'&');
	else if(url.search(/_sort=.+?$/g) > -1)
		url = url.replace(/_sort=.+?$/g,'_sort='+sort);
	else
		url += '&_sort='+sort;
	
	var fun = function(){
		location.href = url;
	};
	
	setTimeout(fun,1);
	//location.href = APP+'?'+VAR_MODULE+'='+CURR_MODULE+'&'+VAR_ACTION+'='+action+'&_order='+field+'&_sort='+sort+ (ext ? '&'+ext : '' );
}

function toggleStatus(obj,id,name)
{
	if($('img',obj).length == 0)
		return false;
	
	var module = obj.getAttribute('module');
	var query = new Object();
	query.field = name;
	query.val = $('img',obj).get(0).getAttribute('status');
	query.id = id;
	
	$.ajax({
		url: APP + '?' + VAR_MODULE + '=' + module + '&' + VAR_ACTION + '=toggleStatus',
		type:"POST",
		cache: false,
		data:query,
		dataType:"json",
		success: function(result){
			if(result.isErr == 0)
			{
				var img = $('img',obj).get(0);
				var src = img.src.replace(query.val + '.gif',result.content + '.gif');
				img.setAttribute('status',result.content);
				img.src = src;
			}
			else
				$.ajaxError();
		}
	});
}