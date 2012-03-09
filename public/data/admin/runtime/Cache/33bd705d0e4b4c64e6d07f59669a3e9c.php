<?php if (!defined('THINK_PATH')) exit();?>
<?php function getTypeName($type)
	{
		return l("SHARE_".strtoupper($type));
	}
	function getShareData($data)
	{
		return l("SHARE_DATA_".strtoupper($data));
	} ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link href="__TMPL__Static/Css/style.css" rel="stylesheet" />
<script type="text/javascript" src="__TMPL__Static/Js/jquery.js"></script>
<script type="text/javascript" src="__TMPL__Static/Js/base.js"></script>
<script type="text/javascript" src="__TMPL__Static/Js/json.js"></script>
<script type="text/javascript" src="__TMPL__Static/Js/jquery.pngFix.js"></script>
<script type="text/javascript">
<!--
//指定当前组模块URL地址 
var URL = '__URL__';
var ROOT_PATH = '__ROOT__';
var APP	 =	 '__APP__';
var STATIC = '__TMPL__Static';
var VAR_MODULE = '<?php echo c('VAR_MODULE');?>';
var VAR_ACTION = '<?php echo c('VAR_ACTION');?>';
var CURR_MODULE = '<?php echo ($module_name); ?>';
var CURR_ACTION = '<?php echo ($action_name); ?>';

//定义JS中使用的语言变量
var CONFIRM_DELETE = '<?php echo L("CONFIRM_DELETE");?>';
var AJAX_LOADING = '<?php echo L("AJAX_LOADING");?>';
var AJAX_ERROR = '<?php echo L("AJAX_ERROR");?>';
var ALREADY_REMOVE = '<?php echo L("ALREADY_REMOVE");?>';
var SEARCH_LOADING = '<?php echo L("SEARCH_LOADING");?>';
var CLICK_EDIT_CONTENT = '<?php echo L("CLICK_EDIT_CONTENT");?>';
//-->
</script>
</head>
<body>
	<div class="fanwe-body">
		<div class="fb-title"><div><p><span><?php echo ($ur_href); ?></span></p></div></div>
		<div class="fb-body">
			<table class="body-table" cellpadding="0" cellspacing="1" border="0">
				<tr>
					<td class="body-table-td">
						<div class="body-table-div">
<script type="text/javascript" src="__TMPL__Static/Js/dataList.js"></script>
<script type="text/javascript" src="__TMPL__Static/Js/shareList.js"></script>
<div class="handle-btns">
	<div class="img-button "><p><input type="button" id="editShare" name="editShare" value="<?php echo L("EDIT");?>" onclick="editData(this,'checkList','share_id')" class="editShare"></p></div>
	<div class="img-button "><p><input type="button" id="removeShare" name="removeShare" value="<?php echo L("REMOVE");?>" onclick="removeData(this,'checkList')" class="removeShare"></p></div>
	<div class="img-button "><p><input type="button" id="ToExamineSelect" name="ToExamineSelect" value="<?php echo L("TO_EXAMINE_SELECT");?>" onclick="ToExamineSelectData(this,'checkList')" class="ToExamineSelect"></p></div>
	<div class="img-button "><p><input type="button" id="ToExamineAll" name="ToExamineAll" value="<?php echo L("TO_EXAMINEALL");?>" onclick="ToExamineAllData()" class="ToExamineAll"></p></div>
	<div class="img-button "><p><input type="button" id="ShiftClass" name="ShiftClass" value="<?php echo L("SHIFT_CLASS");?>" onclick="ShiftClassData(this,'checkList')" class="ShiftClass"></p></div>
	<div class="img-button "><p><input type="button" id="BatchDelete" name="BatchDelete" value="<?php echo L("BATCH_DELETE");?>" onclick="BatchDelete()" class="BatchDelete"></p></div>
</div>
<div class="search-box">
    <form action="__APP__">
		<span><?php echo L("SHARE_CONTENT");?></span>
		<input class="textinput" type="text" value="<?php echo ($keyword); ?>" name="keyword" size="12" />
		<small></small>
		<span><?php echo L("USER_NAME");?></span>
		<input class="textinput" type="text" value="<?php echo ($uname); ?>" name="uname" id="user_name" size="8" />
		<small></small>
		<span><?php echo L("SHARE_TYPE");?></span>
		<select name="type">
			<option value="all" <?php if($type == 'all'): ?>selected="selected"<?php endif; ?> ><?php echo l("SHARE_ALL");?></option>
			<option value="default" <?php if($type == 'default'): ?>selected="selected"<?php endif; ?> ><?php echo l("SHARE_DEFAULT");?></option>
			<option value="ask" <?php if($type == 'ask'): ?>selected="selected"<?php endif; ?> ><?php echo l("SHARE_ASK");?></option>
			<!--<option value="ershou" <?php if($type == 'ershou'): ?>selected="selected"<?php endif; ?> ><?php echo l("SHARE_ERSHOU");?></option>-->
			<option value="fav" <?php if($type == 'fav'): ?>selected="selected"<?php endif; ?> ><?php echo l("SHARE_FAV");?></option>
			<!--<option value="comments" <?php if($type == 'comments'): ?>selected="selected"<?php endif; ?> ><?php echo l("SHARE_COMMENTS");?></option>-->
			<option value="ask_post" <?php if($type == 'ask_post'): ?>selected="selected"<?php endif; ?> ><?php echo l("SHARE_ASK_POST");?></option>
			<option value="bar" <?php if($type == 'bar'): ?>selected="selected"<?php endif; ?> ><?php echo l("SHARE_BAR");?></option>
			<option value="bar_post" <?php if($type == 'bar_post'): ?>selected="selected"<?php endif; ?> ><?php echo l("SHARE_BAR_POST");?></option>
			<option value="album" <?php if($type == 'album'): ?>selected="selected"<?php endif; ?> ><?php echo l("SHARE_ALBUM");?></option>
			<option value="album_item" <?php if($type == 'album_item'): ?>selected="selected"<?php endif; ?> ><?php echo l("SHARE_ALBUM_ITEM");?></option>
			<option value="album_best" <?php if($type == 'album_best'): ?>selected="selected"<?php endif; ?> ><?php echo l("SHARE_ALBUM_BEST");?></option>
		</select>
		<small></small>
		<span><?php echo L("SHARE_DATA");?></span>
		<select name="share_data">
			<option value="default" <?php if($share_data == 'default'): ?>selected="selected"<?php endif; ?> ><?php echo l("SHARE_DATA_DEFAULT");?></option>
			<option value="img" <?php if($share_data == 'img'): ?>selected="selected"<?php endif; ?> ><?php echo l("SHARE_DATA_IMG");?></option>
			<option value="goods" <?php if($share_data == 'goods'): ?>selected="selected"<?php endif; ?> ><?php echo l("SHARE_DATA_GOODS");?></option>
			<option value="photo" <?php if($share_data == 'photo'): ?>selected="selected"<?php endif; ?> ><?php echo l("SHARE_DATA_PHOTO");?></option>
			<option value="goods_photo" <?php if($share_data == 'goods_photo'): ?>selected="selected"<?php endif; ?> ><?php echo l("SHARE_DATA_GOODS_PHOTO");?></option>
			
		</select>
		<small></small>
		<span><?php echo L("SHARE_CATEGORY");?></span>
		<select name="cate_id">
			<option value="0" <?php if($cate_id == 0): ?>selected="selected"<?php endif; ?> ><?php echo l("SHARE_ALL");?></option>
			<option value="-1" <?php if($cate_id == -1): ?>selected="selected"<?php endif; ?> >无分类</option>
			<?php if(is_array($cate_list)): foreach($cate_list as $key=>$cate_item): ?><option value="<?php echo ($cate_item["cate_id"]); ?>" <?php if($cate_id == $cate_item['cate_id']): ?>selected="selected"<?php endif; ?> ><?php echo ($cate_item["cate_name"]); ?></option><?php endforeach; endif; ?>
		</select>
		<small></small>
		<span><?php echo L("STATUS");?></span>
		<select name="status">
			<option value="-1" <?php if($status == -1): ?>selected="selected"<?php endif; ?> ><?php echo L("SHARE_ALL");?></option>
			<option value="0" <?php if($status == 0): ?>selected="selected"<?php endif; ?> ><?php echo L("STATUS_0");?></option>
			<option value="1" <?php if($status == 1): ?>selected="selected"<?php endif; ?> ><?php echo L("STATUS_1");?></option>
		</select>
		<input class="submit_btn" type="submit" value="<?php echo L("SEARCH");?>" />
		<input type="hidden" name="<?php echo c('VAR_MODULE');?>" value="<?php echo ($module_name); ?>" />
		<input type="hidden" name="<?php echo c('VAR_ACTION');?>" value="index" />
	</form>
</div>
<!-- Think 系统列表组件开始 -->
<table id="checkList" class="table-list list" cellpadding="0" cellspacing="0" border="0"><thead><tr><th width="30" class="first"><input type="checkbox" onclick="checkAll('checkList')"></th><th width="50" ><a href="javascript:sortBy('share_id','<?php echo ($sort); ?>','index')" title="按照<?php echo L("ID");?><?php echo ($sortType); ?> "><?php echo L("ID");?><?php if(($order)  ==  "share_id"): ?><img src="__TMPL__Static/Images/<?php echo ($sortImg); ?>.gif" align="absmiddle"><?php endif; ?></a></th><th ><a href="javascript:sortBy('content','<?php echo ($sort); ?>','index')" title="按照<?php echo L("SHARE_CONTENT");?><?php echo ($sortType); ?> "><?php echo L("SHARE_CONTENT");?><?php if(($order)  ==  "content"): ?><img src="__TMPL__Static/Images/<?php echo ($sortImg); ?>.gif" align="absmiddle"><?php endif; ?></a></th><th width="100" ><a href="javascript:sortBy('cate_name','<?php echo ($sort); ?>','index')" title="按照<?php echo L("SHARE_CATEGORY");?><?php echo ($sortType); ?> "><?php echo L("SHARE_CATEGORY");?><?php if(($order)  ==  "cate_name"): ?><img src="__TMPL__Static/Images/<?php echo ($sortImg); ?>.gif" align="absmiddle"><?php endif; ?></a></th><th width="100" ><a href="javascript:sortBy('user_name','<?php echo ($sort); ?>','index')" title="按照<?php echo L("USER_NAME");?><?php echo ($sortType); ?> "><?php echo L("USER_NAME");?><?php if(($order)  ==  "user_name"): ?><img src="__TMPL__Static/Images/<?php echo ($sortImg); ?>.gif" align="absmiddle"><?php endif; ?></a></th><th width="100" ><a href="javascript:sortBy('create_time','<?php echo ($sort); ?>','index')" title="按照<?php echo L("CREATE_TIME");?><?php echo ($sortType); ?> "><?php echo L("CREATE_TIME");?><?php if(($order)  ==  "create_time"): ?><img src="__TMPL__Static/Images/<?php echo ($sortImg); ?>.gif" align="absmiddle"><?php endif; ?></a></th><th width="30" ><a href="javascript:sortBy('collect_count','<?php echo ($sort); ?>','index')" title="按照<?php echo L("COLLECT_COUNT");?><?php echo ($sortType); ?> "><?php echo L("COLLECT_COUNT");?><?php if(($order)  ==  "collect_count"): ?><img src="__TMPL__Static/Images/<?php echo ($sortImg); ?>.gif" align="absmiddle"><?php endif; ?></a></th><th width="30" ><a href="javascript:sortBy('relay_count','<?php echo ($sort); ?>','index')" title="按照<?php echo L("RELAY_COUNT");?><?php echo ($sortType); ?> "><?php echo L("RELAY_COUNT");?><?php if(($order)  ==  "relay_count"): ?><img src="__TMPL__Static/Images/<?php echo ($sortImg); ?>.gif" align="absmiddle"><?php endif; ?></a></th><th width="90" ><a href="javascript:sortBy('comment_count','<?php echo ($sort); ?>','index')" title="按照<?php echo L("COMMENT_COUNT");?><?php echo ($sortType); ?> "><?php echo L("COMMENT_COUNT");?><?php if(($order)  ==  "comment_count"): ?><img src="__TMPL__Static/Images/<?php echo ($sortImg); ?>.gif" align="absmiddle"><?php endif; ?></a></th><th width="90" ><a href="javascript:sortBy('type','<?php echo ($sort); ?>','index')" title="按照<?php echo L("SHARE_TYPE");?><?php echo ($sortType); ?> "><?php echo L("SHARE_TYPE");?><?php if(($order)  ==  "type"): ?><img src="__TMPL__Static/Images/<?php echo ($sortImg); ?>.gif" align="absmiddle"><?php endif; ?></a></th><th width="90" ><a href="javascript:sortBy('share_data','<?php echo ($sort); ?>','index')" title="按照<?php echo L("SHARE_DATA");?><?php echo ($sortType); ?> "><?php echo L("SHARE_DATA");?><?php if(($order)  ==  "share_data"): ?><img src="__TMPL__Static/Images/<?php echo ($sortImg); ?>.gif" align="absmiddle"><?php endif; ?></a></th><th width="60" ><a href="javascript:sortBy('is_index','<?php echo ($sort); ?>','index')" title="按照<?php echo L("IS_INDEX");?><?php echo ($sortType); ?> "><?php echo L("IS_INDEX");?><?php if(($order)  ==  "is_index"): ?><img src="__TMPL__Static/Images/<?php echo ($sortImg); ?>.gif" align="absmiddle"><?php endif; ?></a></th><th width="60" ><a href="javascript:sortBy('status','<?php echo ($sort); ?>','index')" title="按照<?php echo L("STATUS");?><?php echo ($sortType); ?> "><?php echo L("STATUS");?><?php if(($order)  ==  "status"): ?><img src="__TMPL__Static/Images/<?php echo ($sortImg); ?>.gif" align="absmiddle"><?php endif; ?></a></th><th width="80" ><a href="javascript:sortBy('sort','<?php echo ($sort); ?>','index')" title="按照<?php echo L("SORT");?><?php echo ($sortType); ?> "><?php echo L("SORT");?><?php if(($order)  ==  "sort"): ?><img src="__TMPL__Static/Images/<?php echo ($sortImg); ?>.gif" align="absmiddle"><?php endif; ?></a></th><th width="80">操作</th></tr></thead><tbody><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$share): ++$i;$mod = ($i % 2 )?><tr ="<?php echo ($share[""]); ?>" class="<?php if(($mod)  ==  "0"): ?>even<?php endif; ?>"><td class="first"><input type="checkbox" name="key"	value="<?php echo ($share["share_id"]); ?>"></td><td ><?php echo ($share["share_id"]); ?></td><td align="left" ><?php echo ($share["content"]); ?></td><td ><?php echo ($share["cate_name"]); ?></td><td ><?php echo ($share["user_name"]); ?></td><td ><?php echo (toDate($share["create_time"])); ?></td><td ><?php echo ($share["collect_count"]); ?></td><td ><?php echo ($share["relay_count"]); ?></td><td ><?php echo (getCommentCount($share["comment_count"],$share['share_id'])); ?></td><td ><?php echo (getTypeName($share["type"])); ?></td><td ><?php echo (getShareData($share["share_data"])); ?></td><td ><span class="pointer" module="Share" href="javascript:;" onclick="toggleStatus(this,'<?php echo (addslashes($share["share_id"])); ?>','is_index')"><?php echo (getStatusImg($share["is_index"])); ?></span></td><td ><span class="pointer" module="Share" href="javascript:;" onclick="toggleStatus(this,'<?php echo (addslashes($share["share_id"])); ?>','status')"><?php echo (getStatusImg($share["status"])); ?></span></td><td ><span class="pointer" module="Share" href="javascript:;" onclick="numberEdit(this,'<?php echo (addslashes($share["share_id"])); ?>','sort')"><?php echo ($share["sort"]); ?></span></td><td><a href="javascript:;" onclick="editData(this,'<?php echo ($share["share_id"]); ?>','share_id')"><?php echo L("EDIT");?></a>&nbsp;&nbsp;<a href="javascript:;" onclick="removeData(this,'<?php echo ($share["share_id"]); ?>','share_id')"><?php echo L("REMOVE");?></a>&nbsp;&nbsp;</td></tr><?php endforeach; endif; else: echo "" ;endif; ?></tbody></table>
<!-- Think 系统列表组件结束 -->

<div class="pager"><?php echo ($page); ?></div>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="ajax-loading"></div>
</body>
<script type="text/javascript">
jQuery(function($){
	updateBodyDivHeight();
	$(window).resize(function(){
		updateBodyDivHeight();
	});
});

function updateBodyDivHeight()
{
	jQuery(".body-table-div").height(jQuery(".fanwe-body").height() - 36);
	if(jQuery(".body-table-div").get(0).scrollHeight > jQuery(".body-table-div").height())
	{
		var width = jQuery(".body-table-div").width() - 16;
		jQuery(".body-table-div > *").each(function(){
			if(!$(this).hasClass('ajax-loading'))
			{
				$(this).width(width)	
			}
		});
	}
}
</script>
</html>