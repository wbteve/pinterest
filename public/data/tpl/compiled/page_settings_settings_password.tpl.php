<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<?php 
$css_list[0]['url'] = './tpl/css/setting.css';
$js_list[0] = './public/js/city.js';
 include template('inc/header'); ?><div id="body" class="fm960">
<div class="piece1">
<div class="piece1_hd"></div>
<div class="piece1_bd clearfix">
<div id="content" style="width:960px;"><? include template('inc/settings/settings_menu'); ?><div id="setting_box" class="fl">
<div id="setting_form" class="setting_password">
<form action="<?php echo FU('settings/savepassword',array()); ?>" method="post">
<? if(!empty($msg)) { ?>
<div class="green_alert_l"><?=$msg?></div>
<? } ?>
<dl>
<dd>现在的密码：</dd>
<dt>
<input name="oldpassword" class="gray_text" type="password" value="">
</dt>
<dd>新的密码：</dd>
<dt>
<input name="newpassword" class="gray_text" type="password" value="">
</dt>
<dd>确认密码：</dd>
<dt>
<input name="newpasswordagain" class="gray_text" type="password" value="">
</dt>
<dd>&nbsp;</dd>
<dt>
<input type="hidden" name="action" value="savepassword" />
<input class="green_button" type="submit" value="确定">
</dt>
</dl>
</form>
</div>
</div>
</div>
</div>
<div class="piece1_ft"></div>
</div>
</div><? include template('inc/footer'); ?>