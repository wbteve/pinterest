<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<div class="piece1 mt20">
<div class="piece1_hd"></div>
<div class="piece1_bd" style="padding:10px 10px 20px;">
<ul class="topic_grid"><? if(is_array($forum_list)) { foreach($forum_list as $forum_item) { if(is_array($forum_item)) { foreach($forum_item as $forum) { ?><li class="tg_f">
             <a target="_blank" href="<?php echo FU('club/forum',array("fid"=>$forum['fid'])); ?>"><img class="tg_cover" src="<?=$forum['logo']?>"></a>
                <h2 class="tg_bar"><a target="_blank" href="<?php echo FU('club/forum',array("fid"=>$forum['fid'])); ?>"><?=$forum['name']?></a><span>(<?=$forum['thread_count']?>)</span></h2>
                <p class="tg_intro"><?=$forum['desc']?></p>
                <ul>
                <?php $topics_idx = 1; ?>                <? if(is_array($forum['topics'])) { foreach($forum['topics'] as $topic) { if($topics_idx<3) { ?>
<li class="tg_tpk"><a target="_blank" href="<?=$topic['url']?>"><?php echo cutStr($topic['title'],50,'...');?></a></li><?php $topics_idx++; } } } ?>
                </ul>
            </li>
<? } } } } ?>
</ul>
</div>
<div class="piece1_ft"></div>
</div>