<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<div class="ff_inf mb25 mt5 pl5">
<div class="user_at clearfix"> </div>
<ul class="clearfix">
<li style="padding-left:0">
<a href="<?php echo FU('u/follow',array("uid"=>$home_uid)); ?>">关注</a><br>
<a href="<?php echo FU('u/follow',array("uid"=>$home_uid)); ?>"><span><?=$home_user['follows']?></span></a>
</li>
<li>
<a href="<?php echo FU('u/fans',array("uid"=>$home_uid)); ?>">粉丝</a><br>
<a href="<?php echo FU('u/fans',array("uid"=>$home_uid)); ?>"><span ><?=$home_user['fans']?></span></a>
</li>
<li style="background-image:none;">
<a>被喜欢</a><br>
<a><span style="color:#f39;"><?=$home_user['collects']?></span></a>
</li>
</ul>
</div>
<div class="per_inf mb25">
<h3 class="mb10">个人信息</h3>
<? if($home_uid == $_FANWE['uid']) { ?>
<a class="pi_setting" href="<?php echo FU('settings/personal',array()); ?>" target="_blank">设置</a>
<? } ?>
<p class="mb5 ml5 mr10">
<?=$home_user['introduce']?>
</p>
<? if(!empty($reside_province)) { ?>
<p class="ml5">
地区：<span><?=$reside_province?></span> 
<span><?=$reside_city?></span>
</p>
<? } ?>
</div>
<div class="mb25 medals">
<h3 class="mb10">时尚勋章</h3>
<a class="pi_setting" href="<?php echo FU('medal/u',array("uid"=>$home_uid)); ?>" target="_blank">更多</a>
<?php 
$medals = FS('User')->getUserMedal($home_uid);
 ?>
<ul class="ml5"><? if(is_array($medals)) { foreach($medals as $medal) { ?><li class="medal_f"><a href="<?php echo FU('medal/u',array("uid"=>$home_uid)); ?>" target="_blank"><img src="<?=$medal['small_img']?>" height="25" alt="<?=$medal['name']?>" title="<?=$medal['name']?>"></a></li>
<? } } ?>
  	</ul>
</div>
<div class="mb25 hot_event">
<h3 class="mb10">热门活动</h3>
<ul><? if(is_array($hot_events)) { foreach($hot_events as $hot_event) { ?><li>
<a target="_blank" href="<?=$hot_event['url']?>"><img class="hot_pic" width="50" height="50" src="<?php echo getImgName($hot_event['imgs'][0]['img'],100,100,0); ?>" /></a>
<div class="hot_title">
<p><a target="_blank" href="<?=$hot_event['url']?>"><?php echo cutStr($hot_event['title'],46,'...');?></a></p>
<?=$hot_event['thread_count']?>回应 
</div>
</li>
<? } } ?>
</ul>
</div>
<? if(!empty($today_daren)) { ?>
<div class="mb25 tdaren">
<h3 class="mb10">今日达人</h3>
<a href="<?=$today_daren['user']['url']?>"><img class="ml10" style="width:180px;" src="<?=$today_daren['img']?>"></a>
<div class="tl">
<a href="<?=$today_daren['user']['url']?>" class="n GUID" uid="<?=$today_daren['user']['uid']?>"><?=$today_daren['user']['user_name']?></a><!--getfollow <?=$today_daren['user']['uid']?> inc/getfollow/u_me_list--></div>
</div>
<script type="text/javascript">
function UpdateDarenUserFollow(obj,result)
{
var parent = $(obj).parent();
if(result.status == 1)
{
$(obj).before('<img src="./tpl/images/add_fo_ok.png" class="fo_ok fr">');
$(obj).remove();
}
}
</script>
<? } ?>