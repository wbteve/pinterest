<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<?php 
$css_list[0]['url'] = './tpl/css/welcome.css';
$js_list[0] = './tpl/js/welcome.js';
 include template('inc/header'); ?><div id="body" class="fm960">
<div class="piece1 mb20">
<div class="piece1_left"></div>
<div class="piece1_hd"></div>
<div class="piece1_bd"><!--dynamic advLayoutName args=首页轮播广告位,adv/indexlflashadv,--><div class="index-active-list fr"><!--dynamic getPinkUserInfo--><!--dynamic advLayoutName args=首页右侧用户信息底部广告位,adv/user_info_bottom_adv,--><div class="mobile">
<a class="mbtn iphone" href="#" target="_blank" title="iPhone客户端">iphone客户端</a>
<a class="mbtn android" href="#" target="_blank" title="Android客户端">Andioid客户端</a>
</div>
</div>
<div class="blank3"></div>
</div>
<div class="piece1_ft"></div>
</div><!--dynamic getUUBestDarens--><!--dynamic getIndexCateShare--><!--dynamic getUUIndexShop--></div><? include template('inc/footer'); ?>