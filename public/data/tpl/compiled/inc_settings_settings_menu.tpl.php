<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<div class="fl " id="setting_bar">
<h1>设置</h1>
<div class="base_info">
<a href="<?php echo FU('settings/personal',array()); ?>"<? if(ACTION_NAME == 'personal') { ?> class="c"<? } ?>>基本信息</a><br />
<a href="<?php echo FU('settings/avatar',array()); ?>"<? if(ACTION_NAME == 'avatar') { ?> class="c"<? } ?>>修改头像</a><br />
<a href="<?php echo FU('settings/password',array()); ?>"<? if(ACTION_NAME == 'password') { ?> class="c"<? } ?>>修改密码</a><br />
<a href="<?php echo FU('settings/bind',array()); ?>"<? if(ACTION_NAME == 'bind') { ?> class="c"<? } ?>>帐号绑定</a><br />
</div>
<h1>认证</h1>
<div id="certification">
<a href="<?php echo FU('settings/buyerverifier',array()); ?>"<? if(ACTION_NAME == 'buyerverifier') { ?> class="c"<? } ?>>淘宝买家认证</a><br />
</div>
</div>