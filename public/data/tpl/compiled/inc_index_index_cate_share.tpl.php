<? if(!defined('IN_FANWE')) exit('Access Denied'); if($cate_list) { if(is_array($cate_list)) { foreach($cate_list as $citem) { if($citem['share_list']) { ?>
<div class="piece1 mb20">
<div class="piece1_hd"></div>
<div class="piece1_bd" style="padding:5px 25px ">
<div class="share_title">
<em><a target="_blank" href="<?php echo FU('book/cate',array("cate"=>$citem['cate_code'])); ?>"><span>分享</span><?=$citem['short_name']?></a></em>
<span class="st_key">
<a target="_blank" href="<?php echo FU('book/cate',array("cate"=>$citem['cate_code'],"sort"=>"hot7")); ?>">热门</a>
<a target="_blank" href="<?php echo FU('book/cate',array("cate"=>$citem['cate_code'],"sort"=>"new")); ?>">最新</a>
<span>|</span><?php $tag_index =1; if(is_array($citem['hot_tags'])) { foreach($citem['hot_tags'] as $tag) { ?><a target="_blank" href="<?php echo FU('book/shopping',array("sort"=>"hot7","tag"=>$tag['encode'])); ?>"><?=$tag['tag_name']?></a>
<? if($tag_index > 11) { ?><?php break; } ?><?php $tag_index++; } } ?>
</span>
<span class="more fr">
<a href="<?php echo FU('book/cate',array("cate"=>$citem['cate_code'])); ?>">更多>></a>
</span>
</div>
<div class="cate_share_box">
<ul>
<li ckass="first"><!--dynamic advLayoutName args=分类右侧大图广告185X330,,<?=$citem['cate_code']?>--></li><?php $cate_idx = 1; if(is_array($citem['share_list'])) { foreach($citem['share_list'] as $share) { if($cate_idx <9) { if(($cate_idx>=1 && $cate_idx<=3) ||($cate_idx>=6 && $cate_idx<=8)) { ?><?php $class = 'two'; if($cate_idx>=1 && $cate_idx<=3) { ?><?php $left=195+($cate_idx-1)*160; ?><?php $top=0; } else { ?><?php $left=440+($cate_idx-6)*160; ?><?php $top=170; } ?><?php $img_w=150; ?><?php $img_h=160; } elseif($cate_idx>=4&&$cate_idx<=5) { ?><?php $class = 'three'; if($cate_idx==4) { ?><?php $top=0; ?><?php $left=675; } else { ?><?php $top=170; ?><?php $left=195; } ?><?php $img_w=235; ?><?php $img_h=160; } ?>
<li class="<?=$class?>" style="top:<?=$top?>px;left:<?=$left?>px">
<a href="<? if($share['imgs']['0']['url']) { ?><?=$share['imgs']['0']['url']?><? } else { ?><?=$share['url']?><? } ?>" target="_blank"><?php $index_img = $share['index_img']; if($index_img) { ?>
<img class="img" src="<?php echo getImgName($index_img,"$img_w","$img_h",1); ?>" />
<? } else { ?>
<img class="img" src="<?php echo getImgName($share['imgs'][0]['img'],"$img_w","$img_h",1); ?>" />
<? } ?>
</a>
<a class="trsp_bg w<?=$img_w?>" href="<? if($share['imgs']['0']['url']) { ?><?=$share['imgs']['0']['url']?><? } else { ?><?=$share['url']?><? } ?>" target="_blank">
<h4>
<span class="likeit fl">
<b class="nums red"><? echo intval($share['collect_count']); ?></b>
</span>
<span class="f12 fr"><?=$share['tag_name']?></span>
</h4>
</a>
</li><?php $cate_idx++; } } } ?>
</ul>
</div>
<div class="cate_share_who  clearfix">
<div class="t fl">
分享 <a target="_blank" href="<?php echo FU('book/cate',array("cate"=>$citem['cate_code'])); ?>"><?=$citem['short_name']?></a> 的<br>美丽们
</div>
<ul class="fl clearfix"><?php $user_index = 0; if(is_array($citem['user'])) { foreach($citem['user'] as $uid => $user) { if($uid > 0) { ?>
<li>
<? if($user_index > 4) { ?><?php break; } ?><?php $user_index++; ?><?php echo setTplUserFormat($uid,1,0,'s',32,'','r3 icard lazyload',''); ?><a href="<?php echo FU('u/index',array("uid"=>$uid)); ?>" target="blank" class="fl"><span class="name" title="<?=$user?>"><?php echo cutStr($user,8,'...');?></span></a>
<div class="follow fl">
<?php 
$is_follow = FS('User')->getIsFollowUId($uid);
if($is_follow)
{
 ?>
<a onclick="$.User_Follow(<?=$uid?>,this,CateUpdateUserFollow);" href="javascript:;"><img src="./tpl/images/flow_ok.gif" /></a>
<?php 
}else{
 ?>
<a onclick="$.User_Follow(<?=$uid?>,this,CateUpdateUserFollow);" href="javascript:;" class="addfollow"><img src="./tpl/images/to_flow.gif" /></a>
<?php 
}
 ?>
</div>
</li>
<? } } } ?>
</ul>
<div class="share_count fr">
<b><?=$citem['share_count']?>人</b><br>
<span>正在分享</span>
</div>
</div>
</div>
<div class="piece1_ft"></div>
</div>
<? } } } ?>
<script>
function CateUpdateUserFollow(obj,result)
{
if(result.status == 1)
{
$(obj).before('<img src="./tpl/images/flow_ok.gif" />');
$(obj).remove();
}
else{
$(obj).before('<img src="./tpl/images/to_flow.gif" />');
$(obj).remove();
}
}

jQuery(function(){
$('.cate_share_box li').hover(function(){
$(this).children('.trsp_bg').stop().animate( { height: "80px" }, 300 );
$(this).children('.trsp_bg').find('h4 .f12').stop().animate({ fontSize: "18px" }, 50);
},
function(){
$(this).children('.trsp_bg').stop().animate( { height: "30px" }, 300 );
$(this).children('.trsp_bg').find('h4 .f12').stop().animate({ fontSize: "12px" }, 50);
});
});

</script>
<? } ?>