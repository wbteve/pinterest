<? if(!defined('IN_FANWE')) exit('Access Denied'); 
0
|| checkTplRefresh('./tpl/pink2/page/u/u_message.htm', './tpl/pink2/inc/pages.htm', 1331261578, './data/tpl/compiled/page_u_u_message.tpl.php', './tpl/pink2', 'page/u/u_message')
;?>
<?php 
$css_list[0]['url'][] = './tpl/css/zone.css';
$js_list[0] = './tpl/js/zone.js';
 include template('inc/header'); ?><div id="body" class="fm960">
<div class="homens_hd"></div>
<div class="homens_bd clearfix">
<div id="content" class="clearfix" style="width:960px;"><? include template('inc/u/u_menu'); ?><div class="content fr" style="width:820px;">
<div class="zone_head" style="margin:0 20px;">
<div class="mb15">
<h1 class="zone_title"><?=$_FANWE['home_user_names']['short']?>的信件</h1>
<a href="<?php echo FU('u/sendmsg',array("uid"=>$_FANWE['uid'])); ?>" style="font-size:20px;" class="zone_pub">给她写信</a>
</div>
</div>
<? if(empty($msg_list) && empty($sys_msgs) && empty($sys_notices)) { ?>
<div class="empty">
<img alt="" class="fl" src="./tpl/images/fanwe4.png">
<span>呼~~这里还是空的~~</span>
</div> 
<? } else { ?>
<ul class="msg_list clearfix"><? if(is_array($sys_msgs)) { foreach($sys_msgs as $msg) { ?><li class="sys_msg" href="<?php echo FU('u/msgview',array("mid"=>$msg['mid'])); ?>" mid="<?=$msg['mid']?>">
<a class="sm_img"><img src="./tpl/images/pm_pic.gif" /></a>
<div class="inf">
<div class="title">
<label class="fl"><input class="fl msg_mid" type="checkbox" name="mid" value="<?=$msg['mid']?>" /></label>
<p class="fl">
系统消息：
<a href="<?php echo FU('u/msgview',array("mid"=>$msg['mid'])); ?>"><?=$msg['title']?></a>
<? if($msg['status'] == 0) { ?>
<img src="./tpl/images/new_pm_2.gif" />
<? } ?>
</p>
</div>
<div class="time">
<span><?php echo getBeforeTimelag($msg['create_time']); ?></span>
</div>
</div>
</li>
<? } } if(is_array($sys_notices)) { foreach($sys_notices as $notice) { ?><li class="sys_msg" nid="<?=$notice['id']?>">
<a class="sm_img"><img src="./tpl/images/pm_pic.gif" /></a>
<div class="inf">
<div class="title">
<label class="fl"><input class="fl notice_id" type="checkbox" name="nid" value="<?=$notice['id']?>" /></label>
<p class="fl">
系统通知：
<a href="<?php echo FU('u/msgview',array("nid"=>$notice['id'])); ?>"><?=$notice['title']?></a>
<? if($notice['status'] == 0) { ?>
<img src="./tpl/images/new_pm_2.gif" />
<? } ?>
</p>
</div>
<div class="time">
<span><?php echo getBeforeTimelag($notice['create_time']); ?></span>
</div>
<div class="msg">
<?=$notice['content']?>
</div>
</div>
</li>
<? } } if(is_array($msg_list)) { foreach($msg_list as $msg) { ?><li href="<?php echo FU('u/msgview',array("lid"=>$msg['mlid'])); ?>" mlid="<?=$msg['mlid']?>">
<a href="<?=$msg['msg_tuser']['url']?>" class="msg_user"><img src="<?php echo avatar($msg['tuid'],'m',$msg['msg_tuser']['server_code'],1);?>" height="64" /></a>
<div class="inf">
<div class="title">
<label class="fl"><input class="fl msg_mlid" type="checkbox" name="mlid" value="<?=$msg['mlid']?>" /></label>
<p class="fl">
<? if($msg['last_uid'] == $_FANWE['uid']) { ?>
我 对 <a href="<?=$msg['msg_tuser']['url']?>" class="GUID" uid="<?=$msg['tuid']?>"><?=$msg['msg_tuser']['user_name']?></a> 说：
<? } else { ?>
<a href="<?=$msg['msg_tuser']['url']?>" class="GUID" uid="<?=$msg['tuid']?>"><?=$msg['msg_tuser']['user_name']?></a> 对 我 说：
<? } if($msg['is_new'] == 1 && $msg['last_uid'] != $_FANWE['uid']) { ?>
<img src="./tpl/images/new_pm_2.gif" />
<? } ?>
</p>
<span>共 <?=$msg['num']?> 封</span>
</div>
<div class="msg">
<?=$msg['last_msg']?>
</div>
<div class="time">
<span><?=$msg['time']?></span>
<? if($msg['last_uid'] != $_FANWE['uid']) { ?>
&nbsp;&nbsp;<a href="<?php echo FU('u/msgview',array("lid"=>$msg['mlid'])); ?>">回复</a>
<? } ?>
</div>
</div>
</li>
<? } } ?>
</ul>
<div id="msg_page">
<div class="handle">
<label class="fl"><input type="checkbox" id="selectMsgAll" class="fl"/><span class="fl">全选</span></label>
<input type="button" class="remove_msg_btn fl" id="removeMsg" value="删除" />
</div>
<div class="pagination"><? if($pager['page_count'] > 1) { ?>
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
</div>
<? } ?>
</div>
</div>
</div>
<div class="homews_ft"></div>
</div>
<script type="text/javascript">
jQuery(function($){
$(".msg_list li").hover(function(){
$(this).addClass('active');
},function(){
$(this).removeClass('active');
});

$(".msg_list li").click(function(){
var href = this.getAttribute('href');
var fun = function(){
location.href = href;
};
if(href != null && href != '')
setTimeout(fun,1);
});

$(".msg_list li input").click(function(event){
event.stopPropagation();
});

$("#selectMsgAll").change(function(){
if(this.checked)
{
$(".msg_mlid").attr('checked',true);
$(".msg_mid").attr('checked',true);
$(".notice_id").attr('checked',true);
}
else
{
$(".msg_mlid").attr('checked',false);
$(".msg_mid").attr('checked',false);
$(".notice_id").attr('checked',false);
}
});

$("#removeMsg").click(function(){
var mlids = new Array();
$(".msg_mlid:checked").each(function(){
mlids.push(this.value);
});

var mids = new Array();
$(".msg_mid:checked").each(function(){
mids.push(this.value);
});

var nids = new Array();
$(".notice_id:checked").each(function(){
nids.push(this.value);
});

if(mlids.length > 0 || mids.length > 0 || nids.length > 0)
{
$("#removeMsg").attr('disabled',true);
var query = new Object();
query.mlid = mlids.join(",");
query.mid = mids.join(",");
query.nid = nids.join(",");

$.ajax({
url: SITE_PATH+"services/service.php?m=user&a=removemsg",
type: "POST",
data:query,
dataType: "json",
success: function(result){
for(var mlid in result.mlid)
{
if(result.mlid[mlid] > 0)
{
$(".msg_list li[mlid='"+ mlid +"']").remove();	
}
}

for(var mid in result.mid)
{
if(result.mid[mid] > 0)
{
$(".msg_list li[mid='"+ mid +"']").remove();	
}
}

for(var nid in result.nid)
{
if(result.nid[nid] > 0)
{
$(".msg_list li[nid='"+ nid +"']").remove();	
}
}

if($(".msg_list li").length == 0)
{
location.reload(true);
}
},
error:function(){
alert('删除信件失败，请稍候重新删除');
$("#removeMsg").attr('disabled',false);
}
});
}
});
});
</script><? include template('inc/footer'); ?>