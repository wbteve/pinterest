<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<div id="lb_goods">
<div class="goods_box_r">
<img src="<?=$goods['item']['pic_url']?>" height="80">
<p><?=$goods['item']['name']?><span>¥<?=$goods['item']['price']?></span></p>
<input type="button" class="g_a r3 TIME_OUT_CLOSE" value="<?php echo sprintf('确　定（%d）',"3"); ?>" time="3" onclick="$.Goods_Close();"/>
</div>
</div>