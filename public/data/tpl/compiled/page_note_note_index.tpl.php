<? if(!defined('IN_FANWE')) exit('Access Denied'); 
0
|| checkTplRefresh('./tpl/pink2/page/note/note_index.htm', './tpl/pink2/page/note/note_other.htm', 1325606397, './data/tpl/compiled/page_note_note_index.tpl.php', './tpl/pink2', 'page/note/note_index')
|| checkTplRefresh('./tpl/pink2/page/note/note_index.htm', './tpl/pink2/page/note/note_img.htm', 1325606397, './data/tpl/compiled/page_note_note_index.tpl.php', './tpl/pink2', 'page/note/note_index')
|| checkTplRefresh('./tpl/pink2/page/note/note_index.htm', './tpl/pink2/inc/pages.htm', 1325606397, './data/tpl/compiled/page_note_note_index.tpl.php', './tpl/pink2', 'page/note/note_index')
;?>
<?php 
$css_list[0]['url'][] = './tpl/css/tweetlist.css';
$css_list[0]['url'][] = './tpl/css/sidebar.css';
$css_list[0]['url'][] = './tpl/css/note.css';
$js_list[0] = './tpl/js/note.js';
 include template('inc/header'); ?><div id="body" class="fm960" >
<div class="home_hd"></div>
<div class="home_bd clearfix pb25">
<div id="content" class="fl"  style="width:650px;"><!--dynamic getHomeNav args=<?=$share_detail['uid']?>,<?=$current_type?>--><div class="note_content" id="note_<?=$share_detail['share_id']?>">
<? if($current_type == 'other') { ?><ul class="t_l ">
<li class="t_f" style="">
<div class="hd"><?php echo setTplUserFormat($share_detail['uid'],1,0,'m',48,'','avt icard r5',''); ?></div>
<div class="tk" id="shareInfobox">
<div class="inf"><?php echo setTplUserFormat($share_detail['uid'],0,0,'',0,'icard n gc','',''); ?><a class="t fr"><?=$share_detail['time']?></a>
<? if($share_detail['is_relay']) { if($share_detail['type'] == 'ask') { ?>
<span>关联问题《<a target="_blank" title="<?=$share_detail['title']?>" class="from" href="<?php echo FU('ask/detail',array("tid"=>$share_detail['rec_id'])); ?>"><?php echo cutStr($share_detail['title'],40,'...');?></a>》</span>
<? } elseif($share_detail['type'] == 'bar') { ?>
<span>关联主题《<a target="_blank" title="<?=$share_detail['title']?>" class="from" href="<?php echo FU('club/detail',array("tid"=>$share_detail['rec_id'])); ?>"><?php echo cutStr($share_detail['title'],40,'...');?></a>》</span>
<? } elseif($share_detail['type'] == 'ask_post') { ?>
<span>关联问题《<a target="_blank" title="<?=$share_detail['title']?>" class="from" href="<?php echo FU('ask/detail',array("tid"=>$share_detail['rec_id'])); ?>"><?php echo cutStr($share_detail['title'],40,'...');?></a>》</span>
<? } elseif($share_detail['type'] == 'bar_post') { ?>
<span>关联主题《<a target="_blank" title="<?=$share_detail['title']?>" class="from" href="<?php echo FU('club/detail',array("tid"=>$share_detail['rec_id'])); ?>"><?php echo cutStr($share_detail['title'],40,'...');?></a>》</span>
<? } } if($share_detail['is_rec']) { if($share_detail['type'] == 'ask') { ?>
<span>创建问题《<a target="_blank" title="<?=$share_detail['title']?>" class="from" href="<?php echo FU('ask/detail',array("tid"=>$share_detail['rec_id'])); ?>"><?php echo cutStr($share_detail['title'],40,'...');?></a>》</span>
<? } elseif($share_detail['type'] == 'bar') { ?>
<span>创建主题《<a target="_blank" title="<?=$share_detail['title']?>" class="from" href="<?php echo FU('club/detail',array("tid"=>$share_detail['rec_id'])); ?>"><?php echo cutStr($share_detail['title'],40,'...');?></a>》</span>
<? } elseif($share_detail['type'] == 'ask_post') { ?>
<span>回答问题《<a target="_blank" title="<?=$share_detail['title']?>" class="from" href="<?php echo FU('ask/detail',array("tid"=>$share_detail['rec_id'])); ?>"><?php echo cutStr($share_detail['title'],40,'...');?></a>》</span>
<? } elseif($share_detail['type'] == 'bar_post') { ?>
<span>回应主题《<a target="_blank" title="<?=$share_detail['title']?>" class="from" href="<?php echo FU('club/detail',array("tid"=>$share_detail['rec_id'])); ?>"><?php echo cutStr($share_detail['title'],40,'...');?></a>》</span>
<? } elseif($share_detail['type'] == 'album_best') { ?>
<span>推荐杂志社<a target="_blank" title="<?=$share_detail['title']?>" href="<?php echo FU('album/show',array("id"=>$share_detail['rec_id'])); ?>">《<?php echo cutStr($share_detail['title'],40,'...');?>》</a></span>
<? } elseif($share_detail['type'] == 'album_item') { ?>
<span>加入杂志社<a target="_blank" title="<?=$share_detail['title']?>" href="<?php echo FU('album/show',array("id"=>$share_detail['rec_id'])); ?>">《<?php echo cutStr($share_detail['title'],40,'...');?>》</a>
<? if($share_detail['rec_uid'] > 0) { ?>
收入自 @<?php echo setTplUserFormat($share_detail['rec_uid'],0,0,'',0,'','',''); ?></span>
<? } else { ?>
</span>
<? } } } if($share_detail['type'] == 'album') { ?>
<span>创建杂志社<a target="_blank" title="<?=$share_detail['title']?>" href="<?php echo FU('album/show',array("id"=>$share_detail['rec_id'])); ?>">《<?php echo cutStr($share_detail['title'],40,'...');?>》</a></span>
<? } ?>
</div>
<p class="sms"><?=$share_detail['content']?></p>
<? if($share_detail['is_relay'] || $share_detail['is_rec']) { ?>
<div class="q r5">
<img src="./tpl/images/quote_arrow.png" class="q_a">
<p class="sms"><?php echo setTplUserFormat($parent_share['uid'],0,0,'',0,'icard n','',''); ?>：<?php echo cutStr($parent_share['content'],200,'...');?> 
<? if($parent_share['type'] == 'ask') { ?>
<a title="详情查看" href="<?php echo FU('ask/detail',array("tid"=>$parent_share['rec_id'])); ?>" target="_blank">详情查看</a> 
<? } elseif($parent_share['type'] == 'bar') { ?>
<a title="详情查看" href="<?php echo FU('club/detail',array("tid"=>$parent_share['rec_id'])); ?>" target="_blank">详情查看</a> 
<? } ?>
<a href="<?=$parent_share['url']?>" target="_blank">原文转发(<?=$parent_share['relay_count']?>)</a> 
<a href="<?=$parent_share['url']?>" target="_blank">原文评论(<?=$parent_share['comment_count']?>)</a>
</p>
<? if(!empty($parent_share['imgs'])) { ?>
<div class="pic" style="display: block;"><? if(is_array($parent_share['imgs'])) { foreach($parent_share['imgs'] as $parent_share_img) { ?><div class="r3">
<img<? if($parent_share_img['type'] == 'g') { ?> alt="<?=$parent_share_img['name']?>"<? } ?> class="fl" d-src="<?php echo getImgName($parent_share_img['img'],100,100,0); ?>" src="<?php echo getImgName($parent_share_img['img'],100,100,0); ?>" />
<? if($parent_share_img['type'] == 'g') { ?>
<img class="tag" src="./tpl/images/goods_tag.png" />
<? } ?>
</div>
<? } } ?>
<br class="clear">                       
</div>
<ul class="pic_b"><? if(is_array($parent_share['imgs'])) { foreach($parent_share['imgs'] as $parent_share_img) { if($parent_share_img['type'] == 'g') { ?>
<li class="pic_b_f r5" style="display: none;"> 
<div class="pic_b_hd" style="height:19px;">
<a class="mg_slink ofh" ref="nofollow" target="_blank" href="<?=$parent_share_img['to_url']?>"><?=$parent_share_img['name']?></a>
<a class="buy_it mg_slink" target="_blank" href="<?=$parent_share_img['to_url']?>"><span class="g_p"><span><?=$parent_share_img['price_format']?></span></span><i></i></a>	
</div>                
<div class="pic_b_bd">
<a class="add_to_album_btn" href="javascript:;" style="display: none;" onclick="$.Show_Rel_Album(<?=$parent_share_img['id']?>,'goods');"></a>
<img alt="<?=$parent_share_img['name']?>" src="<?php echo getImgName($parent_share_img['img'],468,468,0); ?>">
</div>
<div class="show_big">
<img class="big_book" style="right:37px" src="./tpl/images/book_13x13.png">
<a class="big_detail" ref="nofollow" style="right:10px" target="_blank" href="<?=$parent_share_img['url']?>">详情</a>
</div>
</li>
<? } else { ?>
<li class="pic_b_f r5" style="display: none;">
<div class="pic_b_bd">
<a class="add_to_album_btn" href="javascript:;" style="display: none;" onclick="$.Show_Rel_Album(<?=$parent_share_img['id']?>,'photo');"></a>
<img src="<?php echo getImgName($parent_share_img['img'],468,468,0); ?>">
</div>	
<div class="show_big">
<img class="big_book" src="./tpl/images/book_13x13.png">
<a class="big_detail" ref="nofollow" target="_blank" href="<?=$parent_share_img['url']?>">详情</a>
<img class="big_cur" src="./tpl/images/big_13x13.png">
<a class="bigimg" ref="nofollow" target="_blank" href="<?=$parent_share_img['img']?>">查看原图</a>
</div>
</li>
<? } } } ?>
</ul>
<? } ?>
</div>
<? } ?>
<div class="tl">
<a class="add_fav fav" w="f" href="javascript:;"  onclick="$.FavShare(<?=$share_detail['share_id']?>,this,32,'#note_<?=$share_detail['share_id']?>');"></a>
<div class="favDiv">
<a class="SHARE_FAV_COUNT favCount" href="<?=$share_detail['url']?>" target="_blank"><?=$share_detail['collect_count']?></a>
<i></i>
</div>
<? if($is_eidt_share) { ?>
<a w="f" href="javascript:;" class="mg">管理</a>
<? } ?>
<a w="f" href="javascript:;" onclick="$.RelayShare(<?=$share_detail['share_id']?>);" class="fw">转发(<?=$share_detail['relay_count']?>)</a>
</div>
<ul class="SHARE_FAV_LIST u_like"><? if(is_array($share_detail['collects'])) { foreach($share_detail['collects'] as $collect_uid) { ?><li><?php echo setTplUserFormat($collect_uid,1,0,'m',0,'','icard r3',''); ?></li>
<? } } ?>
</ul>
<? if($is_eidt_share) { ?>
<ul class="t_m_l_h">
<li><a class="del" onclick="MOGU.Note_Delete('16wfq8','note')" href="javascript:void(0);">删除</a></li>
</ul>
<? } ?>
</div>
</li>
</ul>
<script type="text/javascript">
jQuery(function($){
$('.t_l .pic div').click(function(){
var parent = $(this).parent();
var index = $('div',parent).index(this);
var picb = parent.next();
parent.hide();
$('li',picb).eq(index).show();
return false;
});

$('.t_l .pic_b .pic_b_bd').click(function(){
var parent = $(this).parent();
parent.hide();
parent.parent().prev().show();
});
});
</script><? } else { ?><div class="shw">
<div class="shw_head"> </div>
<div class="shw_body">
<div class="image" >
<!--<a class="flow_left" href="javascript:;"></a>
<a class="flow_right" href="javascript:;"></a>-->
<? if($current_type == 'bao') { ?>
<a class="add_to_album_btn" href="javascript:;" style="display: none;" onclick="$.Show_Rel_Album(<?=$current_obj['id']?>,'goods');"></a>
<? } else { ?>
<a class="add_to_album_btn" href="javascript:;" style="display: none;" onclick="$.Show_Rel_Album(<?=$current_obj['id']?>,'photo');"></a>
<? } ?>
<a href="<? if($current_type == 'bao') { ?><?=$current_obj['to_url']?><? } else { ?><?=$current_obj['img']?><? } ?>" target="_blank" class="show_big"><img src="<?php echo getImgName($current_obj['img'],468,468,0); ?>" /></a>&nbsp;
</div>
<? if($current_type == 'bao') { ?>
<div class="shop_info">
<a href="<?=$current_obj['to_url']?>" target="_blank">
<span class="nii_p"><?php echo cutStr($current_obj['name'],32,'...');?></span>
</a>
<span class="price"><?=$current_obj['price_format']?></span>
<a href="<?=$current_obj['to_url']?>" target="_blank" class="buy_url"></a>
</div>
<? } else { ?>
<div class="big_img"><a href="<?=$current_obj['img']?>" target="_blank" class="alibm">查看原图</a></div>
<? } if(!empty($share_detail['imgs']) && count($share_detail['imgs']) > 1) { ?>
<div class="small_image">
<ul class="nt_pic_list"><? if(is_array($share_detail['imgs'])) { foreach($share_detail['imgs'] as $share_detail_img) { if($share_detail_img['type'] == 'g') { ?>
<li keys="<?=$share_detail_img['id']?>" tp="good" class="<? if($current_type == 'bao' && $share_detail_img['id'] == $current_obj['id']) { ?>c<? } ?>">
<div><a href="<?=$share_detail_img['url']?>"><img src="<?php echo getImgName($share_detail_img['img'],100,100,0); ?>"></a></div>
<span><?=$share_detail_img['price_format']?></span>
</li>
<? } else { ?>
<li tp="image" keys="<?=$share_detail_img['id']?>" class="<? if($current_type == 'photo' && $share_detail_img['id'] == $current_obj['id']) { ?>c<? } ?>">
<div><a href="<?=$share_detail_img['url']?>"><img src="<?php echo getImgName($share_detail_img['img'],100,100,0); ?>"></a></div>
<span>&nbsp;</span>
</li>
<? } } } ?>
</ul>
</div>
<? } ?>
<input type="hidden" value="<?=$pns['prev']?>" id="user_share_prev" />
<input type="hidden" value="<?=$pns['next']?>" id="user_share_next" />
</div>
<div class="shw_foot"> </div>
</div>
<div class="blank9"></div>
<div id="SHARE_TAGS_<?=$share_detail['share_id']?>" class="fashion">
<? if($is_eidt_tag) { ?>
<div class="ed_fashion SHARE_TAG_EDIT_BOX">
<div class="fa_title">
<div class="fsl"><span>时尚元素</span>&nbsp;&nbsp;<?php echo sprintf('最多可以设置%d个标签,标签之间用空格隔开',$_FANWE['setting']['share_tag_count']); ?></div>
<div class="fsr"><a onclick="$.ShareTagClose('<?=$share_detail['share_id']?>',this)" href="javascript:;">关闭</a></div>
</div>
<div class="fa_inp">
            <?php 
                $tags_val = array();
                foreach($share_tags as $share_tag)
                {
                    $tags_val[] = $share_tag['tag_name'];
                }
                $tags_val = implode(' ',$tags_val);
             ?>
<input type="text" value="<?=$tags_val?>" class="SHARE_TAG text">
<a onclick="$.ShareTagSave('<?=$share_detail['share_id']?>',this)" href="javascript:;" style="text-align:center; line-height:30px; font-size:14px; color:#fff;">提交</a>
</div>
<div class="clear"></div>
</div>
<? } ?>
<div class="sw_fashion SHARE_TAG_SHOW_LIST">
<span>时尚元素：</span>
<? if(empty($share_tags)) { if($is_eidt_tag) { ?>
        <span class="SHARE_TAG_LIST"></span>
<span class="edit"><a onclick="$.ShareTagEdit('<?=$share_detail['share_id']?>',this)" href="javascript:;">添加</a></span>
<? } } else { ?>
        <span class="SHARE_TAG_LIST"><? if(is_array($share_tags)) { foreach($share_tags as $share_tag) { ?><a href="<?=$share_tag['url']?>" target="_blank"><?=$share_tag['tag_name']?></a>
<? } } ?>
        </span>
<? if($is_eidt_tag) { ?>
<span class="edit"><a onclick="$.ShareTagEdit('<?=$share_detail['share_id']?>',this)" href="javascript:;">编辑</a></span>
<? } } ?>
</div>
</div>
<div class="note_who_like">
<div style="overflow:hidden;zoom:1;padding-bottom:10px;">
<a href="javascript:;" class="fl" onclick="$.Fav_Share(<?=$share_detail['share_id']?>,this,32,'#note_<?=$share_detail['share_id']?>');"><img class="fl add_fav_new" src="./tpl/images/like.png" /></a>
<span class="nwl_cfav"><span class="SHARE_FAV_COUNT"><?=$share_detail['collect_count']?></span><i></i></span>
<a href="javascript:;" onclick="$.Relay_Share(<?=$share_detail['share_id']?>);" class="nwl_forward">转发(<?=$share_detail['relay_count']?>)</a>
</div>
<div class="SHARE_FAV_BOX nwl_img<? if(count($share_detail['collects']) == 0) { ?> hidden<? } ?>">
<span>她们喜欢这个分享</span>
<ul class="SHARE_FAV_LIST u_like"><? if(is_array($share_detail['collects'])) { foreach($share_detail['collects'] as $collect_uid) { ?><li><?php echo setTplUserFormat($collect_uid,1,0,'m',0,'','icard r3',''); ?></li>
<? } } ?>
</ul>
</div>
</div>
<ul class="t_l pt40">
<li style="border-bottom:0; border-top:1px solid #ebede3;"  class="t_f" id="shareInfobox">
<div class="hd"><?php echo setTplUserFormat($share_user['uid'],1,0,'m',48,'','avt icard r5',''); ?></div>
<div class="tk">
<div class="inf"><?php echo setTplUserFormat($share_user['uid'],0,0,'',0,'icard n gc','',''); ?><a class="t fr" target="_blank"><?=$share_detail['time']?></a>
<span>
<? if($share_detail['type'] == 'ask') { ?>
创建《<a target="_blank" title="<?=$share_detail['title']?>" class="from" href="<?php echo FU('ask/detail',array("tid"=>$share_detail['rec_id'])); ?>"><?php echo cutStr($share_detail['title'],40,'...');?></a>》
<? } elseif($share_detail['type'] == 'bar') { ?>
创建《<a target="_blank" title="<?=$share_detail['title']?>" class="from" href="<?php echo FU('club/detail',array("tid"=>$share_detail['rec_id'])); ?>"><?php echo cutStr($share_detail['title'],40,'...');?></a>》
<? } elseif($share_detail['type'] == 'ask_post') { ?>
回应《<a target="_blank" title="<?=$share_detail['title']?>" class="from" href="<?php echo FU('ask/detail',array("tid"=>$share_detail['rec_id'])); ?>"><?php echo cutStr($share_detail['title'],40,'...');?></a>》
<? } elseif($share_detail['type'] == 'bar_post') { ?>
回应《<a target="_blank" title="<?=$share_detail['title']?>" class="from" href="<?php echo FU('club/detail',array("tid"=>$share_detail['rec_id'])); ?>"><?php echo cutStr($share_detail['title'],40,'...');?></a>》
<? } elseif($share_detail['type'] == 'album_best') { ?>
推荐杂志社<a target="_blank" title="<?=$share_detail['title']?>" href="<?php echo FU('album/show',array("id"=>$share_detail['rec_id'])); ?>">《<?php echo cutStr($share_detail['title'],40,'...');?>》</a>
<? } elseif($share_detail['type'] == 'album_item') { ?>
加入杂志社<a target="_blank" title="<?=$share_detail['title']?>" href="<?php echo FU('album/show',array("id"=>$share_detail['rec_id'])); ?>">《<?php echo cutStr($share_detail['title'],40,'...');?>》</a>
<? if($share_detail['rec_uid'] > 0) { ?>
收入自 @<?php echo setTplUserFormat($share_detail['rec_uid'],0,0,'',0,'','',''); } } ?>
</span>
</div>
<p class="sms"><?=$share_detail['content']?></p>
</div>
</li>
</ul><? } ?>
<div class="note_comment">
<form method="post">
<div class="pub_box" id="publish_note">
<div class="pub_edit r5">
<div><span class="fw_count"><?php echo sprintf('还可以输入<em class="word_count WORD_COUNT">%s</em>个汉字',"140"); ?></span></div>
<textarea name="content" class="PUB_TXT pub_txt fl rl5" length="140" position="0"></textarea>
<input type="button" value="评论" class="pub_btn fl rr5" onclick="$.Add_Share_Comment(this,'#SHARE_COMMENT_LIST_<?=$share_detail['share_id']?>')">
<div class="pub_ext">
<div class="pub_opt fl">
<span class="fl">添加：</span>
<a w="note" onclick="$.Show_Expression(this)" href="javascript:;" style="background-position: 0pt -3px;" class="add_face">表情</a>
</div>
<div class="pub_out fr">
<input id="comment_relay_tweet" type="checkbox" name="is_relay" checked="checked" value="1" />
<label for="comment_relay_tweet">转发给我的粉丝</label>
<input type="hidden" value="<?=$share_detail['share_id']?>" name="share_id" />
<input type="hidden" name="parent_id" value="0" />
</div>
</div>
</div>
</div>
<div id="SHARE_COMMENT_LIST_BOX">
<ul id="SHARE_COMMENT_LIST_<?=$share_detail['share_id']?>" class="c_l rb5"><? if(is_array($share_detail['comments'])) { foreach($share_detail['comments'] as $comment) { ?><li class="c_f" id="COMMENT_<?=$comment['comment_id']?>"><?php echo setTplUserFormat($comment['uid'],1,0,'s',0,'','avt',''); ?><p class="sms"><?php echo setTplUserFormat($comment['uid'],0,1,'',0,'n icard','',''); ?>：回复<?=$comment['content']?><span>（<?=$comment['time']?>）</span></p>
<div>
<? if($is_remove_comment) { ?>
<a onclick="$.Delete_Comment(<?=$comment['comment_id']?>,this);" class="del" w="f" href="javascript:void(0);">删除</a>
<? } ?>
<a class="rpl" w="f" href="javascript:;" uname='<?=$comment['user']['user_name']?>' cid="<?=$comment['comment_id']?>" onclick="$.Reply_Comment(this);">回复</a>
</div>
</li>
<? } } ?>
</ul>
<div class="pagination"><? if($pager['page_count'] > 1) { ?>
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
</div>
</form>
</div>
<? if(count($fav_user_fav_share) > 0) { ?>
<div class="note_all_fav_bao">
<div class="title"> 喜欢这个分享的人还喜欢... </div>
<ul class="key_all_fav_bao clear_in"><? if(is_array($fav_user_fav_share)) { foreach($fav_user_fav_share as $share) { ?><li keys="<?=$share['share_id']?>">
<div><a href="<?=$share['url']?>"><img src="<?php echo getImgName($share['imgs'][0]['img'],100,100,0); ?>" /></a></div>
<span><?php echo setTplUserFormat($share['uid'],0,0,'',0,'icard favn','',''); ?></span>
</li>
<? } } ?>
</ul>
</div>
<? } if(count($user_collect_share) > 0) { ?>
<div class="note_fav_bao">
<div class="title"><?=$user_show_name['name']?>喜欢的分享... </div>
<ul class="key_fav_bao clear_in"><? if(is_array($user_collect_share)) { foreach($user_collect_share as $share) { ?><li keys="<?=$share['share_id']?>">
<div><a href="<?=$share['url']?>"><img src="<?php echo getImgName($share['imgs'][0]['img'],100,100,0); ?>" /></a></div>
<span><?php echo setTplUserFormat($share['uid'],0,0,'',0,'icard favn','',''); ?></span>
</li>
<? } } ?>
</ul>
</div>
<? } ?>
</div>
</div>
<div id="sidebar" class="fr"  style="width:310px;">
<div class="user_info psb" style="">
<div class="clearfix">
<a href="<?php echo FU('u/index',array("uid"=>$share_user['uid'])); ?>"><img class="fl info_avatar"  src="<?php echo avatar($share_user['uid'],'m',$share_user['server_code'],1);?>"></a>
<div class="clearfix">
<a class="n<? if($share_user['gender'] == 1) { ?> bc<? } else { ?> gc<? } ?>" href="<?php echo FU('u/index',array("uid"=>$share_user['uid'])); ?>"><?=$share_user['user_name']?></a> <span class="send"> <a href="javascript:;" onclick="$.AtMe_Share(this);" class="home_at_ta bc" toname="<?=$share_user['user_name']?>" >@<?=$user_show_name['short']?></a> </span>
</div><!--getfollow <?=$share_user['uid']?> inc/getfollow/note_index--></div>
<p class="introduce mt10">
<? if(empty($share_user['introduce'])) { if($share_user['uid'] != $_FANWE['uid']) { ?>
<a style="color:#aaa">真懒啊，连自我介绍都不写。</a> 
<? } else { ?>
<a class="s_d" href="<?php echo FU('settings/personal',array()); ?>" target="_blank">添加自我介绍</a>
<? } } else { ?>
<?=$share_user['introduce']?>
<? if($share_user['uid'] == $_FANWE['uid']) { ?>
( <a class="s_d" href="<?php echo FU('settings/personal',array()); ?>" target="_blank">修改</a> )
<? } } ?>
</p>
</div>
<div class="medals psb bt1 bb1">
<h2><a><?=$user_show_name['short']?>的勋章...</a></h2>
<ul><? if(is_array($user_medals)) { foreach($user_medals as $medal) { ?><li class="medal_f"><a target="_blank" href="<?php echo FU('medal/u',array("uid"=>$share_detail['uid'])); ?>"><img title="<?=$medal['name']?>" alt="<?=$medal['name']?>" src="<?=$medal['big_img']?>" height="39"></a></li>
<? } } ?>
</ul>
</div>
<div class="ff_inf psb bb1">
<ul style="margin:0px;">
<li style="padding-left:0">
<a href="<?php echo FU('u/follow',array("uid"=>$share_user['uid'])); ?>">关注</a><br>
<a href="<?php echo FU('u/follow',array("uid"=>$share_user['uid'])); ?>"><span><?=$share_user['follows']?></span></a>
</li>
<li>
<a href="<?php echo FU('u/fans',array("uid"=>$share_user['uid'])); ?>">粉丝</a><br/>
<a href="<?php echo FU('u/fans',array("uid"=>$share_user['uid'])); ?>"><span><?=$share_user['fans']?></span></a>
</li>
<li style="border-right:none;">
<a>被喜欢</a><img style="margin-left:5px;" src="./tpl/images/heart_12x11.png"><br/>
<a ><span style="color:#ff076a; text-decoration:none;"><?=$share_user['collects']?></span></a>
</li>
</ul>
</div>
<? if($current_type == 'bao') { ?>
<div class="hot_images psb bt1 bb1">
<h2><a><?=$user_show_name['short']?>最被喜欢的宝贝</a></h2>
<ul class="fl"> <? if(is_array($best_goods_share)) { foreach($best_goods_share as $goods_share) { ?><li><a target="_blank" href="<?=$goods_share['url']?>"><img src="<?php echo getImgName($goods_share['img'],100,100,0); ?>"></a></li>
<? } } ?>
</ul> 
</div>
<?=$shop_percent_html?>
<? } elseif($current_type == 'photo') { ?>
<div class="hot_images psb bt1 bb1">
<h2><a><?=$user_show_name['short']?>最被喜欢的照片 </a></h2>
<ul class="fl"><? if(is_array($best_photo_share)) { foreach($best_photo_share as $photo_share) { ?><li><a target="_blank" href="<?=$photo_share['url']?>"><img src="<?php echo getImgName($photo_share['img'],100,100,0); ?>"></a></li>
<? } } ?>
</ul>
</div>
<div class="hot_images psb bt1 bb1">
<h2><a><?=$user_show_name['short']?>喜欢的照片 </a></h2>
<ul class="fl"><? if(is_array($user_fav_photo)) { foreach($user_fav_photo as $photo_share) { ?><li><a target="_blank" href="<?=$photo_share['url']?>"><img src="<?php echo getImgName($photo_share['img'],100,100,0); ?>"></a></li>
<? } } ?>
</ul>
</div>
<? } else { } ?>
</div>
</div>
<div class="home_ft"></div>
</div>
<script type="text/javascript">
function UpdateUserFollow(obj,result)
{
if(result.status == 1)
{
$(obj).before('<img class="fo_ok" src="./tpl/images/add_ok_03.png">');
$(obj).remove();
}
}

jQuery(function(){
CommentInit();

<? if(getIsManage('share')) { ?>
$('#shareInfobox').hover(function(e){
$.GetManageMenu('share',<?=$share_detail['share_id']?>,this,e);
},function(){});
<? } ?>

});

function CommentInit()
{
$(".pagination a").click(function(){
var page = $(this).attr('page');
$.Get_Share_Comment("<?=$share_detail['share_id']?>",page,'#SHARE_COMMENT_LIST_BOX',CommentInit);
return false;
});
}
</script><? include template('inc/footer'); ?>