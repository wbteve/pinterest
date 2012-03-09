<? if(!defined('IN_FANWE')) exit('Access Denied'); 
0
|| checkTplRefresh('./tpl/pink2/page/album/album_category.htm', './tpl/pink2/inc/pages.htm', 1331260973, './data/tpl/compiled/page_album_album_category.tpl.php', './tpl/pink2', 'page/album/album_category')
;?>
<?php 
$css_list[0]['url'] = './tpl/css/album.css';
$js_list[0] = './tpl/js/album.js';
 include template('inc/header'); ?><div id="body" class="fm960"> 
<div class="album_subnav"><? if(is_array($_FANWE['cache']['albums']['category'])) { foreach($_FANWE['cache']['albums']['category'] as $category) { if($category['id'] == $album_cate['id']) { ?>
<a href="<?=$category['url']?>" title="<?=$category['name']?>"><img src="<?=$category['img_hover']?>" /></a>
<? } else { ?>
<a href="<?=$category['url']?>" title="<?=$category['name']?>"><img src="<?=$category['img']?>" /></a>
<? } } } ?>
</div>
<div class="imagewall_sort">
<div class="fl" style="margin-left:0" >
<a>排序：</a>
<a href="<?php echo FU('album/category',array("id"=>$album_cate['id'],"sort"=>"new")); ?>"<? if($sort == 'new') { ?> class="c"<? } ?>>最新</a>
<a href="<?php echo FU('album/category',array("id"=>$album_cate['id'],"sort"=>"hot")); ?>"<? if($sort == 'hot') { ?> class="c"<? } ?>>最热</a>
</div>
</div>
<div class="blank12"></div>
<div id="album_container" class="albumwall album_td"><?php $col_index = 0; if(is_array($share_display)) { foreach($share_display as $share_col) { ?><?php $col_index++; ?><div class="col<?=$col_index?> col share_col"><? if(is_array($share_col)) { foreach($share_col as $share) { ?><div class="album_list_t" shareID="<?=$share['share_id']?>" id="SHARE_LIST_<?=$share['share_id']?>">
<div class="hd"></div>
<div class="bd">
<ul class="pic"><? if(is_array($share['imgs'])) { foreach($share['imgs'] as $share_img) { ?><li>
<a href="<?php echo FU('album/show',array("id"=>$share['rec_id'],"sid"=>$share['share_id'])); ?>" target="_blank"><img class="lazyload" original="<?php echo getImgName($share_img['img'],200,999,0); ?>" src="./tpl/images/lazyload.gif"></a>
<? if($share_img['type'] == 'g') { ?>
<span class="p">
<span>
<b><?=$share_img['price_format']?></b>
</span>
<i></i>
</span>
<? } ?>
</li>
<? } } ?>
</ul>
<!-- 喜欢 -->
<div class="favorite">
<a href="javascript:;" class="favaImg" onclick="$.Fav_Share(<?=$share['share_id']?>,this,32,'#SHARE_LIST_<?=$share['share_id']?>');">喜欢</a>
<div class="favDiv"> <a target="_blank" class="favCount SHARE_FAV_COUNT" href="<?=$share['url']?>"><?=$share['collect_count']?></a><i></i></div>
<span class="creply_n"> (<a href="<?=$share['url']?>" target="_blank"><?=$share['comment_count']?></a>)</span>
<a href="<?=$share['url']?>" class="creply">评论</a>
</div>
<!-- 原推内容 -->
<div class="who_share">
<div class="tk clearfix">
<? if(!empty($share['content']) && $share['content'] != $share['empty_content']) { ?>
<p><?php echo cutStr($share['content'],96,'...');?></p>
<? } ?>
<span class="share_mf"> </span>
</div>
<div class="ws_bd clearfix"><?php echo setTplUserFormat($share['uid'],1,0,'m',48,'','icard avt fl r3 lazyload',''); ?><div class="user_info"><?php echo setTplUserFormat($share['uid'],0,0,'',0,'n icard','',''); ?> <span class="add_album_u">加入杂志社</span><br />
<a target="_blank" href="<?php echo FU('album/show',array("id"=>$share['rec_id'])); ?>" class="share_album_n">《<?php echo cutStr($share['title'],16,'...');?>》</a>
</div>
</div>
</div>
</div>
<!-- 评论 -->
<? if(!empty($share['comments'])) { ?>
<ul class="rep_list"><? if(is_array($share['comments'])) { foreach($share['comments'] as $comment) { if($comment['uid']) { ?>
<li class="rep_f"><?php echo setTplUserFormat($comment['uid'],1,0,'s',24,'','avt icard fl r3 lazyload',''); ?><p class="colg"><?php echo setTplUserFormat($comment['uid'],0,0,'',0,'n icard fl','',''); ?>：<?=$comment['content']?>
</p>
</li>
<? } } } ?>
</ul>
<? } ?>
<div class="ws_ft"></div>
</div>
<? } } ?>
</div>
<? } } ?>
</div>
<div class="blank12"></div>
<? if($pager['page_count'] > 1) { ?>
<div class="pagination"> <? if($pager['page_count'] > 1) { ?>
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
<script type="text/javascript">
FANWE.NO_COUNTER = true;
var colHeight = 0;
var colIndex = 0;
var rowHtml = '<div class="album_list_t empty_row"><div class="hd"></div><div class="bd"></div><div class="ws_ft"></div></div>';
jQuery(function(){

<? if(getIsManage('album')) { ?>
$('.album_list_t').hover(function(){
var shareID = this.getAttribute('shareID');
if(shareID)
$.GetManageMenu('album',shareID,this);
},function(){});
<? } ?>

if($.browser.msie&&($.browser.version == "6.0")&&!$.support.style)
{
return ;
}
else{

$(".share_col").each(function(i){
$(this).append(rowHtml);
});
$(window).scroll(function(){
$(".share_col").each(function(i){
$('.empty_row .bd',this).height(0);
if($(this).height() > colHeight)
{
colIndex = i;
colHeight = $(this).height();
}
});

$(".share_col").each(function(i){
if(i != colIndex)
{
$('.empty_row .bd',this).height(colHeight - $(this).height());
}
});
});
}
});
</script><? include template('inc/footer'); ?>