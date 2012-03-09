<?php
$id = intval($_FANWE['request']['id']);
$module = strtolower(trim($_FANWE['request']['module']));
if($id == 0 || empty($module))
	exit;

if(!getIsManage($module)&&FDB::resultFirst("select uid from ".FDB::table("share")." where share_id = ".$id)!=$_FANWE['uid'])
	exit;

if($module=='share'&&FDB::resultFirst("select uid from ".FDB::table("share")." where share_id = ".$id)==$_FANWE['uid'])	
{
	$_FANWE['authoritys'][$module]=array("edit"=>1,"delete"=>1);
}

$manage_lock = checkIsManageLock($module,$id);
include template('manage/slide_menu');
display();
?>