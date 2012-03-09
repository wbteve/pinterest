<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link href="__TMPL__Static/Css/style.css" rel="stylesheet" />
<script type="text/javascript" src="__TMPL__Static/Js/jquery.js"></script>
</head>
<body style="background:#99A2B3;padding:0">
<div class="fanwe-header">
	<div class="fh-top">
		<div class="fht-logo"><img src="__TMPL__Static/Images/logo.gif" /></div>
		<div class="fht-links">
			<span>欢迎您！<?php echo ($_SESSION["admin_name"]); ?></span>
			<a class="edit-pwd" href="<?php echo U('Index/password');?>" target="mainFrame">修改密码</a>
			<a class="browse-index" href="../" target="brank">浏览首页</a>
			<a href="<?php echo U('Cache/system');?>" target="mainFrame">更新缓存</a>
			<a href="<?php echo U('Public/logout');?>" target="mainFrame">退出</a>
		</div>
		<div class="fht-navs">
			<?php if(is_array($role_navs)): foreach($role_navs as $key=>$nav): ?><div class="<?php if($key == 0): ?>active<?php endif; ?>">
				<p>
					<a href="<?php echo U('Index/left',array('id'=>$nav['id']));?>"  target="leftFrame"><?php echo ($nav["name"]); ?></a>
				</p>
			</div><?php endforeach; endif; ?>
		</div>
	</div>
	<!--<div class="fh-bottom">
		<div class="fhb-body">
			
		</div>
	</div>-->
</div>
<div class="ajax-loading" style="top:36px; right:0;"></div>
</body>
<script type="text/javascript">
jQuery(function(){
	$(".fht-navs div").click(function(){
		$(".fht-navs div").removeClass("active");
		$(this).addClass("active");
		$('a',this).blur();
	});
	
	$(".fht-navs div").click(function(){
		$(".fht-navs div").removeClass("active");
		$(this).addClass("active");
		$('a',this).blur();
	});
});
</script>
</html>