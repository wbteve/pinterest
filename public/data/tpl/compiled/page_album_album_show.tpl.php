<? if(!defined('IN_FANWE')) exit('Access Denied'); 
0
|| checkTplRefresh('./tpl/pink2/page/album/album_show.htm', './tpl/pink2/page/album/album_comment.htm', 1331261947, './data/tpl/compiled/page_album_album_show.tpl.php', './tpl/pink2', 'page/album/album_show')
|| checkTplRefresh('./tpl/pink2/page/album/album_show.htm', './tpl/pink2/page/album/album_comment.htm', 1331261947, './data/tpl/compiled/page_album_album_show.tpl.php', './tpl/pink2', 'page/album/album_show')
|| checkTplRefresh('./tpl/pink2/page/album/album_show.htm', './tpl/pink2/inc/pages.htm', 1331261947, './data/tpl/compiled/page_album_album_show.tpl.php', './tpl/pink2', 'page/album/album_show')
;?>
<?php 
$css_list[0]['url'] = './tpl/css/imagewall.css';
$css_list[1]['url'] = './tpl/css/album.css';
$js_list[0] = './tpl/js/album.js';
 include template('inc/header'); ?><div id="body" class="fm960"> 
<!--专辑头 -->
<div class="mt30 subnav">
<a href="<?php echo FU('album',array()); ?>"><img style="margin-right:3px;" src="./tpl/images/album_bg_start.png" />杂志社</a>/ <a href="<?=$album_cate['url']?>"><?=$album_cate['name']?></a> /<a href="<?php echo FU('u/album',array("uid"=>$album['uid'])); ?>"><?=$album_user['user_name']?>的杂志社</a>
</div>
<div class="piece1 mb20 mt10">
<div class="piece1_hd"></div>
<div class="piece1_bd" id="albumShowBox" shareID="<?=$album['share_id']?>">
<div class="album_show_title clearfix">
<div class="user_avatar"><?php echo setTplUserFormat($album['uid'],1,0,'m',48,'','avt icard r5',''); ?><div class="followdiv"><!--getfollow <?=$album['uid']?> inc/getfollow/album--></div>
</div>
<div class="album_intro fl">
<h1 class="title">
<?=$album['title']?>
<div class="album_mana">
<? if($is_manage_album) { ?>
<a href="<?php echo FU('album/edit',array("id"=>$album['id'])); ?>" class="ea_edit">编辑</a>
<a href="javascript:;" class="del" onclick="$.Remove_Album(<?=$album['id']?>,this,'<?php echo FU('album',array()); ?>')">删除</a>
<? } ?>
</div>
</h1><?php echo setTplUserFormat($album['uid'],0,1,'',0,'uname gc icard','',''); ?><span style="color: #BBB;padding-left: 5px;"><?php echo fToDate($album['create_time']); ?></span>
<pre class="album_content"><?=$album['content']?></pre>
<div class="element">
时尚元素：<? if(is_array($album['tags'])) { foreach($album['tags'] as $tag) { ?><a href="<? echo FU('book/shopping',array('tag'=>urlencode($tag))); ?>"><?=$tag?></a>
<? } } ?>
</div>
</div>
<div class="album_imginfo fl">
<ul>
<li style="padding-left:0">
<a href="<?php echo FU('u/bao',array("uid"=>$album['uid'])); ?>">商品</a><br>
<span><?=$album['goods_count']?></span>
</li>
<li>
<a href="<?php echo FU('u/photo',array("uid"=>$album['uid'])); ?>">图片</a><br>
<span><?=$album['photo_count']?></span>
</li>
<li style="background-image:none;">
<a>被喜欢</a><br>
<span style="color:#f39;"><?=$album['collect_count']?></span>
</li>
</ul>
<div class="fo_album clr mt15" id="fo_album">
<? if($album['uid'] == $_FANWE['uid']) { ?>
<span class="followed r3"><?=$album['best_count']?>人推荐</span>
<? } else { if($is_best_album) { ?>
<span class="followed r3">已推荐</span>
<span class="num"><?=$album['best_count']?>人</span>
<a class="follow_del" href="javascript:void(0);" onclick="$.Remove_Best_Album(<?=$album['id']?>,this,UpdateAlbumHandler);">取消</a>
<? } else { ?>
<a class="follow_add fl" href="javascript:;" onclick="$.Best_Album(<?=$album['id']?>,UpdateAlbumHandler);">推荐专辑</a>
<span class="num ml10"><?=$album['best_count']?>人</span>
<? } } ?>
</div>
<script type="text/javascript">
function UpdateAlbumHandler(result)
{
$("#fo_album").html(result.html);
}
</script>
</div>
</div>
</div>
<div class="piece1_ft"></div>
</div>
<? if($is_manage_album) { ?>
<div class="add_album_content mb20 clearfix">
<a href="<?php echo FU('u/me',array()); ?>" class="add_album_content_btn" target="_blank">添加内容</a>
<!--<span class="fl">快速添加网络图片:</span>
<input type="text" class="url fl" value="http://...小技巧：输入网址即可获取网页中所有图片"><input type="button" class="add fl" value="加入杂志社">-->
</div>
<? } ?>
<div class="presentation">
<a style="width:80px;">展示方式：</a>
<a href="<?php echo FU('album/show',array("id"=>$album['id'],"type"=>"3")); ?>"<? if($show_type == 3) { ?> class="c"<? } ?>>大图</a>
<a href="<?php echo FU('album/show',array("id"=>$album['id'],"type"=>"2")); ?>"<? if($show_type == 2) { ?> class="c"<? } ?>>中图</a>
<a href="<?php echo FU('album/show',array("id"=>$album['id'],"type"=>"1")); ?>"<? if($show_type == 1) { ?> class="c"<? } ?>>小图</a>
</div>
<div id="album_container" class="imagewall clearfix pb40 <?=$page_col_class?>"><?php $col_index = 0; if(is_array($share_display)) { foreach($share_display as $share_col) { ?><?php $col_index++; if($col_index == $page_col_num) { ?>
<div class="col<?=$col_index?> col mr0 share_col"><div class="i_w_f i_cmt">
<div class="hd"></div>
<div class="bd">
<div class="pub_box_all clearfix">
<form>
<div style="padding:0 10px">
<h2>评论...</h2>
<textarea class="txt PUB_TXT" name="content" position="0"></textarea>
<div class="pub_bottom clearfix">
<a class="fl" onclick="$.Show_Expression(this);" href="javascript:;"><img class="fl add_face" w="album_rpl" onclick="return false;" src="./tpl/images/add_face_c.png"></a>
<input class="txt_btn r3 fr" type="button" value="评论" onclick="$.Add_Share_Comment(this,'#albumCommentList')"/>
<input type="hidden" name="comment_type" value="album" />
<input type="hidden" name="parent_id" value="0" />
<input type="hidden" name="share_id" value="<?=$album_share['share_id']?>" />
</div>
</div>
<ul class="txt_c" id="albumCommentList"><? if(is_array($album_comments)) { foreach($album_comments as $comment) { if($comment['uid']) { ?>
<li>
<div class="pic_n"><?php echo setTplUserFormat($comment['uid'],1,0,'s',24,'','avt icard fl r3 lazyload',''); ?></div>
<p><?php echo setTplUserFormat($comment['uid'],0,0,'',0,'n icard fl','',''); ?>：<?=$comment['content']?>
</p>
<a class="rpl fr" href="javascript:;" uname="<?=$comment['user']['user_name']?>" cid="<?=$comment['comment_id']?>" onclick="$.Reply_Comment(this);">回复</a>
</li>
<? } } } ?>
</ul>
<? if($album_share['comment_count'] > 5) { ?>
<a class="see_all fr" href="<?=$album_share['url']?>" target="_blank">全部<?=$album_share['comment_count']?>条</a>
<? } ?>
</form>
</div>
</div>
<div class="ft"></div>
</div>
<script type="text/javascript">
jQuery(function($){
$("#albumCommentList li").live('mouseover',function(){
$(".rpl",this).show();
}).live('mouseout',function(){
$(".rpl",this).hide();
});
});
</script><? } else { ?>
<div class="col<?=$col_index?> col share_col">
<? } if(is_array($share_col)) { foreach($share_col as $share) { ?><div class="i_w_f" shareID="<?=$share['share_id']?>" id="SHARE_LIST_<?=$share['share_id']?>">
<? if($is_manage_album) { ?>
<a title="删除" href="javascript:;" class="del_pic" style="display: none;" onclick="$.Tweet_Delete(<?=$share['share_id']?>)"></a>
<? } ?>
<div class="hd"></div>
<div class="bd">
<ul class="pic"><? if(is_array($share['imgs'])) { foreach($share['imgs'] as $share_img) { if($show_type == 3) { ?>
<li style="width:446px;padding:0;margin:0 auto 3px;">
<a href="<?=$share_img['url']?>" target="_blank"><img class="lazyload" original="<?php echo getImgName($share_img['img'],468,468,0); ?>" src="./tpl/images/lazyload.gif"></a>
<? } elseif($show_type == 2) { ?>
<li style="width:286px;padding:0;margin:0 auto 3px;">
<a href="<?=$share_img['url']?>" target="_blank"><img class="lazyload" original="<?php echo getImgName($share_img['img'],468,468,0); ?>" src="./tpl/images/lazyload.gif" width="286"></a>
<? } else { ?>
<li style="width:200px;padding:0;margin:0 auto 3px;">
<a href="<?=$share_img['url']?>" target="_blank"><img class="lazyload" original="<?php echo getImgName($share_img['img'],468,468,0); ?>" src="./tpl/images/lazyload.gif" width="200"></a>
<? } if($share_img['type'] == 'g') { ?>
<span class="p">
<span>
<b><?=$share_img['price_format']?></b>
</span>
<i></i>
</span>
<a class="add_to_album_btn" href="javascript:;" style="display: none;" onclick="$.Show_Rel_Album(<?=$share_img['id']?>,'goods');"></a>
<? } else { ?>
<a class="add_to_album_btn" href="javascript:;" style="display: none;" onclick="$.Show_Rel_Album(<?=$share_img['id']?>,'photo');"></a>
<? } ?>
</li>
<? } } ?>
</ul>
<div class="clear"></div>
<div class="favorite">
<a href="javascript:;" class="favaImg" onclick="$.Fav_Share(<?=$share['share_id']?>,this,32,'#SHARE_LIST_<?=$share['share_id']?>');">喜欢</a>
<div class="favDiv"> <a target="_blank" class="favCount SHARE_FAV_COUNT" href="<?=$share['url']?>"><?=$share['collect_count']?></a><i></i></div>
<span class="creply_n"> (<a href="<?=$share['url']?>" target="_blank"><?=$share['comment_count']?></a>)</span>
<a href="<?=$share['url']?>" class="creply">评论</a>
</div>
<div class="who_share">
<div class="tk clearfix">
<? if(!empty($share['content']) && $share['content'] != $share['empty_content']) { ?>
<p><?php echo cutStr($share['content'],96,'...');?></p>
<? } ?>
<span class="album_t"><?=$share['time']?></span>
</div>
</div>
</div>
<? if(!empty($share['comments'])) { ?>
<ul class="rep_list"><? if(is_array($share['comments'])) { foreach($share['comments'] as $comment) { if($comment['uid']) { ?>
<li class="rep_f"><?php echo setTplUserFormat($comment['uid'],1,0,'s',24,'','avt icard fl r3 lazyload',''); ?><p><?php echo setTplUserFormat($comment['uid'],0,0,'',0,'n icard gc','',''); ?>：<?=$comment['content']?></p>
</li>
<? } } } ?>
</ul>
<a class="rep_more" target="_blank"  href="<?=$share['url']?>"> <span class="rm_no"><?=$share['comment_count']?>条评论...</span> <span class="rm_ft"></span> </a>
<? } else { ?>
<div class="ft"></div>
<? } ?>
</div>
<? } } ?>
</div>
<? } } if($col_index > 0 && $col_index < $page_col_num) { ?>
<div class="col mr0 share_col"><div class="i_w_f i_cmt">
<div class="hd"></div>
<div class="bd">
<div class="pub_box_all clearfix">
<form>
<div style="padding:0 10px">
<h2>评论...</h2>
<textarea class="txt PUB_TXT" name="content" position="0"></textarea>
<div class="pub_bottom clearfix">
<a class="fl" onclick="$.Show_Expression(this);" href="javascript:;"><img class="fl add_face" w="album_rpl" onclick="return false;" src="./tpl/images/add_face_c.png"></a>
<input class="txt_btn r3 fr" type="button" value="评论" onclick="$.Add_Share_Comment(this,'#albumCommentList')"/>
<input type="hidden" name="comment_type" value="album" />
<input type="hidden" name="parent_id" value="0" />
<input type="hidden" name="share_id" value="<?=$album_share['share_id']?>" />
</div>
</div>
<ul class="txt_c" id="albumCommentList"><? if(is_array($album_comments)) { foreach($album_comments as $comment) { if($comment['uid']) { ?>
<li>
<div class="pic_n"><?php echo setTplUserFormat($comment['uid'],1,0,'s',24,'','avt icard fl r3 lazyload',''); ?></div>
<p><?php echo setTplUserFormat($comment['uid'],0,0,'',0,'n icard fl','',''); ?>：<?=$comment['content']?>
</p>
<a class="rpl fr" href="javascript:;" uname="<?=$comment['user']['user_name']?>" cid="<?=$comment['comment_id']?>" onclick="$.Reply_Comment(this);">回复</a>
</li>
<? } } } ?>
</ul>
<? if($album_share['comment_count'] > 5) { ?>
<a class="see_all fr" href="<?=$album_share['url']?>" target="_blank">全部<?=$album_share['comment_count']?>条</a>
<? } ?>
</form>
</div>
</div>
<div class="ft"></div>
</div>
<script type="text/javascript">
jQuery(function($){
$("#albumCommentList li").live('mouseover',function(){
$(".rpl",this).show();
}).live('mouseout',function(){
$(".rpl",this).hide();
});
});
</script></div>
<? } ?>
</div>
<? if($pager['page_count'] > 1) { ?>
<div class="blank12"></div>
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
<? } if(!empty($other_album)) { ?>
<div class="image_wall_more clr">
<div id="album_more" class="piece1 other_t">
    		<div class="piece1_hd"></div>
    		<div class="piece1_bd">
        		<div class="albumall_more">
<div class="album_more">
<h1><?=$album_user['user_name']?>的其他杂志社</h1>
<a href="<?php echo FU('u/album',array("uid"=>$album['uid'])); ?>" class="more_btn">查看更多</a>
</div>
            		<ul class="album_more_pic clearfix"><?php $album_index = 0; if(is_array($other_album)) { foreach($other_album as $album) { ?><?php $album_index++; if($album_index == 3) { ?>
<li style="padding-right:0;" class="all_list">
<? } else { ?>
                        <li class="all_list">
<? } ?>
<div class="album_title">
<a href="<?=$album['url']?>"><?php echo cutStr($album['title'],24,'...');?></a>
<span class="pic_n"><span><?=$album['img_count']?>&nbsp;图</span><i></i></span>
</div>
<ul class="all_pic_s"><?php $list_img_counts = array(0,0,0,0,0,0); if(is_array($list_img_counts)) { foreach($list_img_counts as $imgkey => $imgindex) { ?><?php $img = $album['imgs'][$imgkey]; if(!empty($img)) { ?>
<li><a target="_blank" href="<?=$album['url']?>"><img src="<?php echo getImgName($img['img'],100,100,0); ?>" /></a></li>
<? } else { ?>
<li><a target="_blank" class="add_img" href="<?=$album['url']?>"><img src="./tpl/images/none_pic.png" /></a></li>
<? } } } ?>
</ul>
<p><?php echo cutStr($album['content'],98,'...');?></p>
</li>
<? } } ?>
</ul>
</div>
</div>
<div class="piece1_ft"></div>
</div>
</div>
<? } ?>
</div>
<script type="text/javascript">
FANWE.NO_COUNTER = true;
var colHeight = 0;
var colIndex = 0;
var rowHtml = '<div class="i_w_f empty_row"><div class="hd"></div><div class="bd"></div><div class="ft"></div></div>';
jQuery(function(){
$(".i_w_f").hover(function(){
$(".del_pic",this).show();
},function(){
$(".del_pic",this).hide();
});

<? if(getIsManage('album')) { ?>
$('#albumShowBox,.i_w_f').hover(function(){
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