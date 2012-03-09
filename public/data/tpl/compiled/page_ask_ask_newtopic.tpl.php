<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<?php 
$css_list[0]['url'] = './tpl/css/topic.css';
 include template('inc/header'); ?><div id="body" class="fm960">
<div class="piece1">
<div class="piece1_hd"></div>
<div class="piece1_bd clearfix">
<div id="content" style="width:960px;">
<form id="ask_form" action="<?php echo FU('ask/donewtopic',array()); ?>" method="post">
<div id="topic_create">
<div id="topic_nav">
<a href="<?php echo FU('ask/index',array()); ?>">问答吧</a> &gt;我要提问
</div>
<div id="topic_edit_box">
<div id="topic_con" class="fl">
<dl>
<dt>标题</dt>
<dd>
<input type="text" name="title"  class="text" value="" />
</dd>
<dt>类别</dt>
<dd><? if(is_array($asks)) { foreach($asks as $ask) { ?><input type="radio" style="vertical-align:middle;" name="aid" id="cate_<?=$ask['aid']?>" value="<?=$ask['aid']?>"<? if($ask['aid'] == $current_aid) { ?> checked="checked"<? } ?>/><label for="cate_<?=$ask['aid']?>"><?=$ask['name']?></label>
<? } } ?>
</dd>
<dt class="PUB_SHARE_TAG_BOX" style="display:none;">标签</dt>
<dd class="PUB_SHARE_TAG_BOX" style="display:none;">
<input type="text" name="tags"  class="text PUB_SHARE_TAG" value="" title="<?php echo sprintf('最多可以设置%d个标签,标签之间用空格隔开',$_FANWE['setting']['share_tag_count']); ?>"/>
</dd>
<dt>描述</dt>
<dd>
<textarea cols="30" rows="10" name="content" class="PUB_TXT" position="0"></textarea>
</dd>
</dl>
<div id="postOption" class="fl">
<span class="fl">添加：</span>
<a href="javascript:;" class="add_face fl" w="newtpk" onclick="$.Show_Expression(this)">表情</a>
<a href="javascript:;" class="add_goods fl" onclick="$.Goods_Add(this)" w="newtpk">商品</a>
<a href="javascript:;" class="add_pic fl" onclick="$.Pic_Add(this)" w="newtpk">图片</a>
</div>
<div class="pub_out fr">
<input type="checkbox" checked="checked" name="pub_out_check" value="1">
<label for="pub_out_check">同步</label>（<a href="<?php echo FU('settings/bind',array()); ?>" target="_blank">设置</a>）
                    </div>
<div class="clear"></div>
<div class="pub_box clr" style="margin-left: 45px; width: 505px;">
<ul class="pub_img PUB_IMG"></ul>
</div>
<div class="clear"></div>
<div id="topic_new_toolbar">
<input type="submit" class="green_button" value="创  建"/>
<a href="<?php echo FU('ask/forum',array("fid"=>$current_aid)); ?>">取消</a>
<input name="rhash" value="<!--dynamic getRHash-->" type="hidden"/>
<input name="action" value="donewtopic" type="hidden"/>
</div>
</div>
<div id="topic_setting" class="fl">

</div>
</div>
</div>
</form>
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

$("#ask_form").submit(function(){
var title = $.trim(this.title.value);
var aid = $("input[name='aid']:checked").val();
aid = parseInt(aid);
var content = $.trim(this.content.value);

if(title == '')
{
alert(LANG.title_require);
this.title.focus();
return false;
}

if(aid == 0 || isNaN(aid))
{
alert(LANG.cid_require);
return false;
}

if(content == '')
{
alert(LANG.content_require1);
this.content.focus();
return false;
}

$.Pub_Img_Sort(this);
});
});
</script><? include template('inc/footer'); ?>