<? if(!defined('IN_FANWE')) exit('Access Denied'); 
0
|| checkTplRefresh('./tpl/pink2/page/book/book_dapei.htm', './tpl/pink2/inc/pages.htm', 1331260938, './data/tpl/compiled/page_book_book_dapei.tpl.php', './tpl/pink2', 'page/book/book_dapei')
;?>
<?php 
$css_list[0]['url'][] = './tpl/css/general.css';
$css_list[0]['url'][] = './tpl/css/book.css';
$css_list[0]['url'][] = './tpl/css/categories.css';
 include template('inc/header'); ?><div id="body" class="container_16">
<div class="top_tags" id="cms308"> 
<ul style="min-height:180px;" class="top_tags_bd clearfix"> 
<li class="style_link">
<a class="c" href="<?php echo FU('book/dapei',array()); ?>">搭配秀</a>
<a href="<?php echo FU('book/look',array()); ?>">晒&nbsp;&nbsp;&nbsp;&nbsp;货</a>
<a href="<?php echo FU('style',array()); ?>">精选搭配</a>
</li><?php $tag_index = 1; if(is_array($category_tags)) { foreach($category_tags as $key => $cate) { ?><li<? if($tag_index == 1) { ?> class="w17"<? } ?>>
<span><?=$cate['cate_name']?></span>
<p><? if(is_array($cate['tags'])) { foreach($cate['tags'] as $k => $tag) { ?> 
<a href="<?=$tag['url']?>" <? if($tag['is_hot'] == 1) { ?>class="h"<? } ?>><?=$tag['tag_name']?></a> 
<? } } ?>
</p>
</li><?php $tag_index++; } } ?>
<li style="right:40px;" class="share"> 
<div class="show_style">
<a href="<?php echo FU('u/me',array()); ?>"><img src="./tpl/images/show-btn.png"></a>
</div>
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
<ul class="pic"><? if(is_array($share['dapei_imgs'])) { foreach($share['dapei_imgs'] as $share_img) { ?><li>
<a style="width:200px;" href="<?=$share_img['url']?>" target="_blank">
<img class="book_img lazyload" original="<?php echo getImgName($share_img['img'],200,999,0); ?>" src="./tpl/images/lazyload.gif" width=200 />
</a>
</li>
<? } } ?>
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
jQuery(function(){<!--dynamic getShareByUserClickShareID--><!--dynamic getManageDynamic args=book_dapei-->if($.browser.msie&&($.browser.version == "6.0")&&!$.support.style)
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