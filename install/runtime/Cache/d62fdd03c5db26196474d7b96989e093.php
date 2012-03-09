<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<title>方维分享系统  -- 安装向导</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="__TMPL__Public/css/style.css" />
</head>
<body>
<div class="install block">
<form name="install" action="<?php echo u('Index/index');?>" method="POST" >
<div class="header">
	<h1></h1>
</div>
<div class="main">
	<h2 class="title">环境检查</h2>
	<table class="tb" style="margin:20px 0 20px 53px; width:550px;" cellspacing="1">
		<tr>
			<th class="center">项目</th>
			<th class="center">所需配置</th>
			<th class="center">当前服务器</th>
		</tr>
		<?php if(is_array($result["systems"])): foreach($result["systems"] as $key=>$system): ?><tr>
			<td><?php echo ($system["name"]); ?></td>
			<td class="center"><?php echo ($system["ask"]); ?></td>
			<td class="center"><span class="<?php if($system["status"] == 1): ?>w<?php else: ?>nw<?php endif; ?>"><?php echo ($system["msg"]); ?></span></td>
		</tr><?php endforeach; endif; ?>
		<tr>
			<td>short_open_tag</td>
			<td class="center">On</td>
			<td class="center"><span class="<?php if($is_short_open_tag): ?>w<?php else: ?>nw<?php endif; ?>"><?php if($is_short_open_tag): ?>On<?php else: ?>请将 php.ini 中的 short_open_tag 设置为 On<?php endif; ?></span></td>
		</tr>
	</table>
	<h2 class="title">目录、文件权限检查</h2>
	<table class="tb" style="margin:20px 0 20px 53px; width:550px;" cellspacing="1">
		<tr>
			<th class="center">目录文件</th>
			<th class="center">所需状态</th>
			<th class="center">检查结果</th>
		</tr>
		<?php if(is_array($result["files"])): foreach($result["files"] as $key=>$file): ?><tr>
			<td><?php echo ($file["name"]); ?></td>
			<td class="center"><?php echo ($file["ask"]); ?></td>
			<td class="center"><span class="<?php if($file["status"] == 1): ?>w<?php else: ?>nw<?php endif; ?>"><?php echo ($file["msg"]); ?></span></td>
		</tr><?php endforeach; endif; ?>
	</table>
	<h2 class="title">函数依赖性检查</h2>
	<table class="tb" style="margin:20px 0 20px 53px; width:550px;" cellspacing="1">
		<tr>
			<th class="center">函数名称</th>
			<th class="center">所需状态</th>
			<th class="center">检查结果</th>
		</tr>
		<?php if(is_array($result["funs"])): foreach($result["funs"] as $key=>$fun): ?><tr>
			<td><?php echo ($fun["name"]); ?></td>
			<td class="center"><?php echo ($fun["ask"]); ?></td>
			<td class="center"><span class="<?php if($fun["status"] == 1): ?>w<?php else: ?>nw<?php endif; ?>"><?php echo ($fun["msg"]); ?></span></td>
		</tr><?php endforeach; endif; ?>
	</table>
	<div class="center" style="padding:0 0 20px 0;">
		<?php if($result['status'] == 1 and $is_short_open_tag): ?><input type="button" value="下一步" onclick="location.href='<?php echo u('Index/database');?>'" class="formbutton" /><?php endif; ?>
	</div>
	<div class="center footer">
		方维分享系统
	</div>
</div>
</form>
</div>
</body>
</html>