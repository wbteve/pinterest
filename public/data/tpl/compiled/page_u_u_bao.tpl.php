<? if(!defined('IN_FANWE')) exit('Access Denied'); 
0
|| checkTplRefresh('./tpl/pink2/page/u/u_bao.htm', './tpl/pink2/inc/pages.htm', 1331261758, './data/tpl/compiled/page_u_u_bao.tpl.php', './tpl/pink2', 'page/u/u_bao')
;?>
<?php 
$css_list[0]['url'][] = './tpl/css/tweetlist.css';
$css_list[0]['url'][] = './tpl/css/zone.css';
$js_list[0] = './tpl/js/zone.js';
 include template('inc/header'); ?><div id="body" class="fm960">
<div class="homens_hd"></div>
<div class="homens_bd clearfix">
<div id="content" class="clearfix" style="width:960px;"><? include template('inc/u/u_menu'); ?><div class="content fr" style="width:820px;">
<div class="zone_head" style="margin:0 20px;">
<div class="mb15">
<h1 class="zone_title"><?=$user_names['short']?>的宝贝 (<?=$home_user['goods']?>)</h1>
</div>
</div>
<? if(empty($goods_list)) { ?>
<div class="empty">
<img alt="" class="fl" src="./tpl/images/fanwe4.png">
<span>呼~~这里还是空的~~</span>
</div> 
<? } else { ?>
<ul class="bao_olist clearfix"><? if(is_array($goods_list)) { foreach($goods_list as $goods) { ?><li>
<div class="bl_b">
<a target="_blank" href="<?=$goods['url']?>" ><img src="<?php echo getImgName($goods['img'],100,100,0); ?>" alt="<?=$goods['name']?>" /></a>
<span class="bl_p"><?=$goods['price']?></span>
</div>
<div class="b_l_fav"> <span class="b_l_fav_n"><?=$goods['collect_count']?></span> <i></i></div>
</li>
<? } } ?>
</ul>
<div class="pagination mt20"><? if($pager['page_count'] > 1) { ?>
<div class="pages">
<? if($pager['page'] > 1) { ?>
<a href="<?=$pager['page_prev']?>" class="page_prev" page="<?=$pager['prev_page']?>">&lt;上一页</a>
<? } if(is_array($pager['page_nums'])) { foreach($pager['page_nums'] as $page_num) { if($pager['page'] == $page_num['name']) { ?>
<a class="c"><?=$page_num['name']?></a>
<? } elseif($page_num['name'] == '...') { ?>
<i>...</i>
<? } else { ?>
<a href="<?=$page_num['url']?>" page="<?=$page_num['name']?>"><?=$page_num['name']?></a>
<? } } } if($pager['page'] < $pager['page_count']) { ?>
<a href="<?=$pager['page_next']?>" class="page_next" page="<?=$pager['next_page']?>">下一页&gt;</a>
<? } ?>
</div>
<? } ?> 
</div>
<? } ?>
</div>
</div>
</div>
<div class="homews_ft"></div>
</div><? include template('inc/footer'); ?>