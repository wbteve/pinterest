$(document).ready(function(){
	$.getJSON('http://tj.fanwe.com/license.php?callback=?',function(data){
		if(data.lisence!='')
		{
			var str = "域名已授权";
		}
		else
		{
			var str = "域名未授权";
		}
		var version = CURRENT_VERSION;
		if(parseFloat(data.version)>parseFloat(version))
		{
			$("#version_tip").html("<span style='color:#f30;'>有新版本，需要更新 &nbsp;&nbsp;<a href='http://www.fanwe.cn' target='_blank'>官方网站</a></span>"+"&nbsp;"+str);
		}
		else
		{
			$("#version_tip").html("<span style='color:#f30;'>当前是最新版本"+version+"</span>"+"&nbsp;"+str);
		}
	});	
});