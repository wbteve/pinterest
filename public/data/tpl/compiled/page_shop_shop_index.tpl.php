<? if(!defined('IN_FANWE')) exit('Access Denied'); 
0
|| checkTplRefresh('./tpl/pink2/page/shop/shop_index.htm', './tpl/pink2/inc/pages.htm', 1331260950, './data/tpl/compiled/page_shop_shop_index.tpl.php', './tpl/pink2', 'page/shop/shop_index')
;?>
<?php 
$css_list[0]['url'] = './tpl/css/shop.css';
$js_list[0] = './tpl/js/shop.js';
 include template('inc/header'); ?><div id="body" class="fm960">
<div class="top_shop_cat box_shadow">
<div class="cat">
<h3><a href="<?php echo FU('shop',array()); ?>" class="cat_name<? if(0 == $cid) { ?> cat_current<? } ?>">全部精选</a></h3>
</div><? if(is_array($_FANWE['cache']['shops']['root'])) { foreach($_FANWE['cache']['shops']['root'] as $root_id) { ?><?php $root_cate = $_FANWE['cache']['shops']['all'][$root_id]; ?><?php $root_current = in_array($cid,$root_cate['childs']); ?><div class="cat">
<h3><a href="<?=$root_cate['url']?>" class="cat_name<? if($root_current || $root_id == $cid) { ?> cat_current<? } ?>"><?=$root_cate['name']?></a></h3>
<div style="padding-left: 13px;"><? if(is_array($root_cate['childs'])) { foreach($root_cate['childs'] as $child_id) { ?><?php $child_cate = $_FANWE['cache']['shops']['all'][$child_id]; ?><div class="sub_cat_name<? if($child_id == $cid) { ?> sub_cat_current<? } ?>"> <a href="<?=$child_cate['url']?>"><?=$child_cate['name']?></a> </div>
<? } } ?>
<div class="clear-fix"></div>
</div>
</div>
<? } } ?>
</div>
<div class="top_shop_list box_shadow"><? if(is_array($shop_list)) { foreach($shop_list as $shop_item) { ?><div class="shop_item" shopID="<?=$shop_item['shop_id']?>">
<a href="<?=$shop_item['url']?>" target="_blank" class="click_area"></a>
<a href="<?=$shop_item['url']?>" target="_blank" class="link_btn" style="display: none;"></a>
<h4 class="title"><?php echo cutStr($shop_item['shop_name'],36,'...');?></h4>
<div class="stat">
推荐人数：<span style="color: #f36;"><?=$shop_item['recommend_count']?></span><br/>
店铺看点：<? if(is_array($shop_item['tags'])) { foreach($shop_item['tags'] as $tag) { ?><a href="<? echo FU('book/shopping',array('tag'=>urlencode($tag))); ?>" target="_blank"><?=$tag?></a> <? } } ?>
</div>
<div class="goods_list"><? if(is_array($shop_item['imgs'])) { foreach($shop_item['imgs'] as $img) { ?><img src="<?php echo getImgName($img,100,100,0); ?>" /> 
<? } } ?>
<div class="clear-fix"></div>
</div>
</div>
<? } } ?>
<div class="clear-fix"></div>
<? if($pager['page_count'] > 1) { ?>
<div class="pagination mt20"> <? if($pager['page_count'] > 1) { ?>
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
<? } ?> 
</div>
<? } ?>
</div>
<div class="clear-fix"></div>
</div>
<? if(getIsManage('ask')) { ?>
<script type="text/javascript">
jQuery(function($){
$('.top_shop_list .shop_item').hover(function(){
var shopID = this.getAttribute('shopID');
$.GetManageMenu('shop',shopID,this);
},function(){});
});
</script>
<? } include template('inc/footer'); ?>