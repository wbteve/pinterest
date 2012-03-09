<? if(!defined('IN_FANWE')) exit('Access Denied'); 
0
|| checkTplRefresh('./tpl/pink2/page/u/u_book.htm', './tpl/pink2/inc/pages.htm', 1331261547, './data/tpl/compiled/page_u_u_book.tpl.php', './tpl/pink2', 'page/u/u_book')
;?>
<?php 
$css_list[0]['url'][] = './tpl/css/general.css';
$css_list[0]['url'][] = './tpl/css/book.css';
$js_list[0] = './tpl/js/book.js';
 include template('inc/header'); ?><div id="body" class="container_16">
<div class="imagewall_sort" style="height:40px;">
</div>
<div id="imagewall_container" class="imagewall">
<?php 
for($i=0;$i<=3;$i++)
{
$col_index = $i + 1;
 ?>
<div class="col<?=$col_index?> clear_in share_col">
<? if($i==0) { ?>
<div class="i_w_f o u_profile">
<div class="b_u_hd"></div>
<div class="b_u_bd">
<div class="up_info">
<a class="uavatar" href="<?php echo FU('u/index',array("uid"=>$home_uid)); ?>"><img src="<?php echo avatar($home_uid,'b',$home_user['server_code'],1);?>" width="100" height="100" /></a>
<a class="name gc" href="<?php echo FU('u/index',array("uid"=>$home_uid)); ?>"><?=$home_user['user_name']?></a>
<? if($is_show_follow) { ?>
<a href="javascript:;" class="add_fo" onclick="$.User_Follow(<?=$home_uid?>,this,UpdateUserFollow);"></a>
<? } elseif($home_uid != $_FANWE['uid']) { ?>
<a class="fo_st"></a>
<? } ?>
</div>
<? if(!empty($reside_province) || !empty($home_user['introduce'])) { ?>
<p class="ml5">
<div class="up_address">
<? if(!empty($reside_province)) { ?>
<?=$reside_province?> <?=$reside_city?>
<? } ?>
<p><?=$home_user['introduce']?></p>
</div>
</p>
<? } ?>
<div class="up_userhome">
<a href="<?php echo FU('u/index',array("uid"=>$home_uid)); ?>" target="_blank" class="gohome">去<?=$_FANWE['home_user_names']['short']?>的家</a>
<a href="<?php echo FU('u/topic',array("uid"=>$home_uid)); ?>" target="_blank">主题</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo FU('u/photo',array("uid"=>$home_uid)); ?>" target="_blank">相册</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo FU('u/bao',array("uid"=>$home_uid)); ?>" target="_blank">宝贝</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo FU('u/fav',array("uid"=>$home_uid)); ?>" target="_blank">喜欢</a>
</div>
<div class="up_title">
她关注的热点：
<ul class="nt_list"><? if(is_array($focus_tags)) { foreach($focus_tags as $r => $tag) { ?><li><a target="_blank" href="<? echo FU("book/shopping",array("tag"=>urlencode($tag['tag_name']))); ?>"><?=$tag['tag_name']?></a><i></i></li>
<? } } ?>
</ul>
</div>
</div>
<div class="b_u_ft"></div>
</div>
<? } if(is_array($share_display['col'.$i])) { foreach($share_display['col'.$i] as $k => $share) { ?><div class="i_w_f" id="share_item_<?=$share['share_id']?>">
<div class="hd"></div>
<div class="bd">
<ul class="pic"><? if(is_array($share['imgs'])) { foreach($share['imgs'] as $share_img) { if($share_img['type'] == 'g') { ?>
<li>
<a style="width:200px;" href="<?=$share_img['url']?>" target="_blank">
<img class="lazyload" original="<?php echo getImgName($share_img['img'],200,999,0); ?>" width=200 src="./tpl/images/lazyload.gif"/>
</a>
<span class="p"><span><?=$share_img['price_format']?></span><i></i></span>
<a class="add_to_album_btn" href="javascript:;" style="display: none;" onclick="$.Show_Rel_Album(<?=$share_img['id']?>,'goods');"></a>
</li>
<? } else { ?>
<li>
<a style="width:200px;" href="<?=$share_img['url']?>" target="_blank">
<img class="lazyload" original="<?php echo getImgName($share_img['img'],200,999,0); ?>" width=200 src="./tpl/images/lazyload.gif"/>
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
<span class="fl"><?php echo setTplUserFormat($share['uid'],0,0,'',0,'n icard','',''); ?></span>
<span class="t fr"><?=$share['time']?></span>
<span class="tkinfo clearfix"><?php echo cutStr($share['content'],200,'...');?></span>
</p>
</div>
<div class="ws_ft"></div>
</div>
</div>
<? } } ?>
</div>
<?php 
}
 ?>
</div>
<? if($pager['page_count'] > 1) { ?>
<div class="pagination pd_tb">  
     <? if($pager['page_count'] > 1) { ?>
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
<? } ?></div>
<? } ?>
<div class="clear"></div>
</div>
<script type="text/javascript">
FANWE.NO_COUNTER = true;
var colHeight = 0;
var colIndex = 0;
var rowHtml = '<div class="i_w_f_f empty_row"><div class="hd"></div><div class="bd"></div><div class="ft"></div></div>';
jQuery(function(){
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

function UpdateUserFollow(obj,result)
{
if(result.status == 1)
{
$(obj).before('<a class="fo_st"></a>');
$(obj).remove();
}
}
</script><? include template('inc/footer'); ?>