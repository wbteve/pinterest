<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<?php 
$css_list[0]['url'] = './tpl/css/club.css';
$js_list[0][] = './public/js/jquery.slides.js';
$js_list[0][] = './tpl/js/club.js';
 include template('inc/header'); ?><div id="body" class="fm960">
<div class="piece1 mb20">
<div class="piece1_hd"></div><!--dynamic getNewBestTopics--><div class="piece1_ft"></div>
</div><!--dynamic getRootForumBests--><div class="clear"></div>
</div><? include template('inc/footer'); ?>