<? if(!defined('IN_FANWE')) exit('Access Denied'); 
0
|| checkTplRefresh('./tpl/pink2/page/ask/ask_list.htm', './tpl/pink2/inc/pages.htm', 1331261795, './data/tpl/compiled/page_ask_ask_list.tpl.php', './tpl/pink2', 'page/ask/ask_list')
;?>
<?php 
$css_list[0]['url'] = './tpl/css/ask.css';
 include template('inc/header'); ?><div id="body" class="fm960">
<div id="content" class="fl">
<div class="piece2">
<div class="piece2_hd"></div>
<div class="piece2_bd"> 
<!-- 主题吧详细 -->
<div id="bar_head">
<div class="bar_info">
<img src="./tpl/images/answer.png" alt="" class="fl" />
<div style="padding:15px 0 0 0;"><span>问答吧</span></div>
</div>
<div class="bar_intro">
<div class="bar_rule"> 本吧规则：
<ol>
<li>1. 你可以问任何与购物相关的问题，如在哪买、买什么、好不好等等；</li>
<li>2. 与购物不相关的问题会被删除（或移动）；</li>
<li>3. 提问者请及时选择最佳答案，不然会伤害热心网友们的心。</li>
</ol>
</div>
</div>
<a href="<?php echo FU('ask/newtopic',array("aid"=>$current_aid)); ?>" class="new_button"><img src="./tpl/images/new_ask.png"></a></div>
</div>
<div class="piece2_ft"></div>
</div>
<!-- 主题列表 -->
<div class="piece2 mt20">
<div class="piece2_hd"></div>
<div id="topic_list" class="piece2_bd plr10">
<div id="ask_cate" >
<a class="r3<? if($current_aid == 0) { ?> current<? } ?>" href="<?php echo FU('ask/forum',array()); ?>">全部提问</a><? if(is_array($asks)) { foreach($asks as $ask) { ?><span>|</span><a class="r3<? if($current_aid == $ask['aid']) { ?> current<? } ?>" href="<?=$ask['url']?>"><?=$ask['name']?></a>
<? } } ?>
</div>
<div id="topic_tab" >
<ul>
<li class="<?=$all_type?>"><a href="<?php echo FU('ask/forum',array("aid"=>$current_aid,"type"=>"all")); ?>">所有问题</a></li>
<li class="<?=$best_type?>"><a href="<?php echo FU('ask/forum',array("aid"=>$current_aid,"type"=>"best")); ?>"><img src="./tpl/images/topic_s_3.png" />小编推荐</a></li>
<!--<li class="<?=$over_type?>"><a href="<?php echo FU('ask/forum',array("aid"=>$current_aid,"type"=>"over")); ?>">已解决</a></li>
<li class="<?=$wait_type?>"><a href="<?php echo FU('ask/forum',array("aid"=>$current_aid,"type"=>"wait")); ?>">未解决</a></li>-->
<li class="<?=$none_type?>"><a href="<?php echo FU('ask/forum',array("aid"=>$current_aid,"type"=>"none")); ?>">零回答</a></li>
</ul>
</div>
<div class="topic_list_title">
<div style="width:395px">主题</div>
<div style="width:100px">主持人/创建时间</div>
<div style="width:60px">回应/关注</div>
<div>最后回应</div>
</div>
<ul class="topic_list"><? if(is_array($ask_threads)) { foreach($ask_threads as $thread) { ?><li askID="<?=$thread['tid']?>">
<div class="topic_col0">
<? if($thread['is_top'] == 1) { ?>
<img src="./tpl/images/topic_s_4.png" alt="" />
<? } elseif($thread['is_best'] == 1) { ?>
<img src="./tpl/images/topic_s_3.png" alt="" />
<? } elseif($thread['is_solve'] == 0) { ?>
<img src="./tpl/images/topic_s_5.png" alt="" />
<? } elseif($thread['is_solve'] == 1) { ?>
<img src="./tpl/images/topic_s_6.png" alt="" />
<? } ?>
</div>
<div class="topic_col1"><?php echo setTplUserFormat($thread['uid'],1,0,'s',24,'','avt icard r5 lazyload',''); ?><a class="" href="<?php echo FU('ask/detail',array("tid"=>$thread['tid'])); ?>" target="_blank"> <?php echo cutStr($thread['title'],40,'...');?></a>
</div>
<div class="topic_col2"><?php echo setTplUserFormat($thread['uid'],0,0,'',0,'fl u_name','',''); ?><br />
<?=$thread['time']?>
</div>
<div class="topic_col3">
<span><?=$thread['post_count']?></span><br />
<?=$thread['follow_count']?>
</div>
<div class="topic_col4">
<? if($thread['lastpost'] > 0) { ?><?php echo setTplUserFormat($thread['lastposter'],0,0,'',0,'u_name','',''); ?><br />
<?=$thread['last_time']?>
<? } ?>
</div>
</li>
<? } } ?>
</ul>
<!-- 分页  -->
<div class="pagination" style="padding:40px 0 0 0;"> <? if($pager['page_count'] > 1) { ?>
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
<div class="piece2_ft"></div>
</div>
</div>
<div id="sidebar" class="fr">
<div class="piece3">
<div class="piece3_hd"></div>
<div class="piece3_bd bar_show">
<h2 class="tsb_title">相关的主题吧...</h2>
<ul><? if(is_array($forum_list)) { foreach($forum_list as $forum) { ?><li><a href="<?=$forum['url']?>"><?=$forum['name']?></a></li>
<? } } ?>
</ul>
</div>
<div class="piece3_ft"></div>
</div>
<div class="piece3 mt20">
<div class="piece3_hd"></div>
<div class="piece3_bd p_l" style="padding:0 10px;">
<h2 class="tsb_title">本吧活跃分子...</h2>
<ul><? if(is_array($hotusers)) { foreach($hotusers as $hotuser) { ?><li>
<div><a href="<?php echo FU('u/index',array("uid"=>$hotuser['uid'])); ?>"><img class="u_name GUID" uid="<?=$hotuser['uid']?>" src="<?php echo avatar($hotuser['uid'],'m',$hotuser['server_code'],1);?>" width="64" height="64" alt="<?=$hotuser['user_name']?>" /></a></div>
<a href="<?php echo FU('u/index',array("uid"=>$hotuser['uid'])); ?>" class="u_name GUID" uid="<?=$hotuser['uid']?>"><?=$hotuser['user_name']?></a></li>
<? } } ?>
</ul>
</div>
<div class="piece3_ft"></div>
</div>
<div class="piece3 mt20">
<div class="piece3_hd"></div>
<div id="topic_hot_sidebar" class="piece3_bd topic_list_sidebar">
<h2 class="tsb_title">本吧热门主题...</h2>
<ul><? if(is_array($hottopics)) { foreach($hottopics as $topic) { ?><li><a target="_blank" href="<?php echo FU('ask/detail',array("tid"=>$topic['tid'])); ?>"><?php echo cutStr($topic['title'],36,'...');?></a></li>
<? } } ?>
</ul>
</div>
<div class="piece3_ft"></div>
</div>
</div>
<div class="clear"></div>
</div>

<? if(getIsManage('ask')) { ?>
<script type="text/javascript">
jQuery(function($){
$('.topic_list li').hover(function(){
var askID = this.getAttribute('askID');
if(askID)
$.GetManageMenu('ask',askID,this);
},function(){});
});
</script>
<? } include template('inc/footer'); ?>