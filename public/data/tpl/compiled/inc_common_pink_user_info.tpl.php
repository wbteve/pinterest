<? if(!defined('IN_FANWE')) exit('Access Denied'); if($_FANWE['uid'] > 0) { ?>
<div class="user_info">
<a class="avatar" href="<?php echo FU('u/me',array()); ?>" target="_blank"><img class="r3" src="<?php echo avatar($_FANWE['uid'],'m',$_FANWE['user']['server_code'],1);?>" /></a>
<div class="user_name"><a href="<?php echo FU('u/me',array()); ?>" target="_blank"><?=$_FANWE['user_name']?></a>
<? if($_FANWE['user']['is_buyer']==1) { ?>
<a href="<?php echo FU('settings/buyerverifier',array()); ?>" class="v" target="_blank" title="买家认证"><img src="./tpl/pink2/images/buyer_icon.png"  /></a>
<? } ?>
</div>
<p class="feed_link">欢迎回来<?=$_FANWE['setting']['site_name']?>，去<a href="<?php echo FU('u/me',array()); ?>" target="_blank">看看好友动态</a>吧。</p>
</div>
<? } else { ?>
<div class="user_login">
<a href="<?php echo FU('user/register',array()); ?>" class="register_btn" title="注册">注册</a>
<a class="login-sina-btn" href="<?=$_FANWE['site_root']?>login.php?mod=sina" title="微博登录"><img src="./tpl/images/icon_sina.png"></a>
<a class="login-qq-btn" href="<?=$_FANWE['site_root']?>login.php?mod=qq" title="QQ登录"><img src="./tpl/images/icon_qq_qq.png"></a>
<a class="login-taobao-btn" href="<?=$_FANWE['site_root']?>login.php?mod=taobao" title="淘宝登录"><img src="./tpl/images/tao.png"></a>
</div>
<? } ?>
