<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link href="__TMPL__Static/Css/style.css" rel="stylesheet" />
<script type="text/javascript" src="__TMPL__Static/Js/jquery.js"></script>
</head>
<body style="background:#fff;padding:0;">
	<div class="fanwe-change" rel="left"></div>
</body>
<script type="text/javascript">
jQuery(function($){
	$(".fanwe-change").click(function(){
		var rel = this.getAttribute("rel");
		if(rel == 'left')
		{
			rel = 'right';
			$(this).addClass("fanwe-change-right");
			window.parent.document.getElementById("bodyFrameset").cols = "0,14,*";
		}
		else
		{
			rel = 'left';
			$(this).removeClass("fanwe-change-right");
			window.parent.document.getElementById("bodyFrameset").cols = "190,14,*";
		}
		
		this.setAttribute("rel",rel);
	});
});
</script>
</html>