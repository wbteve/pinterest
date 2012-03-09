<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<div class="shops psb bt1 bb1">
<h2><a><?=$user_lang?>的分享店铺信息...</a></h2>
<span class="shops_c">
<span class="gc"><?=$user['user_name']?></span>共分享<b style="color:#690"><?=$user['goods']?></b>件商品，来自<b style="color:#690"><?=$shops['total']?></b>家店铺
</span>
<dl class="shops_per">
<dt class="t">占比</dt>
<dd class="t">店铺</dd><? if(is_array($shops['list'])) { foreach($shops['list'] as $shop) { ?><dt><?=$shop['percent']?></dt>
<dd> <a href="<?php echo FU('shop/show',array("id"=>$shop['shop_id'])); ?>" target="_blank"><?php echo cutStr($shop['shop_name'],30,'...');?></a> </dd>
<? } } ?>
</dl>
</div>