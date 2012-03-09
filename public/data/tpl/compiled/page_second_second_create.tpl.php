<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<?php 
$css_list[0]['url'] = './tpl/css/second.css';
$js_list[0] = './tpl/js/second.js';
 include template('inc/header'); ?><div id="body" class="fm960">
<div class="piece1">
<div class="piece1_hd"></div>
<div class="piece1_bd clearfix">
<div id="content" style="width:960px;">
<div class="ershou_publish">
<img src="./tpl/images/ershou_title.png" class="ml25 mt10">
<div class="ep_arae">
<div class="epa_t">
<span>创建支付宝担保交易</span>
<a href="<?php echo FU('second',array()); ?>" class="fr">返回上一页</a>
</div>
<form class="ep_form" id="secondForm" method="post" action="<?php echo FU('second/save',array()); ?>">
<div class="ep_items">
<div class="epi_f">
<label class="epi_t">宝贝名称：</label>
<input class="epi_text" type="text" id="secondTitle" name="title" value="" style="width:245px;"/>
<div class="epi_tip" style="left:390px;">请控制在20个汉字内。</div>
<div class="epi_err" style="left:390px;"></div>
<div class="epi_ok" style="left:390px;"></div>
</div>
<div class="epi_f">
<label class="epi_t">宝贝描述：</label>
<textarea class="epi_content" id="secondContent" name="content" ></textarea>
<div class="epi_tip" style="left:560px;">请输入宝贝描述内容，长度不能超过500个汉字<br>
(您还可以输入<span class="ct_count">500</span>个汉字)。</div>
<div class="epi_err" style="left:560px;"></div>
<div class="epi_ok" style="left:560px;"></div>
</div>
<div class="epi_f" id="secondImgs">
<label class="epi_t">宝贝图片：</label>
<input class="epi_file_fake" type="button" value="点击上传图片" onclick="$.Pic_Add(this)"/>
<input type="hidden" class="HIDE_PIC_TYPE" value="1" />
<div class="epi_tip" style="left:225px;display:block">可上传gif、jpg 、jpeg、png、bmp格式图片，大小请控制在2M以内。</div>
<div class="epi_err" style="left:600px;"></div>
<div class="epi_ok" style="left:600px;"></div>
<div class="clear"></div>
<ul class="pub_img PUB_IMG"></ul>
<div class="clear"></div>
</div>
<div class="epi_f">
<label class="epi_t">选择分类：</label>
<div class="epi_cate"><?php $check='checked="checked"'; if(is_array($_FANWE['cache']['seconds'])) { foreach($_FANWE['cache']['seconds'] as $second) { ?><label><input name="sid" value="<?=$second['sid']?>" type="radio"<?=$check?>/><span><?=$second['name']?></span></label>&nbsp;&nbsp;<?php $check=''; } } ?>
</div>
</div>
<div class="epi_f">
<label class="epi_t">宝贝数量：</label>
<select class="epi_baonum" name="num">
<option value="1" >1</option>
<option value="2" >2</option>
<option value="3" >3</option>
</select>
<div class="epi_tip" style="left:200px;"></div>
</div>
<div class="epi_f">
<label class="epi_t">宝贝单价：</label>
<input class="epi_text" type="text" id="secondPrice" name="price" style="width:75px;" value="" />
元
<div class="epi_err" style="left:230px;"></div>
<div class="epi_ok" style="left:230px;"></div>
</div>
<div class="epi_f">
<label class="epi_t">运费：</label>
<input class="epi_text" type="text" name="fare" id="secondFare" style="width:75px;" value="0" />
元
<div class="epi_err" style="left:230px;"></div>
<div class="epi_ok" style="left:230px;"></div>
</div>
<div class="epi_f">
<label class="epi_t">交易有效期：</label>
<select class="epi_select" name="valid_time" id="valid_time">
<option value="30">30天</option>
</select>
<div class="epi_tip" style="left:195px;"></div>
</div>
<div class="epi_f">
<input class="epi_submit green_button r3" type="submit" value="确定" />
<input type="hidden" name="action" value="save" />
<a class="epi_cancel" href="<?php echo FU('second',array()); ?>">取消</a>
</div>
</div>
</form>
</div>
</div>
</div>
</div>
<div class="piece1_ft"></div>
</div>
</div>
<script type="text/javascript">
jQuery(function($){
$('.PUB_IMG li').live('mousedown',function(){
$('.PUB_IMG').dragsort();
});
<? if($_FANWE['user']['is_buyer'] < 1) { ?>
$.weeboxs.open('<div><div class="es_title">去认证吧~ 只有买家认证用户才能发表二手闲置交易哦！</div><div class="buyer"><a href="<?php echo FU('settings/buyerverifier',array()); ?>"></a></div></div>',{boxid:'NO_BUYER_BOX',title:'你还没有通过买家认证',contentType:'text',draggable:false,modal:true,showButton:false,showHeader:true,width:448,height:130});
$("#NO_BUYER_BOX .dialog-close").hide();
<? } elseif($_FANWE['user']['avatar_status'] == 0) { ?>
$.weeboxs.open( '<div><div class="es_title">设置一个头像吧~ 这样才可以发表二手闲置交易哦！</div><div class="avatar"><a href="<?php echo FU('settings/avatar',array()); ?>"></a></div></div>',{boxid:'NO_AVATAR_BOX',title:'你还没有上传头像',contentType:'text',draggable:false,modal:true,showButton:false,showHeader:true,width:448,height:130});
$("#NO_AVATAR_BOX .dialog-close").hide();
<? } ?>
});
</script><? include template('inc/footer'); ?>