<? if(!defined('IN_FANWE')) exit('Access Denied'); 
0
|| checkTplRefresh('./tpl/pink2/page/u/u_album.htm', './tpl/pink2/inc/pages.htm', 1331261749, './data/tpl/compiled/page_u_u_album.tpl.php', './tpl/pink2', 'page/u/u_album')
;?>
<?php 
$css_list[0]['url'][] = './tpl/css/zone.css';
$js_list[0] = './tpl/js/zone.js';
 include template('inc/header'); ?><link rel="stylesheet" type="text/css" href="./tpl/uu43/css/zone.css" />
<div id="body" class="fm960">
<div class="homens_hd"></div>
<div class="homens_bd clearfix">
<div id="content" class="clearfix" style="width:960px;"><? include template('inc/u/u_menu'); ?><div class="content fr" style="width:820px;">
<div class="zone_head myalbum_title">
<div class="mb15">
<h1 class="zone_title"><?=$_FANWE['home_user_names']['short']?>的杂志社</h1>
<a href="<?php echo FU('album/create',array()); ?>" class="zone_pub" >创建杂志社</a> </div>
<div>
<div class="zone_cat fl">
<a href="<?php echo FU('u/album',array("uid"=>$home_uid,"type"=>"1")); ?>"<? if($type == 1) { ?> class="c"<? } ?>><?=$_FANWE['home_user_names']['short']?>发表的</a><span>|</span> <a href="<?php echo FU('u/album',array("uid"=>$home_uid,"type"=>"2")); ?>"<? if($type == 2) { ?> class="c"<? } ?>>关注的人</a><span>|</span> <a href="<?php echo FU('u/album',array("uid"=>$home_uid,"type"=>"3")); ?>"<? if($type == 3) { ?> class="c"<? } ?>><?=$_FANWE['home_user_names']['short']?>推荐的</a>
</div>
</div>
</div>
<? if(empty($album_list)) { ?>
<div class="empty">
<img alt="" class="fl" src="./tpl/images/fanwe4.png">
<span>呼~~这里还是空的~~</span>
</div> 
<? } else { ?>
<ul class="album_all_pic clearfix"><? if(is_array($album_list)) { foreach($album_list as $album) { ?><li class="all_list" >
<div class="album_title">
<a href="<?=$album['url']?>"><?php echo cutStr($album['title'],30,'...');?></a> <span class="pic_n"> <span><?=$album['img_count']?>&nbsp;图</span> <i></i> </span>
</div>
<ul class="all_pic_s"><?php $list_img_counts = array(0,0,0,0,0,0); if(is_array($list_img_counts)) { foreach($list_img_counts as $imgkey => $imgindex) { ?><?php $img = $album['imgs'][$imgkey]; if(!empty($img)) { ?>
<li><a target="_blank" href="<?=$album['url']?>"><img src="<?php echo getImgName($img['img'],100,100,0); ?>" /></a></li>
<? } else { ?>
<li><a target="_blank" class="add_img" href="<?=$album['url']?>"><img src="./tpl/images/none_pic.png" /></a></li>
<? } } } ?>
</ul>
<? if($type > 1) { ?><?php echo setTplUserFormat($album['uid'],0,1,'',0,'icard name gc','',''); } ?>
<p><?php echo cutStr($album['content'],98,'...');?></p>
</li>
<? } } ?>
</ul>
<div class="pagination pt15"><? if($pager['page_count'] > 1) { ?>
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