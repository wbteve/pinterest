<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<?php 
$css_list[0]['url'] = './tpl/css/loginreg.css';
$js_list[0] = './tpl/js/loginreg.js';
 include template('inc/header'); ?><div id="body" class="fm960">
<div class="piece1">
<div class="piece1_hd"></div>
<div class="piece1_bd clearfix">
<div id="content" style="width:960px;">
<div id="register_box">
<div id="register_left">
<h1>新会员注册</h1>
<span><?php echo sprintf('加入%s，发现时尚，分享购物乐趣。',$_FANWE['setting']['site_name']); ?></span>
<div id="register_center">
<div id="register_form">
<form action="<?php echo FU('user/ajax_register',array()); ?>" method="post" id="registerForm">
<div class="ipt_mail">
<span>电子邮箱：</span>
<input type="text" maxlength="36" name="email" id="reg_email" class="text" value="" istip="0" rel=".err_email" check="0"/>
</div>
<div class="ipt_ulike">
<span>昵称：</span>
<input style="*margin-left:-1px;" maxlength="36" type="text" name="user_name" id="reg_user_name" class="text" value="" istip="0" rel=".err_ulike" check="0"/>
</div>
<div class="ipt_sex">
<span>性别：</span>
<div class="rdo">
<input type="radio" name="gender" value="0" checked="checked" class="ck">女
<input style="margin-left:10px" type="radio" name="gender" value="1" class="ck">男
</div>
</div>
<div class="ipt_password">
<span>密码：</span>
<input style="*margin-left:-1px;" maxlength="36" type="password" name="password" id="reg_password" class="text" istip="0" rel=".err_password" check="0"/>
</div>
<div class="ipt_respassword">
<span>确认密码：</span>
<input type="password" maxlength="36" name="confirm_password" id="reg_cpassword" class="text" istip="0" rel=".err_rstpassword" check="0"/>
</div>
<div class="ipt_check">
                                        <span>验证码：</span>
                                        <input class="check" type="text" maxlength="10" name="checkcode">
                                        <img id="img_checkcode" alt="验证码" src="<?=$_FANWE['site_root']?>misc.php?action=verify&rhash=<!--dynamic getRHash-->">
                                        <a id="checkcode_change" href="javascript:void(0);">换一张</a>
                                    </div>
<div class="ipt_box">
<input class="box fl" type="checkbox" name="agreement" id="reg_agreement" checked="checked" value="1" /><?php $agreement_url = FU('user/agreement'); ?><span class="fl"><?php echo sprintf('我已看过并同意《<a href="%s" target="_blank">%s网络服务使用协议</a>》',$agreement_url,$_FANWE['setting']['site_name']); ?></span>
</div>
<div class="ipt_sub">
<input type="submit" class="sub" id="reg_submit" value=" "/>
<div class="lg_reg_loading">
<span style="font-size:13px;">注册中，请稍候...</span>
</div>
<div class="lg_reg_check">
<span style="font-size:13px; color:#f00;">请先完善注册信息。</span>
</div>
</div>
<input name="rhash" id="regRHash" value="<!--dynamic getRHash-->" type="hidden"/>
<input name="refer" value="<?php echo FU('u/index',array()); ?>" type="hidden"/>
<input name="action" value="ajax_register" type="hidden"/>
</form>
</div>
<div id="register_error">
<div class="err_email">请填写正确的常用邮箱，以便找回密码。比如：excample@excample.com</div>
<div class="err_ulike">支持中文，不能以数字开头，由2~20个字符组成，中文算两个字符。</div>
<div class="err_password">6-20个字母、数字或者符号</div>
<div class="err_rstpassword">这里要重复输入一下你的密码</div>
</div>
</div>
</div>
<div id="register_right">
<div class="rst_login">
<span><?php echo sprintf('已有%s账号？请直接登录',$_FANWE['setting']['site_name']); ?></span>
<a href="<?php echo FU('user/login',array()); ?>"></a>
</div>
<div class="other_login">
<span>您也可以用以下方式登录：</span><? if(is_array($login_modules)) { foreach($login_modules as $login_module) { ?><a class="o_icon" href="<?=$login_module['login_url']?>" style="background:url(<?=$login_module['icon_img']?>) no-repeat left center;"><?=$login_module['name']?></a>
<? } } ?>
</div>
</div>
</div>
<div class="clear"></div>
</div>
</div>
<div class="piece1_ft"></div>
</div>
</div><? include template('inc/footer'); ?>