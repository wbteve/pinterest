<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<div class="menu fl">
<div class="profile">
<div class="p_a">
<div class="p_da"> <a href="<?php echo FU('u/book',array("uid"=>$home_uid)); ?>" target="_blank"><img src="<?php echo avatar($home_uid,'b',$home_user['server_code'],1);?>" width="100" alt="<?=$home_user['user_name']?>" /></a></div>
</div>
<div class="name"><a href="<?php echo FU('u/book',array("uid"=>$home_uid)); ?>" target="_blank" class="bc"><?=$home_user['user_name']?></a></div><!--getfollow <?=$home_uid?> inc/getfollow/u_menu--></div>
<ul class="zone_left_tab">
<li>
<h2>
<a class="z_t_a " style="background-position:0 -22px;" href="<?php echo FU('u/book',array("uid"=>$home_uid)); ?>">封面</a>
<a class="sc" href="<?php echo FU('settings/custom',array()); ?>" target="_blank">设置</a>
</h2>
</li>
<li<? if($current_menu == 'talk' || empty($current_menu)) { ?> class="c"<? } ?>>
<h2>
<a class="z_t_a" href="<?php echo FU('u/index',array("uid"=>$home_uid)); ?>" style="background-position:0 5px;">动态</a>
</h2>
</li>
<li<? if($current_menu == 'album') { ?> class="c"<? } ?>>
<h2>
<a class="z_t_a" href="<?php echo FU('u/album',array("uid"=>$home_uid)); ?>" style="background-position:0 -170px;">杂志社</a>
</h2>
</li>
<li<? if($current_menu == 'fav') { ?> class="c"<? } ?>>
<h2><a class="z_t_a" href="<?php echo FU('u/fav',array("uid"=>$home_uid)); ?>" style="background-position:0 -49px;">喜欢</a></h2>
</li>
<li<? if($current_menu == 'bao') { ?> class="c"<? } ?>>
<h2>
<a class="z_t_a" href="<?php echo FU('u/bao',array("uid"=>$home_uid)); ?>" style="background-position:0 -122px;">宝贝</a>
</h2>
</li>
<li<? if($current_menu == 'photo') { ?> class="c"<? } ?>>
<h2>
<a class="z_t_a" href="<?php echo FU('u/photo',array("uid"=>$home_uid)); ?>" style="background-position:0 -98px;">相册</a>
</h2>
</li>
<li<? if($current_menu == 'topic') { ?> class="c"<? } ?>>
<h2>
<a class="z_t_a" href="<?php echo FU('u/topic',array("uid"=>$home_uid)); ?>" style="background-position:0 -195px;">主题</a>
</h2>
</li>
<? if($home_uid == $_FANWE['uid']) { ?>
<li<? if($current_menu == 'message') { ?> class="c"<? } ?>>
<h2>
<a class="z_t_a" href="<?php echo FU('u/message',array("uid"=>$home_uid)); ?>" style="background-position:0 -216px;">信件</a>
</h2>
</li>
<li<? if($current_menu == 'exchange') { ?> class="c"<? } ?>>
<h2>
<a class="z_t_a" href="<?php echo FU('u/exchange',array("uid"=>$home_uid)); ?>" style="background-position:0 -145px;">兑换</a>
</h2>
</li>
<? } ?>
</ul>
</div>
<script type="text/javascript">
function UMenuUpdateUserFollow(obj,result)
{
var parent = $(obj).parent();
if(result.status == 1)
{
parent.html('<span class="followed">已关注</span><div class="followed_border"></div><a onclick="$.User_Follow(<?=$home_uid?>,this,UMenuUpdateUserFollow);" href="javascript:;" class="follow_del">取消</a>');
}
else
{
parent.html('<a onclick="$.User_Follow(<?=$home_uid?>,this,UMenuUpdateUserFollow);" href="javascript:;" class="addfo">加关注</a>');
}
}
</script>