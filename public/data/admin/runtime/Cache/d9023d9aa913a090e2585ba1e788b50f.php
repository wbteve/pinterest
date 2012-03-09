<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo ($SHOP_NAME); ?>购物分享管理系统 - <?php echo L("SYS_LOGIN");?></title>
<link rel="stylesheet" type="text/css" href="__TMPL__Static/Css/login.css" />
<script type="text/javascript" src="__TMPL__Static/Js/jquery.js"></script>
<script type="text/javascript">
<!--
//指定当前组模块URL地址 
var AJAX_LOADING = '<?php echo L("AJAX_LOADING");?>';
var AJAX_ERROR = '<?php echo L("AJAX_ERROR");?>';
//-->
</script>
</head>
<body>
<form method='post' name="login" id="login" action="<?php echo U('Public/checkLogin');?>" >
<div id="login-box">
	<div id="resultMsg"></div>
	<input type="text" name="admin_name" id="admin_name" />
	<input type="password" name="admin_pwd" id="admin_pwd" />
	<input type="text" name="verify" id="verify" />
	<img id="verifyImg" src="<?php echo u('Public/verify');?>"  align="absmiddle" alt="<?php echo L("FRESH_VERIFY_TIP");?>" title="<?php echo L("FRESH_VERIFY_TIP");?>" width="50" height="22">
	<input type="image" id="loginBtn" src="__TMPL__Static/Images/login_btn.png" />
	<input type="hidden" name="ajax" value="1">
</div>
</form>
</body>
<script type="text/javascript">
jQuery(function($){
	if(top.location != self.location)
	{
		top.location.href = self.location.href;
		return;
	}
	
	$("#verifyImg").click(function(){
		fleshVerify();
	});
	
	$(document).keypress(function(e){
		if(e.keyCode == 13)
		{
			login()
		}
	});
	
	$("#login").submit(function(){
		login();
		return false;
	});
});

function login()
{
	$("#resultMsg").stop().removeClass('error').addClass('loading').html(AJAX_LOADING).show();
	
	$.ajax({
		url: "<?php echo U('Public/checkLogin');?>",
		type:"POST",
		cache: false,
		data:$("#login").serialize(),
		dataType:"json",
		error: function(){
			$("#resultMsg").addClass('error').html(AJAX_ERROR).show().fadeOut(5000);
		},
		success: function(result){
			$("#resultMsg").hide();
			if(result.status==1)
				location.href = '__APP__';
			else
			{
				$("#resultMsg").addClass('error').html(result.info).show().fadeOut(5000);
				fleshVerify();
			}
		}
	});
}

function fleshVerify()
{
	var time = new Date().getTime();
	$("#verifyImg").attr('src',"<?php echo U('Public/verify');?>&type=gif&rand="+time);
}
</script>
</html>