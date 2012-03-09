<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<title>方维分享系统  -- 安装向导</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" type="text/css" href="__TMPL__Public/css/style.css" />
<script type="text/javascript" src="__TMPL__Public/js/jquery.js"></script>
<script type="text/javascript" src="__TMPL__Public/js/jquery.json.js"></script>
<script type="text/javascript" src="__TMPL__Public/js/script.js"></script>
<script language="JavaScript">
<!--
//指定当前组模块URL地址 
var URL = '__URL__';
var ROOT_PATH = '__ROOT__';
var APP	 =	 '__APP__';
var VAR_MODULE = '<?php echo c('VAR_MODULE');?>';
var VAR_ACTION = '<?php echo c('VAR_ACTION');?>';
//-->
</script>
</head>
<body><div class="install">
<form name="install" action="<?php echo u('Index/install');?>" method="POST" id="install">
<div class="header">
	<h1></h1>
</div>
<div class="main">
	<h2 class="title" style="margin-left:30px;">网站信息</h2>
	<table class="tb" style="margin:20px 0 20px 30px; width:600px;" cellspacing="1">
		<?php if(is_array($froms["dbinfo"])): foreach($froms["dbinfo"] as $key=>$item): ?><tr>
			<td class="tbopt"><?php echo ($item["name"]); ?>:</td>
			<td width="200"><input type="text" name="dbinfo[<?php echo ($key); ?>]" value="<?php echo ($item["value"]); ?>" size="35" class="txt"></td>
			<td><?php if($item["error"] == 1): ?><span class="red"><?php echo ($item["msg"]); ?></span><?php else: ?><?php echo ($item["notice"]); ?><?php endif; ?></td>
		</tr><?php endforeach; endif; ?>
		<!--<tr>
			<td class="tbopt">安装测试数据:</td>
			<td width="200"><input type="checkbox" name="DEMO_DATA" value="1" <?php if($DEMO_DATA == 1): ?>checked="checked"<?php endif; ?> /></td>
			<td></td>
		</tr>-->
	</table>
	<h2 class="title" style="margin-left:30px;">管理员信息</h2>
	<table class="tb" style="margin:20px 0 20px 30px; width:600px;" cellspacing="1">
		<?php if(is_array($froms["admin"])): foreach($froms["admin"] as $key=>$item): ?><tr>
			<td class="tbopt"><?php echo ($item["name"]); ?>:</td>
			<td width="200"><input type="<?php echo ($item["type"]); ?>" name="admin[<?php echo ($key); ?>]" value="<?php echo ($item["value"]); ?>" size="35" class="txt"></td>
			<td><?php if($item["error"] == 1): ?><span class="red"><?php echo ($item["msg"]); ?></span><?php else: ?><?php echo ($item["notice"]); ?><?php endif; ?></td>
		</tr><?php endforeach; endif; ?>
	</table>
	
	<div class="center" style="padding:0 0 20px 0;">
		<input type="submit" value="开始安装" class="formbutton" />
	</div>
	<div class="center footer">
		方维分享系统
	</div>
</div>
</form>
</div>
</body>
</html>