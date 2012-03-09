<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<ul class="nav_1" id="home_nav">
    <li class="nav_f f<? if($active == '' || $active == 'index') { ?> h<? } ?>">
    	<span class="lt"></span>
<div class="md"><a href="<?php echo FU('u/index',array("uid"=>$uid)); ?>"></a></div>
<span class="rt"></span>
</li>
<li<? if($active == 'topic') { ?> class="h"<? } ?>>
<span class="lt"></span>
<div class="md"><a href="<?php echo FU('u/topic',array("uid"=>$uid)); ?>">主题<br><span class="c"><?=$user['threads']?></span></a></div>
<span class="rt"></span>
</li>
<li<? if($active == 'photo') { ?> class="h"<? } ?>>
<span class="lt"></span>
<div class="md"><a href="<?php echo FU('u/photo',array("uid"=>$uid)); ?>">相册<br><span class="c"><?=$user['photos']?></span></a></div>
<span class="rt"></span>
</li>
<li<? if($active == 'bao') { ?> class="h"<? } ?>>
<span class="lt"></span>
<div class="md"><a href="<?php echo FU('u/bao',array("uid"=>$uid)); ?>">宝贝<br><span class="c"><?=$user['goods']?></span></a></div>
<span class="rt"></span>
</li>
<li<? if($active == 'fav') { ?> class="h"<? } ?>>
<span class="lt"></span>
<div class="md"><a href="<?php echo FU('u/fav',array("uid"=>$uid)); ?>">喜欢<br><span class="c"><?=$user['favs']?></span></a></div>
<span class="rt"></span>
</li>
<li class="hb">
<div class="home_user_hb">
<img src="<?php echo avatar($uid,'s',$user['server_code'],1);?>" class="huh_avt" width="24" height="24" /><!--[if IE 6]><div></div><![endif]--><span class="n"><a href="<?php echo FU('u/book',array("uid"=>$uid)); ?>" target="_blank">
            <?php echo sprintf('%s的画报',$user['user_name']); ?></a></span><!--[if IE 6]><div></div><![endif]--><i></i>
        </div>
</li>
</ul>