<?php
$id = intval($_FANWE['request']['id']);
if($id == 0)
	exit;

if(!checkAuthority('second','edit'))
	exit;

$manage_lock = checkIsManageLock('second',$id);
if($manage_lock === false)
	createManageLock('second',$id);
else
	exit;

$sql = "select * from ".FDB::table("second_goods")." where gid = ".$id;
$goods = FDB::fetchFirst($sql);
if(empty($goods))
{
	deleteManageLock('second',$id);
	exit;
}

$fanwe->cache->loadCache("seconds");
$fanwe->cache->loadCache("citys");
include template('manage/second/edit');
display();
?>