<?php
$id = intval($_FANWE['request']['id']);
if($id == 0)
	exit;

if(!checkAuthority('shop','edit'))
	exit;

$manage_lock = checkIsManageLock('shop',$id);
if($manage_lock === false)
	createManageLock('shop',$id);
else
	exit;

$sql = "select * from ".FDB::table("shop")." where shop_id = ".$id;
$shop = FDB::fetchFirst($sql);
if(empty($shop))
{
	deleteManageLock('shop',$id);
	exit;
}

$fanwe->cache->loadCache("shops");

include template('manage/shop/edit');
display();
?>
