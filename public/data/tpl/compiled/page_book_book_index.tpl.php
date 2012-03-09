<? if(!defined('IN_FANWE')) exit('Access Denied'); 
0
|| checkTplRefresh('./tpl/pink2/page/book/book_index.htm', './tpl/pink2/inc/pages.htm', 1331260961, './data/tpl/compiled/page_book_book_index.tpl.php', './tpl/pink2', 'page/book/book_index')
;?>
<?php 
$css_list[0]['url'][] = './tpl/css/general.css';
$css_list[0]['url'][] = './tpl/css/book.css';
 include template('inc/header'); ?><link rel="stylesheet" type="text/css" href="/tpl/uu43/css/book.css" />
<div id="body" class="container_16">
<div class="blank20"></div>
<div class="box_shadow" style="width:940px; margin:0 10px; overflow:hidden;">
<div class="catalog_guide_wrapper">
<ul class="catalog_guide f14">
<?php 
$cate_active = false;
$g_r_cate = $_FANWE['cache']['goods_category']['root'];
$g_r_cate = &$_FANWE['cache']['goods_category']['all'][$g_r_cate];

if(MODULE_NAME == 'Book' && ACTION_NAME == 'shopping')
$cate_active = true;

$g_i_cates = &$_FANWE['cache']['goods_category']['parent'];
 ?>
<li><a href='<?php echo FU('book/shopping',array()); ?>' class="cgoods<? if($cate_active) { ?>_1 red<? } ?>"><? if($cate_active) { ?><span class="i">社区热荐</span><? } else { ?>社区热荐<? } ?></a></li><? if(is_array($g_i_cates)) { foreach($g_i_cates as $g_i_cate_id) { ?><?php 
$g_i_cate = &$_FANWE['cache']['goods_category']['all'][$g_i_cate_id];
$g_i_code = &$g_i_cate['cate_code'];
if(MODULE_NAME == 'Book' && $goods_cate_code == $g_i_code)
$cate_active = true;
else
$cate_active = false;
 ?>
<li><a href='<?php echo FU('book/cate',array("cate"=>$g_i_code)); ?>' class="<? if($cate_active) { ?>red<? } ?>" style="background:none;"><? if($cate_active) { ?><span class="i"><?=$g_i_cate['cate_name']?></span><? } else { ?><?=$g_i_cate['cate_name']?><? } ?></a></li>
<? } } ?>
</ul>
</div>
</div>
<div class="blank12"></div>
<div class="top_tags">
<ul class="top_tags_bd">
<?php  
$total_tag_col = count($category_tags);
if(!empty($hot_tags))
$total_tag_col++;
$tag_width = intval((750 - ($total_tag_col * 30)) / $total_tag_col);
 if(!empty($hot_tags)) { ?>
<li style="width:<? echo $tag_width; ?>px;"> <span>热门标签</span>
<p> <? if(is_array($hot_tags)) { foreach($hot_tags as $k => $tag) { ?> <a href="<?=$tag['url']?>" <? if($tag['is_hot'] == 1) { ?>class="h"<? } ?>><?=$tag['tag_name']?></a> <? } } ?> </p>
</li>
<? } if(is_array($category_tags)) { foreach($category_tags as $key => $cate) { ?><li style="width:<? echo $tag_width; ?>px;"> <span><?=$cate['cate_name']?></span>
<p> <? if(is_array($cate['tags'])) { foreach($cate['tags'] as $k => $tag) { ?> 
<a href="<?=$tag['url']?>" <? if($tag['is_hot'] == 1) { ?>class="h"<? } ?>><?=$tag['tag_name']?></a> 
<? } } ?>
</p>
</li>
<? } } ?>
<li class="share">
<img src="<?=$category_data['cate_icon']?>"> <br>
<?=$category_data['desc']?><a class="share_bao" href="<?php echo FU('u/index',array()); ?>">推荐宝贝</a>
</li>
</ul>
<div class="top_tags_ft"></div>
</div>
<div class="imagewall_nav">
<div class="imagewall_sort">
<h1 class="fl"><?=$title?></h1>
<div style="margin-left:20px;" class="fl">
<a>排序：</a>
<a <? if($pop_url['act'] == 1) { ?>class="c"<? } ?> href="<?=$pop_url['url']?>">潮流</a>
<a <? if($new_url['act'] == 1) { ?>class="c"<? } ?> href="<?=$new_url['url']?>">最新</a>
<a <? if($hot7_url['act'] == 1) { ?>class="cc"<? } ?> href="<?=$hot7_url['url']?>" style="width:68px;">7天最热</a>
<a <? if($hot30_url['act'] == 1) { ?>class="cc"<? } ?> href="<?=$hot30_url['url']?>" style="width:68px;">30天最热</a>
</div>
</div>
<div class="imagewall" id="imagewall_container"><?php $col_index = 0; if(is_array($share_display)) { foreach($share_display as $share_col) { ?><?php $col_index++; ?><div class="col<?=$col_index?> clear_in share_col"><? if(is_array($share_col)) { foreach($share_col as $share) { ?><div class="i_w_f" shareID="<?=$share['share_id']?>" id="share_item_<?=$share['share_id']?>">
<div class="hd"></div>
<div class="bd">
<ul class="pic"><? if(is_array($share['imgs'])) { foreach($share['imgs'] as $share_img) { if($share_img['type'] == 'g') { ?>
<li>
<a style="width:200px;" href="<?=$share_img['url']?>" target="_blank">
<img class="book_img lazyload" original="<?php echo getImgName($share_img['img'],200,999,0); ?>" width=200 src="./tpl/images/lazyload.gif"/>
</a>
<span class="p"><span><?=$share_img['price_format']?></span><i></i></span>
<a class="add_to_album_btn" href="javascript:;" style="display: none;" onclick="$.Show_Rel_Album(<?=$share_img['id']?>,'goods');"></a>
</li>
<? } else { ?>
<li>
<a style="width:200px;" href="<?=$share_img['url']?>" target="_blank">
<img class="book_img lazyload" original="<?php echo getImgName($share_img['img'],200,999,0); ?>" width=200 src="./tpl/images/lazyload.gif"/>
</a>
<a class="add_to_album_btn" href="javascript:;" style="display: none;" onclick="$.Show_Rel_Album(<?=$share_img['id']?>,'photo');"></a>
</li>
<? } } } ?>
</ul>
<div class="favorite">
<a href="javascript:;" class="favaImg" onclick="$.Fav_Share(<?=$share['share_id']?>,this,32,'#share_item_<?=$share['share_id']?>');"></a>
<div class="favDiv"><a target="_blank" class="favCount SHARE_FAV_COUNT" href="<?=$share['url']?>"  ><?=$share['collect_count']?></a></div>
<a target="_blank" href="<?=$share['url']?>" class="creply"><b><?=$share['comment_count']?></b>评论</a>
</div>
</div>
<div class="who_share">
<div class="ws_bd clearfix"><?php echo setTplUserFormat($share['uid'],1,0,'s',24,'','icard avt fl lazyload',''); ?><p class="fr clearfix">
<span class="t fl"><?php echo setTplUserFormat($share['uid'],0,0,'',0,'n icard','',''); ?></span>
<span class="t fr"><?=$share['time']?></span>
<span class="tkinfo clearfix"><?php echo cutStr($share['content'],200,'...');?></span>
</p>
</div>
<div class="ws_ft"></div>
</div>
</div>
<? } } ?>
</div>
<? } } ?>
</div>
<? if($pager['page_count'] > 1) { ?>
<div style="padding-top:30px" class="clr">
<div class="pagination pd_tb"> <? if($pager['page_count'] > 1) { ?>
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
</div>
<? } ?>
<div class="clear"></div>
</div>
</div>
<script type="text/javascript">
FANWE.NO_COUNTER = true;
var colHeight = 0;
var colIndex = 0;
var rowHtml = '<div class="i_w_f_f empty_row"><div class="hd"></div><div class="bd"></div><div class="ft"></div></div>';
jQuery(function(){<!--dynamic getShareByUserClickShareID--><!--dynamic getManageDynamic args=book_index-->if($.browser.msie&&($.browser.version == "6.0")&&!$.support.style)
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

function ShowUserClickShare(result)
{
if($("#share_item_" + result.share_id).length > 0)
{
var thisshare = $("#share_item_" + result.share_id);
var preshare = thisshare.prev();
var parentcol = thisshare.parent();
$(".col1").prepend(thisshare);
if(!parentcol.hasClass("col1") && $(".col1 .i_w_f").length > 1)
{
if(preshare.get(0))
preshare.after($(".col1 .i_w_f").eq(1));
else
parentcol.prepend($(".col1 .i_w_f").eq(1));
}
}
else
$(".col1").prepend(result.html);
}
</script><? include template('inc/footer'); ?>