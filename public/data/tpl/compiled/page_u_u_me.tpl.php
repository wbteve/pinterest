<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<?php 
$css_list[0]['url'][] = './tpl/css/tweetlist.css';
$css_list[0]['url'][] = './tpl/css/zone.css';
$js_list[0] = './tpl/js/zone.js';
 include template('inc/header'); ?><div id="body" class="fm960">
<div class="homews_hd"></div>
<div class="homews_bd clearfix">
<div id="content" class="fl" style="width:730px;"><? include template('inc/u/u_menu'); ?><div class="content fr" style="width:595px;">
<? if($home_uid == $_FANWE['uid']) { include template('inc/u/u_publish_share'); ?><ul class="zone_tab clearfix">
<li class="zt_f<? if(ACTION_NAME == 'me') { ?> c<? } ?>"><a href="<?php echo FU('u/index',array("uid"=>$home_uid)); ?>">全部</a></li>
<li class="zt_f<? if(ACTION_NAME == 'talk') { ?> c<? } ?>"><a href="<?php echo FU('u/talk',array("uid"=>$home_uid)); ?>"><?=$_FANWE['home_user_names']['short']?>发表的</a></li>
<li class="zt_f<? if(ACTION_NAME == 'atme') { ?> c<? } ?>"><a href="<?php echo FU('u/atme',array("uid"=>$home_uid)); ?>">@我的</a></li>
<li class="zt_f<? if(ACTION_NAME == 'comments') { ?> c<? } ?>"><a href="<?php echo FU('u/comments',array("uid"=>$home_uid)); ?>">评论<?=$_FANWE['home_user_names']['short']?>的</a></li>
<li class="zt_f<? if(ACTION_NAME == 'all') { ?> c<? } ?>"><a href="<?php echo FU('u/all',array()); ?>">随便逛逛</a></li>
</ul>
<? } if(ACTION_NAME == 'atme') { ?>
<div class="talk_tab ml20 mt10">
<a <? if($type == '') { ?>class="c"<? } ?> href="<?php echo FU('u/atme',array("uid"=>$home_uid)); ?>">提到我的</a>
<i>|</i>
<a <? if($type == 'faved') { ?>class="c"<? } ?> href="<?php echo FU('u/atme',array("uid"=>$home_uid,"type"=>"faved")); ?>">喜欢我的</a>
</div>
<? } ?>
<div id="talk_list_box" newshow="do">
<?=$share_list_html?>
</div>
</div>
</div>
<div id="sidebar" class="fr pl15" style="width:215px;"><? include template('inc/u/u_me_list'); ?></div>
</div>
<div class="homews_ft"></div>
</div><? include template('inc/footer'); ?>