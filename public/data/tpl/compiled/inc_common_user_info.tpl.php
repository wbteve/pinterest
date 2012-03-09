<? if(!defined('IN_FANWE')) exit('Access Denied'); if($_FANWE['uid'] > 0) { ?>
<div class="fr" style="margin-top:20px">
<div class="my_btn">
<a href="<?php echo FU('u/me',array()); ?>" class="mb_bd<? if($_FANWE['user']['gender'] == 1) { ?> b<? } ?>">
<img class="mb_avt" src="<?php echo avatar($_FANWE['uid'],'s',$_FANWE['user']['server_code'],1);?>" height="24">
<span class="mb_name<? if($_FANWE['user']['gender'] == 1) { ?> bc<? } else { ?> gc<? } ?>"><?=$_FANWE['user_name']?></span>
</a>
<span class="mb_rb">
<a class="myalbum" href="<?php echo FU('u/album',array()); ?>">杂志社</a>
<a href="<?php echo FU('u/fav',array("uid"=>$_FANWE['uid'])); ?>" class="myfavs">喜欢</a>
</span>
</div>
<div class="my_shotcuts">
<a href="<?php echo FU('settings/personal',array()); ?>" class="setting">设置</a>
<a href="javascript:;" class="message<? if($_FANWE['user_notice']['all'] > 0) { ?> h<? } ?>">消息<? if($_FANWE['user_notice']['all'] > 0) { ?>(<?=$_FANWE['user_notice']['all']?>)<? } ?></a>
<a href="<?php echo FU('invite',array()); ?>" class="setting">邀请</a>
<a href="<?php echo FU('user/logout',array()); ?>" >退出</a>
</div>
    <div class="notice_menu_box fl">
        <ul id="notice_menu" class="s_m">
            <li>
<a href="<?php echo FU('u/talk',array("uid"=>$_FANWE['uid'])); ?>">我发表的</a>
            </li>
            <li>
            	<a href="<?php echo FU('u/comments',array("uid"=>$_FANWE['uid'])); ?>">评论我的<? if($_FANWE['user_notice']['3'] >0) { ?><span>(<?=$_FANWE['user_notice']['3']?>)</span><? } ?></a>
            </li>
            <li>
            	<a href="<?php echo FU('u/fans',array("uid"=>$_FANWE['uid'])); ?>">关注我的<? if($_FANWE['user_notice']['1'] >0) { ?><span>(<?=$_FANWE['user_notice']['1']?>)</span><? } ?></a>
            </li>
<li>
            	<a href="<?php echo FU('u/atme',array("uid"=>$_FANWE['uid'])); ?>">提到我的<? if($_FANWE['user_notice']['4'] >0) { ?><span>(<?=$_FANWE['user_notice']['4']?>)</span><? } ?></a>
            </li>
            <li>
            	<a href="<?php echo FU('u/atme',array("uid"=>$_FANWE['uid'],"type"=>"faved")); ?>">喜欢我的<? if($_FANWE['user_notice']['2'] >0) { ?><span>(<?=$_FANWE['user_notice']['2']?>)</span><? } ?></a>
            </li>
            <li>
            	<a href="<?php echo FU('u/message',array("uid"=>$_FANWE['uid'])); ?>">我的信件<? if($_FANWE['user_notice']['5'] >0) { ?><span>(<?=$_FANWE['user_notice']['5']?>)</span><? } ?></a>
            </li>
        </ul>
</div>
</div>
<? } else { ?>
<ul class="l">
<li class="o_l">
<span><img class="fl" src="./tpl/images/icon_sina.png"><a class="fl" href="<?=$_FANWE['site_root']?>login.php?mod=sina">微博登录</a></span>
<span><img class="fl" src="./tpl/images/icon_qq_qq.png"><a class="fl" href="<?=$_FANWE['site_root']?>login.php?mod=qq">QQ登录</a></span>
<span><img class="fl" src="./tpl/images/tao.png"><a class="fl" href="<?=$_FANWE['site_root']?>login.php?mod=taobao">淘宝登录</a></span>
        <span><img class="fl" src="./tpl/images/icon_qq.png"><a class="fl" href="<?=$_FANWE['site_root']?>login.php?mod=tqq">腾讯微博</a></span>
</li>
<li class="f">
<a href="<?php echo FU('user/login',array()); ?>">登陆</a>
</li>
<li class="login">
<a href="<?php echo FU('user/register',array()); ?>" target="_blank">注册</a>
</li>
</ul>
<? } ?>
