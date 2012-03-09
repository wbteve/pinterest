function ToExamineAllData(){
	if (confirm("确定要一键审核所有分享吗？\n该操作有一定的风险，如：非法内容！")) {
		$.ajax({
			url: APP + '?' + VAR_MODULE + '=' + CURR_MODULE + '&' + VAR_ACTION + '=ToExamineAll',
			type:"POST",
			cache: false,
			dataType:"json",
			success: function(result){
				if(result.isErr==0)
				{
					window.location.href=window.location.href;
				}
			}
		});
	}
}


function ToExamineSelectData(obj,id){
	var ids = new Array();
	
	if (isNaN(id)) {
		$("#" + id + " input:checked[name='key']").each(function(){
			ids.push(this.value);
		});
	}
	else {
		ids.push(id);
		var parent = $(obj).parent().parent();
		id = parent.parent().parent().attr('id');
	}
	
	ids = ids.join(',');
	if (ids == '') 
		return false;
	
	if (!window.confirm("确定审核所选吗？")) 
		return false;
	
	var query = new Object();
	query.id = ids;
	$.ajax({
		url: APP + '?' + VAR_MODULE + '=' + CURR_MODULE + '&' + VAR_ACTION + '=ToExamineSelect',
		type: "POST",
		cache: false,
		data: query,
		dataType: "json",
		success: function(result){
			if (result.isErr == 0) {
				window.location.href=window.location.href;
			}
			else 
				$.ajaxError(result.content);
		}
	});
}

function ShiftClassData(obj, id){
	var ids = new Array();
	
	if (isNaN(id)) {
		$("#" + id + " input:checked[name='key']").each(function(){
			ids.push(this.value);
		});
	}
	else {
		ids.push(id);
		var parent = $(obj).parent().parent();
		id = parent.parent().parent().attr('id');
	}
	
	ids = ids.join(',');
	if (ids == '') 
		return false;
	
	if (!window.confirm("确定转移所选吗？\n注：仅转移某些特定的数据，不能转移全部！")) 
		return false;
	
	var url= APP + '?' + VAR_MODULE + '=' + CURR_MODULE + '&' + VAR_ACTION + '=ShiftClass&id='+ids;
	window.location.href=url;
}

function BatchDelete(){
	if (!window.confirm("该操作有一定的风险，请慎重！")) 
		return false;
	var url= APP + '?' + VAR_MODULE + '=' + CURR_MODULE + '&' + VAR_ACTION + '=BatchDelete';
	window.location.href=url;
}
