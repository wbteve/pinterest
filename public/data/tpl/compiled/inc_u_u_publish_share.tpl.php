<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<div id="publish_me" class="pub_box">
<form method="post" id="u_zone_form">
<img src="./tpl/images/pub_arrow.png" class="pub_arr">
<div class="pub_area r5">
<div class="pub_edit r5">
<div id="zone_select_album" class="zone_select_album clearfix">
<div class="chose_album">
<a href="javascript:;" class="choose fl slide">选择杂志社</a>
<i class="choose_r fl slide_r"></i>
<a class="cancel" href="javascript:;">取消</a>
</div>
<span class="fw_count"><?php echo sprintf('还可以输入<em class="word_count WORD_COUNT">%s</em>个汉字',"140"); ?></span>
</div>
<textarea name="content" class="PUB_TXT pub_txt fl rl5" length="140" position="0"></textarea>
<input type="button" class="pub_btn fl rr5" value="发表" onclick="$.Share_Save(this)" />
<input type="hidden" name="albumid" class="PUB_ALBUM_ID" value="0" />
<input type="hidden" name="module" value="share" />
<input type="hidden" name="action" value="save" />
<div class="fl pub_tags hide PUB_SHARE_TAG_BOX">
<span class="fl tag_title">标签：</span>
<input type="text" class="fl tag_txt PUB_SHARE_TAG" name="tags" title="<?php echo sprintf('最多可以设置%d个标签,标签之间用空格隔开',$_FANWE['setting']['share_tag_count']); ?>" />
<ul></ul>
</div>
<div class="pub_ext">
<div class="pub_opt fl">
<span class="fl">添加：</span>
<a w="pub" href="javascript:;" onclick="$.Show_Expression(this)" style="background-position:-2px -3px;" class="add_face">表情</a>
<a class="add_goods" w="pub" href="javascript:;" onclick="$.Goods_Add(this)" style="background-position:-2px -74px;">商品</a>
<a class="add_event" w="pub" href="javascript:;" onclick="$.Event_Add(this)" style="background-position:-2px -97px;">话题</a>
<a w="pub" style="background-position:-2px -27px;" href="javascript:;" onclick="$.Pic_Add(this)" class="add_pic">图片</a>
</div>
<div class="pub_out fr">
<input type="checkbox" checked="checked" name="pub_out_check" value="1">
<label for="pub_out_check">同步</label>（<a href="<?php echo FU('settings/bind',array()); ?>" target="_blank">设置</a>）
</div>
</div>
</div>
</div>
<ul class="pub_img PUB_IMG clearfix" style="position:relative;"></ul>
<div class="clear"></div>
</form>
</div>
<div id="zone_album" class="my_album" style="display:none;"><div style="padding:20px; text-align:center;"><img src="./tpl/images/loading.gif" /></div></div>
<script type="text/javascript">
var ALBUM_MAX_PAGE = 2;
var ALBUM_PAGE = 1;
var ALBUM_IS_LOADING = false;
var ALBUM_SELECT_ID = 0;
var ALBUM_LI_MOUSE = false;
jQuery(function($){
$.Pub_Count_Bind($("#u_zone_form .PUB_TXT").get(0));
$('#u_zone_form .PUB_IMG li').live('mousedown',function(){
$('#u_zone_form .PUB_IMG').dragsort();
});

$(".chose_album").click(function(){
if($("#zone_album").css('display') == 'none')
{
var offset = $(this).offset();
$("#zone_album").css({"top":offset.top + 32,"left":offset.left}).show();
getAlbumList();
ALBUM_IS_LOADING = true;
checkPageClickByAlbum(true);
return false;
}
else
{
$("#zone_album").hide();
checkPageClickByAlbum(false);
}
});

$("#zone_album .album_name").live('focus',function(){
var old = this.getAttribute("albumName");
if(this.value == old)
{
this.value = '';
$(this).css('color',"#000");
}
}).live('blur',function(){
var old = this.getAttribute("albumName");
if(this.value == old || this.value == '')
{
this.value = old;
$(this).css('color',"#ccc");
}
});

$("#zone_album .page_slide .left").live('click',function(){
if(ALBUM_PAGE > 1)
{
ALBUM_PAGE--;
$.Get_Album_Page(ALBUM_SELECT_ID,ALBUM_PAGE,8,publishGetAlbumPageHadnler);
}
});

$("#zone_album .page_slide .right").live('click',function(){
if(ALBUM_PAGE < ALBUM_MAX_PAGE)
{
ALBUM_PAGE++;
$.Get_Album_Page(ALBUM_SELECT_ID,ALBUM_PAGE,8,publishGetAlbumPageHadnler);
}
});

$("#zone_album .album_ul li").live('mouseover',function(){
ALBUM_LI_MOUSE = true;
$(this).addClass('checked');
}).live('mouseout',function(){
ALBUM_LI_MOUSE = false;
$(this).removeClass('checked');
}).live('click',function(){
ALBUM_SELECT_ID = this.getAttribute("album");
$("#u_zone_form .PUB_ALBUM_ID").val(ALBUM_SELECT_ID);
$(".chose_album .cancel").show();
$("input",this).attr('checked',true);
$(".chose_album .choose").html($(".m_a",this).html());
$("#zone_album").hide();
checkPageClickByAlbum(false);
});

$("#zone_album .album_cid").live('focus',function(){
checkPageClickByAlbum(false);
}).live('blur',function(){
checkPageClickByAlbum(true);
});

$(".chose_album .cancel").click(function(){
ALBUM_SELECT_ID = 0;
$("#u_zone_form .PUB_ALBUM_ID").val(0);
$(".chose_album .choose").html("选择杂志社");
$(this).hide();
return false;
});
});

function checkPageClickByAlbum(bln)
{
$("body").unbind('click');
if(bln)
{
$("body").bind('click',function(event){
if(!$.getClickIsElement($("#zone_album"),event))
$("#zone_album").hide();
});	
}
}

function publishSaveAlbumHadnler(result)
{
$(".chose_album .choose").html(result.title);
ALBUM_SELECT_ID = result.aid;
ALBUM_PAGE = 1;
$(".chose_album .cancel").show();
$("#u_zone_form .PUB_ALBUM_ID").val(ALBUM_SELECT_ID);
$("#zone_album").hide();
}

function publishGetAlbumPageHadnler(result)
{
ALBUM_MAX_PAGE = result.pager.page_count;
ALBUM_PAGE = result.pager.page;
$("#zone_album .album_ul").html(result.html);
$("#zone_album .cu_page").html(result.pager.page);
$("#zone_album .all_page").html(result.pager.page_count);
}

function getAlbumList()
{
var query = new Object();
query.aid = ALBUM_SELECT_ID;
query.page = ALBUM_PAGE;

$.ajax({
url: SITE_PATH+"services/service.php?m=share&a=selectalbum",
type: "POST",
data:query,
dataType: "json",
success: function(result){
ALBUM_MAX_PAGE = result.pager.page_count;
ALBUM_PAGE = result.pager.page;
$("#zone_album").html(result.html);
}
});
}
</script>