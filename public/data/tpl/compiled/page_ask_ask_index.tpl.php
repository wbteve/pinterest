<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<?php 
$css_list[0]['url'] = './tpl/css/ask.css';
 include template('inc/header'); ?><div id="body" class="fm960">
<div class="piece1">
<div class="piece1_hd"></div>
<div class="piece1_bd clearfix">
<div id="content" class="fl">
<div class="mg_ask"></div>
<div class="ask_head r10">
<div class="ask_pic"><img src="./tpl/images/ask_q.png"></div>
<div class="ask_text">
<span>一切购物问题，<?=$_FANWE['setting']['site_name']?>网友都乐意为您解答！</span>
<p>
<b>提问须知：</b><br />
1. 只问与购物相关的问题，网友们不懂天文和地理；<br />
2. 说清自己的情况，"我穿什么衣服好看？"是个傻问题，<?=$_FANWE['setting']['site_name']?>不是魔镜；<br />
3. 及时选择最佳答案，这是对热心网友的基本尊重。
</p>
<a href="<?php echo FU('ask/newtopic',array()); ?>"><img src="./tpl/images/my_q.png"></a> </div>
</div>
<div class="ask_cat_grid clearfix">
<ul class="fl"><? if(is_array($ask_list['l'])) { foreach($ask_list['l'] as $ask) { ?><li class="grid_all">
<ul class="grid_fl">
<li>
<div>
<span><?=$ask['index_char']?>.</span>
<div class="grid_title"><a href="<?=$ask['url']?>"><?=$ask['name']?></a></div>
<div style="float:right;">
<img class="grid_right" src="./tpl/images/back_to_bar.png">
<a class="look_all" target="_blank" href="<?=$ask['url']?>">查看全部</a>
</div>
</div>
</li><? if(is_array($ask['list'])) { foreach($ask['list'] as $ask_thread) { ?><li>
<? if($ask_thread['is_solve'] == 1) { ?>
<img alt="" src="./tpl/images/icon_06.png">
<? } else { ?>
<img src="./tpl/images/icon_03.png" alt="" />
<? } ?>
<div class="grid_text"><a target="_blank" href="<?php echo FU('ask/detail',array("tid"=>$ask_thread['tid'])); ?>"><?php echo cutStr($ask_thread['title'],32,'...');?></a> </div>
<div class="reply"><?=$ask_thread['post_count']?></div>
</li>
<? } } ?>
</ul>
</li>
<? } } ?>
</ul>
<ul class="fr"><? if(is_array($ask_list['r'])) { foreach($ask_list['r'] as $ask) { ?><li class="grid_all">
<ul class="grid_fl">
<li>
<div>
<span><?=$ask['index_char']?>.</span>
<div class="grid_title"><a href="<?=$ask['url']?>"><?=$ask['name']?></a></div>
<div style="float:right;">
<img class="grid_right" src="./tpl/images/back_to_bar.png">
<a class="look_all" target="_blank" href="<?=$ask['url']?>">查看全部</a>
</div>
</div>
</li><? if(is_array($ask['list'])) { foreach($ask['list'] as $ask_thread) { ?><li>
<? if($ask_thread['is_solve'] == 1) { ?>
<img alt="" src="./tpl/images/icon_06.png">
<? } else { ?>
<img src="./tpl/images/icon_03.png" alt="" />
<? } ?>
<div class="grid_text"><a target="_blank" href="<?php echo FU('ask/detail',array("tid"=>$ask_thread['tid'])); ?>"><?php echo cutStr($ask_thread['title'],32,'...');?></a> </div>
<div class="reply"><?=$ask_thread['post_count']?></div>
</li>
<? } } ?>
</ul>
</li>
<? } } ?>
</ul>
</div>
</div>
<div id="sidebar" class="fr">
<div class="ask_star">
<h2 class="tsb_title">热门图片...</h2>
<ul><? if(is_array($hot_asks)) { foreach($hot_asks as $ask_thread) { ?><li>
<a href="<?=$ask_thread['url']?>"><img width="48" height="48" src="<?php echo getImgName($ask_thread['imgs'][0]['img'],80,80,0); ?>" class="avatar_img r5"/></a>
<div class="medal_text">
<a href="<?=$ask_thread['url']?>" target="_blank" class="gc"><?php echo cutStr($ask_thread['title'],20,'...');?></a>
<div>
<?=$ask_thread['time']?><br>
<span><?php echo setTplUserFormat($ask_thread['uid'],0,1,'',0,'','',''); ?></span>
</div>
</div>
</li>
<? } } ?>
</ul>
</div>
<div class="topic_list_sidebar">
<h2 class="tsb_title">刚刚提出的问题...</h2>
<ul><? if(is_array($new_asks)) { foreach($new_asks as $ask_thread) { ?><li> <img src="./tpl/images/back_to_bar.png" /><a href="<?php echo FU('ask/detail',array("tid"=>$ask_thread['tid'])); ?>" target="_blank"><?php echo cutStr($ask_thread['title'],36,'...');?></a> </li>
<? } } ?>
</ul>
</div>
</div>
</div>
<div class="piece1_ft"></div>
</div><? include template('inc/footer'); ?>