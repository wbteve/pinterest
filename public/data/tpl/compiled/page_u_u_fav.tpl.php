<? if(!defined('IN_FANWE')) exit('Access Denied'); 
0
|| checkTplRefresh('./tpl/pink2/page/u/u_fav.htm', './tpl/pink2/inc/u/u_fav.htm', 1331261747, './data/tpl/compiled/page_u_u_fav.tpl.php', './tpl/pink2', 'page/u/u_fav')
;?>
<?php 
$css_list[0]['url'][] = './tpl/css/tweetlist.css';
$css_list[0]['url'][] = './tpl/css/zone.css';
$js_list[0] = './tpl/js/zone.js';
 include template('inc/header'); ?><div id="body" class="fm960">
<div class="homews_hd"></div>
<div class="homews_bd clearfix">
<div id="content" class="fl" style="width:730px;"><? include template('inc/u/u_menu'); ?><div class="content fr" style="width:595px;">
<?=$share_list_html?>
</div>
</div>
<div id="sidebar" class="fr pl15" style="width:215px;"><div class="mb25 s_hot_pic">
<h3 class="mb10">她们的喜欢</h3>
<ul class="mr20 nb"><? if(is_array($fav_list)) { foreach($fav_list as $fav_item) { ?><li><?php echo setTplUserFormat($fav_item['uid'],0,0,'',0,'n gc','',''); ?><a href="<?=$fav_item['url']?>" class="time"><?=$fav_item['time']?></a>
<div class="shp_img mt5 clearfix"><?php $fav_img_index=0; if(is_array($fav_item['imgs'])) { foreach($fav_item['imgs'] as $fav_img) { if($fav_img_index > 2) { ?><?php break; } ?>
<a href="<?=$fav_img['url']?>"><img src="<?php echo getImgName($fav_img['img'],100,100,0); ?>"></a><?php $fav_img_index++; } } ?>
</div>
</li>
<? } } ?>
</ul>
</div>
</div>
</div>
<div class="homews_ft"></div>
</div><? include template('inc/footer'); ?>