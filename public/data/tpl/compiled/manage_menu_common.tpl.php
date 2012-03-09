<? if(!defined('IN_FANWE')) exit('Access Denied'); ?>
<div class="manage_menu">
<strong>操作</strong>
<? if($manage_lock === false) { if(is_array($_FANWE['authoritys'][$module])) { foreach($_FANWE['authoritys'][$module] as $action => $action_val) { ?><?php 
$action_lang = lang('template','manage_'.$action);
 if($action == 'edit') { ?>
<span class="mm_jg">&nbsp;|&nbsp;</span>
<a class="mm_action manage_<?=$action?>" target="_blank" href="<?=$_FANWE['site_root']?>manage/manage.php?m=<?=$module?>&a=<?=$action?>&id=<?=$id?>"><?=$action_lang?></a>
<? } elseif($action == 'delete') { ?>
<span class="mm_jg">&nbsp;|&nbsp;</span>
<a class="mm_action" href="javascript:;" onClick="if(confirm('你确定要删除此项吗？')){$.ManageHandler('<?=$module?>','<?=$action?>',<?=$id?>,$(this).parent().parent());}"><?=$action_lang?></a>
<? } elseif($action == 'share_best') { if($old_module == 'dapei') { ?>
<?php 
$action_lang = lang('template','manage_'.$action.'_'.$manage_object['is_best']);
 ?>
<span class="mm_jg">&nbsp;|&nbsp;</span>
<a class="mm_action" href="javascript:;" onClick="$.ManageHandler('<?=$module?>','<?=$action?>',<?=$id?>,this)"><?=$action_lang?></a>
<? } } else { ?>
<?php 
switch($action)
{
case 'best';
$action_lang = lang('template','manage_'.$action.'_'.$manage_object['is_best']);
break;

case 'top';
$action_lang = lang('template','manage_'.$action.'_'.$manage_object['is_top']);
break;

case 'hot';
$action_lang = lang('template','manage_'.$action.'_'.$manage_object['is_hot']);
break;

case 'index';
$action_lang = lang('template','manage_'.$action.'_'.$manage_object['is_index']);
break;

case 'status';
$action_lang = lang('template','manage_'.$action.'_'.$manage_object['status']);
break;

case 'event';
$action_lang = lang('template','manage_'.$action.'_'.$manage_object['is_event']);
break;
}
 ?>
<span class="mm_jg">&nbsp;|&nbsp;</span>
<a class="mm_action" href="javascript:;" onClick="$.ManageHandler('<?=$module?>','<?=$action?>',<?=$id?>,this)"><?=$action_lang?></a>
<? } } } } else { ?>
<span class="manage_lock">
<a href="<?php echo FU('u/index',array("uid"=>$manage_lock['uid'])); ?>" target="_blank"><?=$manage_lock['user_name']?></a>，<? echo sprintf(lang('manage','manage_lock'),fToDate($manage_lock['time'])); ?></span>
<? } ?>
</div>