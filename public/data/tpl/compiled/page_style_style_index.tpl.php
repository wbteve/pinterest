<? if(!defined('IN_FANWE')) exit('Access Denied'); 
0
|| checkTplRefresh('./tpl/pink2/page/style/style_index.htm', './tpl/pink2/inc/pages.htm', 1331265988, './data/tpl/compiled/page_style_style_index.tpl.php', './tpl/pink2', 'page/style/style_index')
;?>
<?php 
$css_list[0]['url'][] = './tpl/css/categories.css';
$css_list[0]['url'][] = './tpl/css/style.css';
 include template('inc/header'); ?><div id="body" class="fm960">
<div class="top_tags" id="cms308"> 
<ul style="min-height:180px;" class="top_tags_bd clearfix"> 
<li class="style_link">
<a href="<?php echo FU('book/dapei',array()); ?>">搭配秀</a>
<a href="<?php echo FU('book/look',array()); ?>">晒&nbsp;&nbsp;&nbsp;&nbsp;货</a>
<a class="c" href="<?php echo FU('style',array()); ?>">精选搭配</a>
</li><?php $tag_index = 1; if(is_array($category_tags)) { foreach($category_tags as $key => $cate) { ?><li<? if($tag_index == 2) { ?> class="w17"<? } ?>>
<span><?=$cate['cate_name']?></span>
<p><? if(is_array($cate['tags'])) { foreach($cate['tags'] as $k => $tag) { ?> 
<a href="<?=$tag['url']?>" <? if($tag['is_hot'] == 1) { ?>class="h"<? } ?>><?=$tag['tag_name']?></a> 
<? } } ?>
</p>
</li><?php $tag_index++; } } ?>
<li style="right:40px;" class="share"> 
<div class="show_style new_style">
<a href="<?php echo FU('u/me',array()); ?>" style="left:20px;"><img src="./tpl/images/pub_my_style.png"></a>
</div>
</li> 
</ul> 
<div class="top_tags_ft"></div> 
</div>
<div class="piece1 mt20 ml10">
<div class="piece1_hd"></div>
<div class="piece1_bd clearfix">
<div id="content" class="fl">
<div class="with_title">
<a href="<?php echo FU('style',array()); ?>" class="tlogo"></a>
<div class="descption"><?=$title?></div>
</div>
<div class="sort">
<a <? if($pop_url['act'] == 1) { ?>class="ac"<? } ?> href="<?=$pop_url['url']?>">朝流</a>
&nbsp;<span>|</span>&nbsp;
<a <? if($new_url['act'] == 1) { ?>class="ac"<? } ?> href="<?=$new_url['url']?>">NEW</a>
&nbsp;<span>|</span>&nbsp;
<a <? if($hot7_url['act'] == 1) { ?>class="ac"<? } ?> href="<?=$hot7_url['url']?>">一周人气</a>
&nbsp;<span>|</span>&nbsp;
<a <? if($hot30_url['act'] == 1) { ?>class="ac"<? } ?> href="<?=$hot30_url['url']?>">本月最热</a>
</div>
<ul class="mstyle"><? if(is_array($share_list)) { foreach($share_list as $share) { ?><li style="border:0;padding-top:0;" class="mogu_content clearfix" shareID="<?=$share['share_id']?>" id="share_item_<?=$share['share_id']?>">
<div class="image">
<a target="_blank" href="<?=$share['dapei_imgs']['0']['url']?>" ><img class="lazyload" original="<?php echo getImgName($share['dapei_imgs'][0]['img'],468,468,0); ?>" src="./tpl/images/lazyload.gif" width="320" /></a>
</div>
<div class="twict">
<div class="uname"><?php echo setTplUserFormat($share['uid'],0,1,'',0,'icard gc','',''); ?><!--getfollow <?=$share['uid']?> inc/getfollow/style_index--></div>
<p><img src="./tpl/images/mark_left.png" /><?=$share['content']?><img src="./tpl/images/mark_right.png" /></p>
<? if(!empty($share['tags']['user'])) { ?>
<div class="fashion">
<div class="sw_fashion">
<span>时尚元素：</span><? if(is_array($share['tags']['user'])) { foreach($share['tags']['user'] as $share_tag) { ?><a href="<?=$share_tag['url']?>" target="_blank"><?=$share_tag['tag_name']?></a>
<? } } ?>
</div>
</div>
<? } ?>
<div class="forward"><?=$share['time']?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:;" onclick="$.Relay_Share(<?=$share['share_id']?>);" class="mgs_forward">转发(<?=$share['relay_count']?>)</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?=$share['url']?>" class="replcae" target="_blank">评论(<?=$share['comment_count']?>)</a> </div>
<div class="note_who_like">
<div style="overflow:hidden;zoom:1;padding-bottom:10px;">
<a href="javascript:;" class="add_fav" onclick="$.Fav_Share(<?=$share['share_id']?>,this,39,'#share_item_<?=$share['share_id']?>');"> <img class="fl add_fav_new" src="./tpl/images/like.png"> </a> <span class="nwl_cfav"><span class="SHARE_FAV_COUNT"><?=$share['collect_count']?></span><i></i></span>
</div>
<div class="nwl_img">
<ul class="u_like SHARE_FAV_LIST"><? if(is_array($share['collects'])) { foreach($share['collects'] as $collect_uid) { ?><li><?php echo setTplUserFormat($collect_uid,1,0,'s',20,'','icard r3 GUID lazyload',''); ?></li>
<? } } ?>
</ul>
</div>
</div>
</div>
</li>
<? } } ?>
</ul>
<div class="pagination mt20"> <? if($pager['page_count'] > 1) { ?>
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
<div class="blank20"></div>
</div>
<div id="sidebar" class="fr">
<div class="style_hotweek mt30">
<div class="title">一周最热时尚搭配</div>
<ul><? if(is_array($share_week_hots)) { foreach($share_week_hots as $share) { ?><li>
<a class="wekhta" href="<?=$share['dapei_imgs']['0']['url']?>" target="_blank"><img src="<?php echo getImgName($share['dapei_imgs'][0]['img'],150,170,1); ?>" /></a>
<div class="info"><?php echo setTplUserFormat($share['uid'],0,0,'',0,'icard','',''); ?><span><?=$share['collect_count']?></span> 
</div>
</li>
<? } } ?>
</ul>
</div>
</div>
</div>
<div class="piece1_ft"></div>
</div>
</div>
<script type="text/javascript">
function UpdateUserFollow(obj,result)
{
if(result.status == 1)
$(obj).remove();
}

jQuery(function($){
<? if(getIsManage('share')) { ?>
$('.mogu_content').hover(function(){
var shareID = this.getAttribute('shareID');
$.GetManageMenu('dapei',shareID,this);
},function(){});
<? } ?>
});
</script><? include template('inc/footer'); ?>