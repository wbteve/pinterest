<? if(!defined('IN_FANWE')) exit('Access Denied'); 
0
|| checkTplRefresh('./tpl/pink2/page/club/club_forum.htm', './tpl/pink2/inc/pages.htm', 1331262349, './data/tpl/compiled/page_club_club_forum.tpl.php', './tpl/pink2', 'page/club/club_forum')
;?>
<?php 
$css_list[0]['url'] = './tpl/css/club.css';
$js_list[0][] = './public/js/jquery.slides.js';
$js_list[0][] = './tpl/js/club.js';
 include template('inc/header'); ?><div id="body" class="fm960">
<div class="bar_title">
<div class="piece_bd" style="background:url('<?=$root_forum['img']?>') no-repeat ;">
<div class="b_info r5">
<img src="<?=$root_forum['logo']?>" alt="" class="fl">
<div class="b_n">
<h1 class="bar_title"><?=$root_forum['name']?></h1>
创建于：<?php echo fToDate($root_forum['create_time'],'Y-m-d'); ?></div>
</div>
<ul class="b_tab">
<li><a href="<?php echo FU('club/forum',array("fid"=>$root_forum['fid'])); ?>" class="f_b<? if($is_root) { ?> c<? } ?>">所有主题</a></li>
<li>
<a href="<?php echo FU('club/best',array("fid"=>$forum['fid'])); ?>" class="f_b<? if($is_best) { ?> c<? } ?>">小编推荐</a>
<div class="bar_tj"></div>
</li><? if(is_array($root_forum['childs'])) { foreach($root_forum['childs'] as $childid) { ?><?php $child_forum = $_FANWE['cache']['forums']['all'][$childid]; ?><li><a href="<?php echo FU('club/forum',array("fid"=>$childid)); ?>" class="f_b<? if($childid == $forum_id) { ?> c<? } ?>"><?=$child_forum['name']?></a></li>
<? } } ?>
</ul>
</div>
</div>
<div style="background-color:#fff;" class="clearfix pb40">
<div id="content" class="fl">
<div class="bar_c r10" >
<? if(!empty($root_forum['desc'])) { ?>
<pre class="bar_intro r5"><?=$root_forum['desc']?></pre>
<? } ?>
<a href="<?php echo FU('club/newtopic',array("fid"=>$forum_id)); ?>" class="pub_topic"><img src="./tpl/images/pub_newtopic.png" alt="" /></a> </div>
<div class="s_tp">
<a<? if($sort == 'post') { ?> class="c"<? } else { ?> href="<?php echo FU('club/forum',array("fid"=>$forum_id,"sort"=>"post")); ?>"<? } ?>>最后回应</a> / <a<? if($sort != 'post') { ?> class="c"<? } else { ?> href="<?php echo FU('club/forum',array("fid"=>$forum_id,"sort"=>"tid")); ?>"<? } ?>>创建时间 </a>
</div>
<ul class="topic_list"><? if(is_array($topic_list)) { foreach($topic_list as $topic) { ?><li class="tl_f" topicID="<?=$topic['tid']?>">
<div class="tl_c">
<? if($topic['is_best'] == 1) { ?>
<div class="img_tj"></div>
<? } if(isset($topic['imgs'])) { ?>
<a target="_blank" href="<?=$topic['url']?>"><img height="32" src="<?php echo getImgName($topic['imgs'][0]['img'],100,100,0); ?>" class="pic"></a>
<div class="show_big_img"><? if(is_array($topic['imgs'])) { foreach($topic['imgs'] as $img) { ?><img timgsrc="<?php echo getImgName($img['img'],100,100,0); ?>" class="show">
<? } } ?>
</div>
<? } else { ?>
<div class="no_pic" style=" "></div>
<? } ?>
<p class="ct">
<a class="tit fl" target="_blank" href="<?=$topic['url']?>">
<? if($topic['is_top'] == 1) { ?>
<span class="top fl">[置顶]</span> 
<? } ?><?php echo cutStr($topic['title'],60,'...');?></a><br /><?php echo setTplUserFormat($topic['uid'],0,0,'',0,'n','',''); ?><span class="p_time">
<? if(!empty($topic['lastposter'])) { ?>
最后回复 : <?php echo setTplUserFormat($topic['lastposter'],0,0,'',0,'n','',''); ?><?=$topic['last_time']?>
<? } ?>
</span>
</p>
</div>
<div class="count"><?=$topic['post_count']?></div>
<a class="all" target="_blank"  href="<?=$topic['url']?>">查看全文</a>
</li>
<? } } ?>
</ul>
<div class="pagination mt40"><? if($pager['page_count'] > 1) { ?>
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
<div id="sidebar" class="fr" style="width:260px;background-color:#fff;padding-right:30px;"><!--dynamic getBestFlashs args=<?=$root_forum['fid']?>--><div class="bar_member"> </div><!--dynamic getBestTopics--></div>
</div>
<div class="piece1_ft"></div>
</div>
<script type="text/javascript">
jQuery(function($){
<? if(getIsManage('club')) { ?>
$('.topic_list .tl_f').hover(function(){
var topicID = this.getAttribute('topicID');
$.GetManageMenu('club',topicID,this);
},function(){});
<? } ?>
});
</script><? include template('inc/footer'); ?>