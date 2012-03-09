jQuery(function($){
	$("#user_group").change(function(){
		var gid = this.value;
		getUserGroupAuthoritys(gid);
	});
});

function getCitys(province,city)
{
	var provinceID = $(province).val();
	$.ajax({
		url: APP + '?' + VAR_MODULE + '=Region&' + VAR_ACTION + '=getCitys',
		type:"POST",
		cache: false,
		data:{"pid":provinceID},
		dataType:"json",
		success: function(result){
			html = '';
			i = 0;
			count = result.length;
			for(i; i<count; i++)
			{
				html += '<option value="'+ result[i].id +'">'+ result[i].name +'</option>';
			}
			
			$(city).html(html);
		}
	});
}

function getUserGroupAuthoritys(gid)
{
	$.ajax({
		url: APP + '?' + VAR_MODULE + '=UserGroup&' + VAR_ACTION + '=authoritys',
		type:"POST",
		cache: false,
		data:{"gid":gid},
		dataType:"html",
		success: function(html){
			$("#user_authoritys").html(html);
		}
	});
}

function getUserExits(uid,userName)
{
	var userCount = 0;
	$.ajax({
		url: APP + '?' + VAR_MODULE + '=User&' + VAR_ACTION + '=getUserExits',
		type:"POST",
		async:false,
		cache: false,
		data:{"uid":uid,"user_name":userName},
		dataType:"json",
		success: function(result){
			userCount = result.count;
		}
	});
	return userCount;
}

function searchUser(sele,keyword)
{
	var keywords = $(keyword).val();
	var sele = $(sele);
	
	sele.empty();
	option = new Option(SEARCH_LOADING,'');
	sele.get(0).options.add(option);
	
	$.ajax({
		url: APP + '?' + VAR_MODULE + '=User&' + VAR_ACTION + '=getUserList',
		cache: false,
		data:{"key":keywords},
		dataType:"json",
		success:function(data)
		{
			sele.empty();
			if(data && data.length > 0)
			{	
				for(var i=0;i<data.length;i++)
				{
					option = new Option(data[i].user_name, data[i].uid);
					sele.get(0).options.add(option);
				}
			}
			else
			{
				option = new Option(EMPTY_USER,'');
				sele.get(0).options.add(option);
			}
		}
	});	
}