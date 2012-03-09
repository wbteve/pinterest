<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? if(!empty($_FANWE['nav_title'])) { ?><?=$_FANWE['nav_title']?> - <? } if(empty($no_site_name)) { ?><?=$_FANWE['setting']['site_title']?><? } ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<meta name="keywords" content="<?=$_FANWE['seo_keywords']?><?=$_FANWE['setting']['site_keywords']?>" />
<meta name="description" content="<?=$_FANWE['seo_description']?><?=$_FANWE['setting']['site_description']?>" />
<link rel="icon" href="<?=$_FANWE['site_root']?>favicon.ico" type="image/x-icon" />
<?php 
$default_js[] = './public/js/jquery.js';
$default_js[] = './public/js/base.js';
        $default_js[] = './public/js/jquery.easing.js';
$default_js[] = './public/js/jquery.lazyload.js';
 ?>
<script src="<?php echo scriptParse($default_js); ?>" type="text/javascript"></script>
<?php 
$current_css[] = './tpl/css/reset.css';
$current_css[] = './tpl/css/base.css';
$current_css[] = './tpl/css/globe.css';
$current_css[] = './tpl/css/publishbox.css';
$current_css[] = './tpl/css/lightbox.css';
$current_css[] = './tpl/css/addfav.css';
 ?>
<link rel="stylesheet" type="text/css" href="<?php echo cssParse($current_css); ?>" media="all"/><? if(is_array($css_list)) { foreach($css_list as $css) { ?><link rel="stylesheet" type="text/css" href="<?php echo cssParse($css['url']); ?>"<? if(!empty($css['media'])) { ?> media="<?=$css['media']?>"<? } ?> />
<? } } ?>
<script type="text/javascript">
var SITE_PATH = '<?=$_FANWE['site_root']?>';
var SITE_URL = '<?=$_FANWE['site_url']?>';
var TPL_PATH = '<?=TPL_PATH?>';
var PUBLIC_PATH	 = '<?=PUBLIC_PATH?>';
var MODULE_NAME	 = '<?=MODULE_NAME?>';
var ACTION_NAME	 = '<?=ACTION_NAME?>';
var COOKIE_PRE = "<?=$_FANWE['config']['cookie']['cookie_pre']?>";
</script>
</head>
<body>
<div id="head">
<div id="info_bar">
<div class="fm960">
<div class="fl">
<a href="<?php echo FU('index',array()); ?>"><img class="logo fl" src="<?=$_FANWE['site_root']?><?=$_FANWE['setting']['site_logo']?>" /></a>
</div><!--dynamic getUserInfo--></div>
</div>
<div id="nav_bar">
<div class="fm960">
<ul class="fl">
<li class="f"><a href="<?php echo FU('index',array()); ?>"<? if(MODULE_NAME == 'Index') { ?> class="c"<? } ?>>首页</a></li>
<?php 
$g_r_cate = $_FANWE['cache']['goods_category']['root'];
$g_r_cate = &$_FANWE['cache']['goods_category']['all'][$g_r_cate];
 ?>
<li><a href="<?php echo FU('book/shopping',array()); ?>"<? if(MODULE_NAME == 'Book' && !defined('IS_DAPAI')) { ?> class="c"<? } ?>><?=$g_r_cate['cate_name']?></a></li>
<li><a href="<?php echo FU('album',array()); ?>"<? if(MODULE_NAME == 'Album') { ?> class="c"<? } ?>>杂志社</a></li>
<li><a href="<?php echo FU('book/dapei',array()); ?>"<? if(defined('IS_DAPAI')) { ?> class="c"<? } ?>>搭配秀</a></li>
<li>
<a href="<?php echo FU('club/index',array()); ?>"<? if(MODULE_NAME == 'Club') { ?> class="c"<? } ?>>主题吧</a>
<a class="cat<? if(MODULE_NAME == 'Ask') { ?> c<? } ?>" href="<?php echo FU('ask/index',array()); ?>">问答</a>
</li>
<li><a href="<?php echo FU('daren/index',array()); ?>"<? if(MODULE_NAME == 'Daren') { ?> class="c"<? } ?>>达人</a></li>
<? if($_FANWE['setting']['second_status'] == 1) { ?>
<li><a href="<?php echo FU('second',array()); ?>"<? if(MODULE_NAME == 'Second') { ?> class="c"<? } ?>>闲置</a></li>
<? } ?>
<li><a href="<?php echo FU('shop',array()); ?>"<? if(MODULE_NAME == 'Shop') { ?> class="c"<? } ?>>好店</a></li>
<li><a href="<?php echo FU('exchange',array()); ?>"<? if(MODULE_NAME == 'Exchange') { ?> class="c"<? } ?>>积分兑换</a></li>
</ul>

<div class="fr top_search r5">
<form action="<?php echo FU('book/search',array()); ?>" method="post">
<input type="text" name="tag" class="ts_txt" value="" tooltip="请输入关键字"/>
<input type="submit" value=" " class="ts_btn" />
<input type="hidden" name="action" value="search" />
<div class="ts_type fl" style="display:none;">
<input type="radio" name="t" value="8" id="s_h" checked="">
<label for="s_h">搜商品</label>
<input type="radio" value="2" name="t" id="s_p">
<label for="s_p">搜人</label>
</div>
</form>
</div>

</div>
</div>
</div>
<div style="margin:0 auto;width:960px"><!--dynamic advLayoutName args=头部广告位,,--></div>
<div id="body_wrap">