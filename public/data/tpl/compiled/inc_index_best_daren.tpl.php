<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<div class="piece1 clearfix mb20">
<div class="piece1_hd"></div>
<div class="piece1_bd">	
<div class="best_daren">
<div class="dbaren_head">
<strong>美丽达人</strong>
<span class="ky fl">
<a href="<?php echo FU('book/dapei',array()); ?>">全部搭配</a>
<samp>|</samp>
<a href="<?php echo FU('book/look',array()); ?>">全部晒货</a>
<samp>|</samp>
<a href="<?php echo FU('daren/apply',array()); ?>">申请达人</a>
<samp>|</samp>
<a href="<?php echo FU('style/index',array()); ?>" class="style">搭配精选</a>
</span>
<a href="<?php echo FU('daren',array()); ?>" class="more fr">更多>></a>
</div>
<div class="list clearfix">
<ul><? if(is_array($daren_list)) { foreach($daren_list as $daren) { ?><li>
<a target="_blank" href="<?php echo FU('u/index',array("uid"=>$daren['uid'])); ?>" class="uimg"><img height="260" original="<?=$daren['img']?>" src="./tpl/images/lazyload.gif" class="lazyload" /></a>
<div class="uuser"><?php echo setTplUserFormat($daren['uid'],0,0,'',0,'uname','',''); ?><!--getfollow <?=$daren['uid']?> inc/getfollow/best_daren--></div>
<div class="inf"><?=$daren['reason']?></div>
</li>
<? } } ?>
</ul>
</div>
<div class="blank9"></div>
</div>
</div>
<div class="piece1_ft"></div>
</div>

<script>
jQuery(function($){
$(".best_daren .list li").hover(function(){
$(this).addClass("cur");
},function(){
$(this).removeClass("cur");
});
});
</script>