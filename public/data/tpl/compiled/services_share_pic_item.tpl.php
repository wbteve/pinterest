<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<li class="p_f PUB_SHARTE_PIC" title="拖动图片进行排序">
<div class="r5">
<a class="del" href="javascript:;" onclick="$.Pub_Share_Img_Remove(this);"></a>
<p><img src="<?=$result['img']?>" height="80"></p>
<input type="hidden" name="pics[]" value="<?=$result['info']?>" />
<input type="hidden" class="share_sort" name="pics_sort[]" value="" />
</div>
</li>