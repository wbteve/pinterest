<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<div id="lb_goods">
<div class="goods_box GOODS_BOX">
<span>将商品网址粘贴到下面框中即可。</span>
<div style="overflow:hidden;zoom:1">
<input class="g_url fl rl3 GOODS_URL" type="text" />
<input class="g_s fl rr3 GOODS_COLLECT" value="确 定" type="button" />
</div>
<div class="support">
已支持以下网站（<a class="in" href="mailto:<?=$_FANWE['setting']['site_service_email']?>">商家申请加入</a>）：
<p><? if(is_array($business)) { foreach($business as $key => $item) { ?><a href="<?=$item['url']?>" target="_blank" title="<?=$item['name']?>" style="background-image:url(<?=$item['icon']?>);"><?=$item['name']?></a>
<? } } ?>
</p>
</div>
</div>
<div class="lb_loading PUB_LOADING">
<img class="fl" src="./tpl/images/loading.gif">&nbsp;&nbsp;请稍候......</a>
</div>
</div>