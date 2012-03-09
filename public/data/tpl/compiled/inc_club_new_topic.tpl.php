<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<div class="piece1_bd">
<div class="fl" style="width:340px;margin:0 10px">
<div class="piece_title" style="margin-left:0"></div>
<div id="pic_tpk" class="pic_tpk">
<ul><? if(is_array($flash_list)) { foreach($flash_list as $topic) { ?><li class="pic_tpk_f">
<a target="_blank" href="<?=$topic['url']?>"><img alt="" width="340" src="<?=$topic['imgs']['0']['img']?>"></a>
<div class="title">
<a href="<?=$topic['url']?>" target="_blank"><?php echo cutStr($topic['title'],40,'...');?></a>
</div>
</li>
<? } } ?>
</ul>
</div>
<div id="pic_tpk_btn" class="pic_tpk_btn">
<div></div>
</div>
<div class="new_event"> <span class="title"></span><a href="<?php echo FU('event',array()); ?>" target="_blank" class="more">更多...</a>
<ul><? if(is_array($event_list)) { foreach($event_list as $topic) { ?><li> <span></span><a target="_blank" href="<?=$topic['url']?>">&nbsp;<?php echo cutStr($topic['title'],50,'...');?></a></li>
<? } } ?>
</ul>
</div>
</div>
<script type="text/javascript">
jQuery(function($){
$("#pic_tpk ul").carouFredSel({
curcular: false,
infinite: false,
auto : true,
pauseDuration:3000,
pagination: "#pic_tpk_btn div",
scroll: {
pauseOnHover: true
}
});
});
</script>
<div class="fl r_top" style="width:580px;margin:0 10px">
<div class="arr">
<a class="c_arr aleft alstop"  href="javascript:;"></a>
<a class="c_arr aright" href="javascript:;"></a>
</div >
<div class="f_i">
<ul id="new_topic_box" class="tr_in "><? if(is_array($best_list)) { foreach($best_list as $best_item) { ?><li class="f_ili"><? if(is_array($best_item)) { foreach($best_item as $topic_list) { ?><?php $topic_first = true; ?><?php $topic_index = 0; if(is_array($topic_list)) { foreach($topic_list as $topic) { if($topic_index == 0) { ?>
<div class="f_i_d">
<div class="f_i_t">
<p><a href="<?=$topic['url']?>" class="s_t" target="_blank"><?php echo cutStr($topic['title'],44,'...');?></a></p>
<p><span class="s_t2"><?php echo cutStr($topic['content'],56,'...');?></span></p>
</div>
<div class="f_i_img">
<a href="<?=$topic['url']?>" target="_blank"><img src="<?php echo getImgName($topic['imgs'][0]['img'],90,70,1); ?>" class="r10"/></a>
</div>
</div>
<ul class="f_i_ll">
<? } else { ?>
<li>
<span></span>
<a href="<?=$topic['url']?>" target="_blank"><?php echo cutStr($topic['title'],40,'...');?></a>
</li>
<? } ?><?php $topic_index++; } } if(count($topic_list) > 0) { ?>
</ul>
<? } if($topic_first) { ?>
<div style="height:40px;"></div><?php $topic_first = false; } } } ?>
</li>
<? } } ?>
</ul>
</div>
</div>
</div>
<script type="text/javascript">
var topTopicIndex = 1;
jQuery(function($){
$('.aleft').click(function(){
if(!$(this).hasClass('alstop'))
{
topTopicIndex--;
getNewTopics();
}
});

$('.aright').click(function(){
if(!$(this).hasClass('arstop'))
{
topTopicIndex++;
getNewTopics();
}
});
});

function getNewTopics()
{
$.ajax({ 
url: SITE_PATH+"services/service.php?m=topic&a=news",
type: "POST",
data:{"page":topTopicIndex},
dataType: "json",
success: function(result){
if(result.status == 1)
{
$("#new_topic_box").html(result.html);
}

$(".c_arr").removeClass('alstop').removeClass('arstop');
if(topTopicIndex >= 6)
{
topTopicIndex = 6;
$(".aright").addClass('arstop');
}
else if(topTopicIndex <= 1)
{
topTopicIndex = 1;
$(".aleft").addClass('alstop');
}
}
});
}
</script>