<? if(!defined('IN_FANWE')) exit('Access Denied'); if($is_follow > -1) { ?>
<div class="followed_del clearfix">
<? if($is_follow == 0) { ?>
<a onclick="$.User_Follow(<?=$uid?>,this,UMenuUpdateUserFollow);" href="javascript:;" class="addfo">加关注</a>
<? } else { ?>
<span class="followed">已关注</span>
<div class="followed_border"></div>
<a onclick="$.User_Follow(<?=$uid?>,this,UMenuUpdateUserFollow);" href="javascript:;" class="follow_del">取消</a>
<? } ?>
</div>
<? } ?>