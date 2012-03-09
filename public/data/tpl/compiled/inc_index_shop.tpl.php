<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<div class="piece1 clearfix mb20">
<div class="piece1_hd"></div>
<div class="piece1_bd">
<div class="index-shop-list">
<div class="shop_head clearfix">
<strong>当红好店...</strong>
<a href="<?php echo FU('shop',array()); ?>" class="more fr">更多>></a>
</div>
<div class="list clearfix">
<ul><? if(is_array($shop_list)) { foreach($shop_list as $shop) { ?><li><a href="<?php echo FU('shop/show',array("id"=>$shop[shop_id])); ?>" target="_blank" title="<?=$shop['shop_name']?>"><img width="50" height="50" src="<?=$shop['shop_logo']?>"/></a> </li>
<? } } ?>
</ul>
</div>
</div>
</div>
<div class="piece1_ft"></div>
</div>