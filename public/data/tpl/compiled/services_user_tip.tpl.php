<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<div class="tip_info">
<img alt="<?=$user['user_name']?>" src="<?php echo avatar($user['uid'],'m',$user['server_code'],1);?>" class="avatar">
<div class="info">
<p><a class="uname" href="<?=$user['url']?>"><?=$user['user_name']?></a></p>
<p><?=$user['city']?></p>
<p>粉丝：<a target="_blank" href="<?php echo FU('u/fans',array("uid"=>$user['uid'])); ?>"><span><?=$user['fans']?></span></a><img src="./tpl/images/tip_bao.png">分享宝贝：<a target="_blank" href="<?php echo FU('u/bao',array("uid"=>$user['uid'])); ?>"><span><?=$user['goods']?></span></a></p>
</div>
<? if(!empty($user['introduce'])) { ?>
<div class="intro"><?=$user['introduce']?></div>
<? } else { ?>
<div class="intro"><span>真懒啊，连自我介绍都不写。</span></div>
<? } if(!empty($user['medals'])) { ?>
<div class="medal_a"><? if(is_array($user['medals'])) { foreach($user['medals'] as $medal) { ?><a target="_blank" href="<?=$medal['url']?>"><img title="<?=$medal['name']?>" alt="" src="<?=$medal['small_img']?>" width="20" height="20"></a>
<? } } ?>
</div>
<? } ?>
</div>
<div class="tip_toolbar"><!--dynamic getTipUserFollow args=<?=$user['uid']?>--><div class="blank3"></div>
</div>
<div class="tip_arrow"></div>