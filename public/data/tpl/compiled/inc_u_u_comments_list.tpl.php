<? if(!defined('IN_FANWE')) exit('Access Denied'); 
0
|| checkTplRefresh('./tpl/pink2/inc/u/u_comments_list.htm', './tpl/pink2/inc/pages.htm', 1331262365, './data/tpl/compiled/inc_u_u_comments_list.tpl.php', './tpl/pink2', 'inc/u/u_comments_list')
;?>
<? if(empty($comment_list)) { ?>
<div class="empty">
<img alt="" class="fl" src="./tpl/images/fanwe4.png">
<span>呼~~这里还是空的~~</span>
<br>如果你收到网友的评论，就会显示在这里。 
</div>
<? } else { ?>
<ul class="cmt_me" id="wb_comment_me_list"><? if(is_array($comment_list)) { foreach($comment_list as $comment) { ?><li class="c_f" id="COMMENT_<?=$comment['comment_id']?>">
<div class="avt"><?php echo setTplUserFormat($comment['uid'],1,0,'m',48,'','icard r5  b',''); ?><!--getfollow <?=$comment['uid']?> inc/getfollow/u_comments_list--></div>
<div class="ct">
<p class="msg">
<span><?php echo setTplUserFormat($comment['uid'],0,1,'',0,'u_name u_g','',''); ?>：<?=$comment['content']?></span><br>
<a href="<?=$comment['url']?>" class="r_a">回复我的微博</a>：<?php echo cutStr($comment['scontent'],160,'...');?></p>
<div>
<a class="fl" href="<?=$comment['url']?>"><?=$comment['time']?></a> 
<a onclick="$.Delete_Comment(<?=$comment['comment_id']?>,this);" class="fr" href="javascript:void(0);">删除</a>
</div>
</div>
<div class="clear"></div>
</li>
<? } } ?>
</ul>
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
<script type="text/javascript">
function ShareListUpdateUserFollow(obj,result)
{
if(result.status == 1)
{
$(obj).before('<img src="./tpl/images/add_fo_ok.png" class="fo_ok">');
$(obj).remove();
}
}
</script>
<? } ?>