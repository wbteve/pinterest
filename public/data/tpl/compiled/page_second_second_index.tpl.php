<? if(!defined('IN_FANWE')) exit('Access Denied'); 
0
|| checkTplRefresh('./tpl/pink2/page/second/second_index.htm', './tpl/pink2/inc/pages.htm', 1331260952, './data/tpl/compiled/page_second_second_index.tpl.php', './tpl/pink2', 'page/second/second_index')
;?>
<?php 
$css_list[0]['url'] = './tpl/css/tweetlist.css';
$css_list[1]['url'] = './tpl/css/second.css';
$js_list[0] = './tpl/js/share_list.js';
 include template('inc/header'); ?><div id="body" class="fm960">
<div id="content" class="fl">
<div class="piece2">
<div class="piece2_hd"></div>
<div class="piece2_bd ershou_talk">
<img class="ershou_title" src="./tpl/images/ershou_title.png">
<div class="ershou_select">
<ul class="es_cata_l">
<li>按分类:</li>
<li><span<? if($sid == 0) { ?> class="current"<? } ?>><a href="<?=$second_all_url?>"<? if($sid == 0) { ?> class="current"<? } ?>>全部</a></span><? if($sid == 0) { ?><i></i><? } ?></li><? if(is_array($seconds)) { foreach($seconds as $second) { ?><li><span<? if($second['current']) { ?> class="current"<? } ?>><a href="<?=$second['url']?>"<? if($second['current']) { ?> class="current"<? } ?>><?=$second['name']?></a></span><? if($second['current']) { ?><i></i><? } ?></li>
<? } } ?>
</ul>
<ul class="es_cata_l">
<li>按地区:</li>
<li><span<? if($cid == 0) { ?> class="current"<? } ?>><a href="<?=$city_all_url?>"<? if($cid == 0) { ?> class="current"<? } ?>>所有地区</a></span><? if($cid == 0) { ?><i></i><? } ?></li><?php $index=1; if(is_array($citys)) { foreach($citys as $city) { ?><li><span<? if($city['current']) { ?> class="current"<? } ?>><a href="<?=$city['url']?>"<? if($city['current']) { ?> class="current"<? } ?>><?=$city['name']?></a></span><? if($city['current']) { ?><i></i><? } ?></li><?php $index++; if($index > 13) { ?><?php break; } } } ?>
</ul>
</div>
<ul class="t_l" id="secondListBox"><? if(is_array($goods_list)) { foreach($goods_list as $share_item) { ?><li class="t_f" shareID="<?=$share_item['share_id']?>" id="SHARE_LIST_<?=$share_item['share_id']?>">
<div class="t_tag"><span class="t"><?=$share_item['time']?></span></div>
<div class="hd"><?php echo setTplUserFormat($share_item['uid'],1,0,'m',48,'','avt icard r5 lazyload',''); ?></div>
<div class="tk">
<div class="inf"><?php echo setTplUserFormat($share_item['uid'],0,1,'',0,'icard n gc','',''); ?> <span></span> </div>
<p class="sms"><?php echo cutStr($share_item['content'],200,'...');?></p>
<div class="alipay">
<div class="ap_info mb5"><a href="<?=$share_item['page']?>" target="_blank" class="ap_title mg_slink"><?=$share_item['name']?></a><img class="ap_logo" src="./tpl/images/alipay.png"><span class="ap_price"><i>¥</i><?=$share_item['price']?></span>&nbsp;&nbsp;&nbsp;运费：<?=$share_item['transport_fee']?>元&nbsp;&nbsp;&nbsp;数量：<?=$share_item['num']?></div>
<a href="<?=$share_item['page']?>" target="_blank" class="ap_buy">立即购买</a></div>
<div class="pic"><? if(is_array($share_item['imgs'])) { foreach($share_item['imgs'] as $share_item_img) { ?><div class="r3 fl"><img class="lazyload" original="<?php echo getImgName($share_item_img['img'],160,160,0); ?>" src="./tpl/images/lazyload.gif"></div>
<? } } ?>
<br class="clear">
</div>
<ul class="pic_b" ><? if(is_array($share_item['imgs'])) { foreach($share_item['imgs'] as $share_item_img) { ?><li style="width: 468px; display: list-item; display:none;" class="pic_b_f r5">
<div class="pic_b_bd"> <a href="javascript:;" class="add_to_album_btn"></a> <img class="lazyload" original="<?php echo getImgName($share_item_img['img'],468,468,0); ?>" src="./tpl/images/lazyload.gif"> </div>
<div class="show_big" style="display:block;"> <img src="./tpl/images/book_13x13.png" class="big_book"/> <a class="big_detail" href="<?=$share_item['url']?>" target="_blank" ref="nofollow">详情</a> <img src="./tpl/images/big_13x13.png" class="big_cur"/> <a class="bigimg" href="<?=$share_item_img['img']?>" target="_blank" ref="nofollow">查看原图</a> </div>
</li>
<? } } ?>
</ul>
<div class="tl">
<a w="f" href="javascript:;" class="add_fav fav" onclick="$.Fav_Share(<?=$share_item['share_id']?>,this,32,'#SHARE_LIST_<?=$share_item['share_id']?>');"></a>
<div class="favDiv">
<a href="<?=$share_item['url']?>" target="_blank" class="SHARE_FAV_COUNT favCount"><?=$share_item['collect_count']?></a><i></i>
</div>
<a w="f" href="javascript:;" class="fw" onclick="$.Relay_Share(<?=$share_item['share_id']?>);">转发(<?=$share_item['relay_count']?>)</a>
<a w="f" href="javascript:;" class="cmt" shareID="<?=$share_item['share_id']?>" onclick="$.Get_Share_Comment_List(this);">评论(<?=$share_item['comment_count']?>)</a>
</div>
</div>
</li>
<? } } ?>
</ul>
<div class="pagination"> <? if($pager['page_count'] > 1) { ?>
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
<div class="piece2_ft"></div>
</div>
</div>
<div id="sidebar" class="fr">
<div class="piece3 mb20">
<div class="piece3_hd"></div>
<div class="piece3_bd"> <a alt="担保交易" href="<?php echo FU('second/create',array()); ?>" target="_blank" class="add_alipay fr" style="margin-right:10px;"><img src="./tpl/images/alipay_publish_new.png"></a> </div>
<div class="piece3_ft"></div>
</div>
<div class="piece3 mb20">
<div class="piece3_hd"></div>
<div class="piece3_bd alipay_notice">
<span class="piece_tltle">二手闲置交易须知</span>
<p>
<span>1. 本服务仅供网友发布二手闲置交易信息，不允许发布任何批量贩卖、代购等信息。不欢迎专业卖家和电商在此发布信息。</span>
<span>2. 为了交易安全，建议大家选择"支付宝担保交易"服务或当地见面交易来保障交易安全。</span>
<span>3. 发布的交易信息必须包含二手闲置物品的实拍照片，没有照片的消息会被随时删除。</span>
<span>4. <?=$_FANWE['setting']['site_name']?>不对交易信息和交易过程负责，交易前请阅读<a href="javascript:;" class="notice_a">《免责条款》</a>，如有疑意请关闭本页面。</span>
</p>
</div>
<div class="piece3_ft"></div>
</div>
</div>
<div class="clear"></div>
</div>
<div id="second_articles" style="display:none">
<div id="event_notice">
<div class="notice_detail">
<h1>欢迎您使用<?=$_FANWE['setting']['site_name']?>二手闲置交易信息发布公告板。</h1>
<textarea><?=$_FANWE['setting']['second_articles']?></textarea>
</div>
<div class="acc_box">
<a href="javascript:;" class="acc_btn r3">我接受以上条款，确定</a><a href="<?php echo FU('u/me',array()); ?>" class="can_btn r3">我不接受以上条款</a>
</div>
</div>
</div>
<script type="text/javascript">
jQuery(function($){
$(".notice_a").click(function(){
$.weeboxs.open($("#second_articles").html(),{boxid:'SECOND_NOTICE_BOX',title:'免责条款',contentType:'text',draggable:false,modal:true,showButton:false,showHeader:true,width:448,height:200});
$("#SECOND_NOTICE_BOX .dialog-close").hide();
$("#SECOND_NOTICE_BOX .acc_btn").click(function(){
$.weeboxs.close();
});
});

<? if(getIsManage('second')) { ?>
$('#secondListBox .t_f').hover(function(){
var shareID = this.getAttribute('shareID');
if(shareID)
$.GetManageMenu('second',shareID,this);
},function(){});
<? } ?>
});
</script><? include template('inc/footer'); ?>