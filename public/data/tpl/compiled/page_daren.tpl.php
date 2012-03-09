<? if(!defined('IN_FANWE')) exit('Access Denied'); 
0
|| checkTplRefresh('./tpl/pink2/page/daren.htm', './tpl/pink2/inc/header.htm', 1331260953, './data/tpl/compiled/page_daren.tpl.php', './tpl/pink2', 'page/daren')
|| checkTplRefresh('./tpl/pink2/page/daren.htm', './tpl/pink2/inc/pages.htm', 1331260953, './data/tpl/compiled/page_daren.tpl.php', './tpl/pink2', 'page/daren')
|| checkTplRefresh('./tpl/pink2/page/daren.htm', './tpl/pink2/inc/footer.htm', 1331260953, './data/tpl/compiled/page_daren.tpl.php', './tpl/pink2', 'page/daren')
;?>
<?php 
$css_list[0]['url'] = './tpl/css/daren.css';
 ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
<div id="body_wrap"><div id="body" class="fm960">
<div class="piece1">
<div class="piece1_hd"></div>
<div class="piece1_bd">
<div class="daren_title clearfix">
<div class="daren_logo"><img src="./tpl/images/logo-daren.png"></div>
<div class="daren_tag">
<a<? if($is_best) { ?> class="c"<? } ?> href="<?php echo FU('daren/index',array()); ?>">热荐达人</a>/ <a<? if($is_all) { ?> class="c"<? } ?> href="<?php echo FU('daren/all',array()); ?>" >所有达人</a></div>
</div>
<div id="daren_imagewall" class="daren_wall">
<div class="col1 clear_in"><? if(is_array($list['0'])) { foreach($list['0'] as $daren) { ?><div class="d_w_i" darenID="<?=$daren['id']?>">
<div class="pic">
<? if($daren['today_best']) { ?>
<div class="daren_jian"></div>
<? } ?>
<a target="_blank" href="<?=$daren['url']?>" ><img src="<?=$daren['img']?>" width="280"></a>
<div class="user">
<a class="gc n"><?=$daren['user_name']?></a><!--getfollow <?=$daren['uid']?> inc/getfollow/daren--><span class="locate"><?=$daren['city']?></span>
</div>
</div>
<div class="info"> <span>粉丝：</span><a ><?=$daren['fans']?></a> <span>微博：</span><a ><?=$daren['shares']?></a> <span>宝贝：</span><a class="fav" ><?=$daren['goods']?></a> </div>
<div class="u_intro clearfix">
<p class="intro fl" > <img src="./tpl/images/mark_left.png" > <?=$daren['reason']?> </p>
<img class="m_r" src="./tpl/images/mark_right.png"> </div>
</div>
<? } } ?>
</div>
<div class="col2 clear_in"><? if(is_array($list['1'])) { foreach($list['1'] as $daren) { ?><div class="d_w_i" darenID="<?=$daren['id']?>">
<div class="pic">
<? if($daren['today_best']) { ?>
<div class="daren_jian"></div>
<? } ?>
<a target="_blank" href="<?=$daren['url']?>" ><img src="<?=$daren['img']?>" width="280"></a>
<div class="user">
<a class="gc n"><?=$daren['user_name']?></a><!--getfollow <?=$daren['uid']?> inc/getfollow/daren--><span class="locate"><?=$daren['city']?></span>
</div>
</div>
<div class="info"> <span>粉丝：</span><a ><?=$daren['fans']?></a> <span>微博：</span><a ><?=$daren['shares']?></a> <span>宝贝：</span><a class="fav" ><?=$daren['goods']?></a> </div>
<div class="u_intro clearfix">
<p class="intro fl" > <img src="./tpl/images/mark_left.png" > <?=$daren['reason']?> </p>
<img class="m_r" src="./tpl/images/mark_right.png"> </div>
</div>
<? } } ?>
</div>
<div class="col3 clear_in">
<div class="be_daren">
<p> <img src="./tpl/images/back_to_bar.png" class="arrow"/>热爱购物？喜欢分享？爱一切扮靓的秘笈！快来申请做达人吧！ <img src="./tpl/images/daren_icon.png" alt="达人" /> </p>
<a href="<?php echo FU('daren/apply',array()); ?>" ><img src="./tpl/images/be_daren_green.png" alt="我要做达人" /></a>
</div><? if(is_array($list['2'])) { foreach($list['2'] as $daren) { ?><div class="d_w_i" darenID="<?=$daren['id']?>">
<div class="pic">
<? if($daren['today_best']) { ?>
<div class="daren_jian"></div>
<? } ?>
<a target="_blank" href="<?=$daren['url']?>" ><img src="<?=$daren['img']?>" width="280"></a>
<div class="user">
<a class="gc n"><?=$daren['user_name']?></a><!--getfollow <?=$daren['uid']?> inc/getfollow/daren--><span class="locate"><?=$daren['city']?></span>
</div>
</div>
<div class="info"> <span>粉丝：</span><a ><?=$daren['fans']?></a> <span>微博：</span><a ><?=$daren['shares']?></a> <span>宝贝：</span><a class="fav" ><?=$daren['goods']?></a> </div>
<div class="u_intro clearfix">
<p class="intro fl" > <img src="./tpl/images/mark_left.png" > <?=$daren['reason']?> </p>
<img class="m_r" src="./tpl/images/mark_right.png"> </div>
</div>
<? } } ?>
</div>
</div>
<div class="pagination" style="padding-bottom:10px; padding-top:20px;"><? if($pager['page_count'] > 1) { ?>
<div class="pages">
<? if($pager['page'] > 1) { ?>
<a href="<?=$pager['page_prev']?>" class="page_prev" page="<?=$pager['prev_page']?>">&lt;上一页</a>
<? } if(is_array($pager['page_nums'])) { foreach($pager['page_nums'] as $page_num) { if($pager['page'] == $page_num['name']) { ?>
<a class="c"><?=$page_num['name']?></a>
<? } elseif($page_num['name'] == '...') { ?>
<i>...</i>
<? } else { ?>
<a href="<?=$page_num['url']?>" page="<?=$page_num['name']?>"><?=$page_num['name']?></a>
<? } } } if($pager['page'] < $pager['page_count']) { ?>
<a href="<?=$pager['page_next']?>" class="page_next" page="<?=$pager['next_page']?>">下一页&gt;</a>
<? } ?>
</div>
<? } ?></div>
</div>
<div class="piece1_ft"></div>
</div>
</div>
<script type="text/javascript">
function UpdateUserFollow(obj,result)
{
if(result.status == 1)
{
$(obj).before('<img class="fo_ok" src="./tpl/images/followed_daren.png">');
$(obj).remove();
}
}
</script>
<? if(getIsManage('daren')) { ?>
<script type="text/javascript">
jQuery(function($){
$('.d_w_i').hover(function(e){
var darenID = this.getAttribute('darenID');
if(darenID)
$.GetManageMenu('daren',darenID,this,e);
},function(){});
});
</script>
<? } ?>	</div>
<div id="foot_wrap">
<div id="foot" class="fm960 mt10">
<div class="fl"><? if(is_array($_FANWE['cache']['navs']['all']['2']['childs'])) { foreach($_FANWE['cache']['navs']['all']['2']['childs'] as $navcate_id) { ?><?php 
$nav_cate = $_FANWE['cache']['navs']['all'][$navcate_id];
 ?>
<div class="links fl"><?php $first_nav_cate = ''; ?><b><?=$nav_cate['name']?></b>
<ul><? if(is_array($nav_cate['navs'])) { foreach($nav_cate['navs'] as $nav_id) { ?><?php $nav = $_FANWE['cache']['navs']['navs'][$nav_id]; ?><li><a href="<?=$nav['url']?>"<? if($nav['target'] == 1) { ?> target="_blank"<? } ?>><?=$nav['name']?></a></li>
<? } } ?>
</ul>
</div>
<? } } ?>
</div>
<div class="fr foot_right">     
 <div class="logos fl">
<a href="<?php echo FU('index',array()); ?>"><img class="logo fl" src="<?=$_FANWE['site_root']?><?=$_FANWE['setting']['foot_logo']?>" /></a>
</div>

</div>
</div>
<div class="blank20 mt10"></div>
<? if(MODULE_NAME == 'Index') { ?>
<div class="foot-links clearfix">
<span class="fl">
友情链接：<?php $link_index = 1; if(is_array($_FANWE['cache']['links']['all'])) { foreach($_FANWE['cache']['links']['all'] as $link) { if($link_index > 12) { ?><?php break; } ?>
<a href="<?=$link['url']?>" target="_blank"><?=$link['name']?></a>&nbsp;&nbsp;<?php $link_index++; } } ?>
</span>
<a href="<?php echo FU('link',array()); ?>" target="_blank" class="fr">更多...</a>
</ul>
</div>
<? } ?>
<div class="copyright"><?=$_FANWE['setting']['footer_html']?></div>
</div>
<div id="back2top"><a href="#"><span class="arrow">▲</span>回顶部</a></div>
<div id="USER_INFO_TIP" style="display:none;">
<div class="tip_info">
<img class="avatar" alt="" src="./tpl/images/loading_60.gif">
<div>
<p><a href="#">&nbsp;</a></p>
<p>获取用户信息...</p>
<p>&nbsp;</p>
</div>
</div>
<div class="tip_toolbar">&nbsp;</div>
<div class="tip_arrow"></div>
</div>
</body>
<?php 
    $default_js = array();
$default_js[] = './public/js/lang.js';
$default_js[] = './public/js/setting.js';
$default_js[] = './public/js/jquery.bgiframe.js';
$default_js[] = './public/js/jquery.weebox.js';
$default_js[] = './public/js/ajaxfileupload.js';
$default_js[] = './public/js/jquery.dragsort.js';
$default_js[] = './public/js/swfobject.js';
 ?>
<script src="<?php echo scriptParse($default_js); ?>" type="text/javascript" defer="true"></script><? if(is_array($js_list)) { foreach($js_list as $js) { ?><script src="<?php echo scriptParse($js); ?>" type="text/javascript"></script>
<? } } ?><!--dynamic getScript--><script type="text/javascript">
jQuery(function($){
$(".lazyload").lazyload({"placeholder":"./tpl/images/lazyload.gif"});
});
</script>
</html>