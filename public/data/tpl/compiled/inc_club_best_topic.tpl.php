<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<div class="good_topic">
<h2>精彩主题...</h2><? if(is_array($best_list)) { foreach($best_list as $topic) { ?><span><?php echo cutStr($topic['title'],42,'...');?></span>
<ul class="show_img"><? if(is_array($topic['imgs'])) { foreach($topic['imgs'] as $img) { ?><li>
<a href="<?=$topic['url']?>"><img src="<?php echo getImgName($img['img'],100,100,0); ?>" /></a>
</li>
<? } } ?>
</ul>
<? } } ?>
</div>