<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<?php 
$css_list[0]['url'] = './tpl/css/loginreg.css';
$js_list[0] = './tpl/js/loginreg.js';
 include template('inc/header'); ?><div id="body" class="fm960">
<div class="piece1">
<div class="piece1_hd"></div>
<div class="piece1_bd clearfix">
<div id="content" style="width:960px;">
<div class="lg_left">
<h1>登陆<?=$_FANWE['setting']['site_name']?></h1>
<div class="lg_form">
<form id="loginForm" name="loginForm" action="<?php echo FU('user/ajax_login',array()); ?>" method="post">
<div class="lg_name">
<span>用户名：</span>
<input type="text" value="" tooltip="邮箱 或 昵称" name="email_name" class="text" maxlength="32" />
</div>
<div class="err_name"><span></span></div>
<div class="clear"></div>
<div class="lg_pass">
<span>密　码：</span>
<input type="password" value="" name="pass" class="text" maxlength="32" />
</div>
<div class="err_pass"><span></span></div>
<div class="clear"></div>
<div class="iserror" id="iserror">
<div class="war"><img src="./tpl/images/error_02.png"><span>登录名或密码错误</span></div>
<div class="content">
<pre>1、如果登录名是邮箱地址，请输入全称<br/> 如：share@qq.com<br/>2、请检查登录名大小写是否正确。<br/>3、请检查密码大小写是否正确。</pre>
</div>
</div>
<div class="iserror iserror2" id="iserror2">
<div class="war"><img src="./tpl/images/error_02.png"><span>用户被锁定，无法登录！</span></div>
<div class="content">
<pre>1.可以联系管理员进行解锁。<br/>2.重新注册一个账户！</pre>
</div>
</div>
<div class="lg_remember">
<label>
<input type="checkbox" name="remember" class="checkbox" checked="checked" value="1209600">
<span>记住我（两周免登陆）</span>
</label>
</div>
<div class="lg_login">
<input type="submit" value=" " class="sub" id="login_submit" />
<a href="<?php echo FU('user/forgetpassword',array()); ?>">忘记密码？</a>
</div>
<div class="clear"></div>
<div class="lg_login_loading">
<span>登陆中，请稍候...</span>
</div>
<input name="rhash" value="<!--dynamic getRHash-->" type="hidden"/>
<input name="refer" value="<!--dynamic getUserRefer-->" type="hidden"/>
<input name="action" value="ajax_login" type="hidden"/>
</form>
<div class="ot_login">
<span>您也可以用以下方式登录：</span>
<div class="ot_btn"><? if(is_array($login_modules)) { foreach($login_modules as $login_module) { ?><a href="<?=$login_module['login_url']?>" title="<?=$login_module['name']?>"><img src="<?=$login_module['login_img']?>" alt="<?=$login_module['name']?>" /></a>&nbsp;
<? } } ?>
</div>
</div>
</div>
</div>
<div class="lg_right">
<h1>注册</h1>
<span><?php echo sprintf('还没有%s帐号？',$_FANWE['setting']['site_name']); ?></span>
<a href="<?php echo FU('user/register',array()); ?>"></a>
</div>
</div>
</div>
<div class="piece1_ft"></div>
</div>
</div><? include template('inc/footer'); ?>