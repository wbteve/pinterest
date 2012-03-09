<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link href="__TMPL__Static/Css/style.css" rel="stylesheet" />
<script type="text/javascript" src="__TMPL__Static/Js/jquery.js"></script>
<script type="text/javascript" src="__TMPL__Static/Js/base.js"></script>
<script type="text/javascript" src="__TMPL__Static/Js/json.js"></script>
<script type="text/javascript" src="__TMPL__Static/Js/jquery.pngFix.js"></script>
<script type="text/javascript">
<!--
//指定当前组模块URL地址 
var URL = '__URL__';
var ROOT_PATH = '__ROOT__';
var APP	 =	 '__APP__';
var STATIC = '__TMPL__Static';
var VAR_MODULE = '<?php echo c('VAR_MODULE');?>';
var VAR_ACTION = '<?php echo c('VAR_ACTION');?>';
var CURR_MODULE = '<?php echo ($module_name); ?>';
var CURR_ACTION = '<?php echo ($action_name); ?>';

//定义JS中使用的语言变量
var CONFIRM_DELETE = '<?php echo L("CONFIRM_DELETE");?>';
var AJAX_LOADING = '<?php echo L("AJAX_LOADING");?>';
var AJAX_ERROR = '<?php echo L("AJAX_ERROR");?>';
var ALREADY_REMOVE = '<?php echo L("ALREADY_REMOVE");?>';
var SEARCH_LOADING = '<?php echo L("SEARCH_LOADING");?>';
var CLICK_EDIT_CONTENT = '<?php echo L("CLICK_EDIT_CONTENT");?>';
//-->
</script>
</head>
<body>
	<div class="fanwe-body">
		<div class="fb-title"><div><p><span><?php echo ($ur_href); ?></span></p></div></div>
		<div class="fb-body">
			<table class="body-table" cellpadding="0" cellspacing="1" border="0">
				<tr>
					<td class="body-table-td">
						<div class="body-table-div">
<div class="tabs-title">
	<div class="tt-item active" rel="1"><p><a href="javascript:;"><?php echo L("TAB_1");?></a></p></div>
	<div class="tt-item" rel="2"><p><a href="javascript:;"><?php echo L("TAB_2");?></a></p></div>
	<div class="tt-item" rel="3"><p><a href="javascript:;"><?php echo L("TAB_3");?></a></p></div>
</div>
<form method='post' id="form" name="form" action="<?php echo U(MODULE_NAME.'/update');?>">
<div class="tabs-body">
	<table cellpadding="4" cellspacing="0" border="0" class="table-form tabs-item tabs-active" rel="1">
		<tr>
			<td><script type="text/javascript" src="__TMPL__Static/Ckeditor/ckeditor.js"></script><script type="text/javascript" src="__TMPL__Static/Ckfinder/ckfinder.js"></script><textarea id="USER_AGREEMENT_editor" name="settings[USER_AGREEMENT]"><?php echo ($settings["USER_AGREEMENT"]); ?></textarea><script type="text/javascript">var USER_AGREEMENT_editor =CKEDITOR.replace("USER_AGREEMENT_editor",{"width":"96%","height":"130px","toolbar":"Default"}) ;CKFinder.setupCKEditor(USER_AGREEMENT_editor,"__TMPL__Static/Ckfinder") ;</script></td>
		</tr>
	</table>
	<table cellpadding="4" cellspacing="0" border="0" class="table-form tabs-item" rel="2">
		<tr>
			<th width="200"><?php echo L("TODAY_MAX_SCORE");?></th>
			<td><input type="text" class="textinput" name="settings[TODAY_MAX_SCORE]" value="<?php echo ($settings["TODAY_MAX_SCORE"]); ?>" /></td>
		</tr>
		<tr>
			<th><?php echo L("USER_REGISTER_SCORE");?></th>
			<td><input type="text" class="textinput" name="settings[USER_REGISTER_SCORE]" value="<?php echo ($settings["USER_REGISTER_SCORE"]); ?>" /></td>
		</tr>
		<tr>
			<th><?php echo L("USER_LOGIN_SCORE");?></th>
			<td><input type="text" class="textinput" name="settings[USER_LOGIN_SCORE]" value="<?php echo ($settings["USER_LOGIN_SCORE"]); ?>" /></td>
		</tr>
		<tr>
			<th><?php echo L("USER_AVATAR_SCORE");?></th>
			<td><input type="text" class="textinput" name="settings[USER_AVATAR_SCORE]" value="<?php echo ($settings["USER_AVATAR_SCORE"]); ?>" /></td>
		</tr>
		<tr>
			<th><?php echo L("USER_REFERRAL_SCORE");?></th>
			<td><input type="text" class="textinput" name="settings[USER_REFERRAL_SCORE]" value="<?php echo ($settings["USER_REFERRAL_SCORE"]); ?>" /></td>
		</tr>
		<tr>
			<th><?php echo L("CLEAR_REFERRAL_SCORE");?></th>
			<td><input type="text" class="textinput" name="settings[CLEAR_REFERRAL_SCORE]" value="<?php echo ($settings["CLEAR_REFERRAL_SCORE"]); ?>" /></td>
		</tr>
		<tr>
			<th><?php echo L("SHARE_DEFAULT_SCORE");?></th>
			<td><input type="text" class="textinput" name="settings[SHARE_DEFAULT_SCORE]" value="<?php echo ($settings["SHARE_DEFAULT_SCORE"]); ?>" /></td>
		</tr>
		<tr>
			<th><?php echo L("SHARE_IMAGE_SCORE");?></th>
			<td><input type="text" class="textinput" name="settings[SHARE_IMAGE_SCORE]" value="<?php echo ($settings["SHARE_IMAGE_SCORE"]); ?>" /></td>
		</tr>
		<tr>
			<th><?php echo L("DELETE_SHARE_DEFAULT_SCORE");?></th>
			<td><input type="text" class="textinput" name="settings[DELETE_SHARE_DEFAULT_SCORE]" value="<?php echo ($settings["DELETE_SHARE_DEFAULT_SCORE"]); ?>" /></td>
		</tr>
		<tr>
			<th><?php echo L("DELETE_SHARE_IMAGE_SCORE");?></th>
			<td><input type="text" class="textinput" name="settings[DELETE_SHARE_IMAGE_SCORE]" value="<?php echo ($settings["DELETE_SHARE_IMAGE_SCORE"]); ?>" /></td>
		</tr>
	</table>
	<table cellpadding="4" cellspacing="0" border="0" class="table-form tabs-item" rel="3">
		<tr>
			<td><script type="text/javascript" src="__TMPL__Static/Ckeditor/ckeditor.js"></script><script type="text/javascript" src="__TMPL__Static/Ckfinder/ckfinder.js"></script><textarea id="USER_SORE_RULE_editor" name="settings[USER_SORE_RULE]"><?php echo ($settings["USER_SORE_RULE"]); ?></textarea><script type="text/javascript">var USER_SORE_RULE_editor =CKEDITOR.replace("USER_SORE_RULE_editor",{"width":"96%","height":"130px","toolbar":"Default"}) ;CKFinder.setupCKEditor(USER_SORE_RULE_editor,"__TMPL__Static/Ckfinder") ;</script></td>
		</tr>
	</table>
</div>
<table cellpadding="4" cellspacing="0" border="0" class="table-form" style="border-top:none;">
	<tr class="act">
		<th width="200">&nbsp;</th>
		<td>
			<input type="submit" class="submit_btn" value="<?php echo L("SUBMIT");?>" />
			<input type="reset" class="reset_btn" value="<?php echo L("RESET");?>" />
		</td>
	</tr>
</table>
</form>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ajax-loading"></div>
</body>
<script type="text/javascript">
jQuery(function($){
	updateBodyDivHeight();
	$(window).resize(function(){
		updateBodyDivHeight();
	});
});

function updateBodyDivHeight()
{
	jQuery(".body-table-div").height(jQuery(".fanwe-body").height() - 36);
	if(jQuery(".body-table-div").get(0).scrollHeight > jQuery(".body-table-div").height())
	{
		var width = jQuery(".body-table-div").width() - 16;
		jQuery(".body-table-div > *").each(function(){
			if(!$(this).hasClass('ajax-loading'))
			{
				$(this).width(width)	
			}
		});
	}
}
</script>
</html>