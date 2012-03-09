<?php
$share_id = intval($_FANWE['request']['id']);

if($share_id == 0)
	exit;

if(!checkAuthority('share','edit'))
	exit;

$share = FS("Share")->getShareById($share_id);
if(empty($share))
{
	deleteManageLock('share',$share_id);
	exit;
}

$manage_lock = checkIsManageLock('share',$share_id);
if($manage_lock === false)
	createManageLock('share',$share_id);
else
	exit;

$share['share_tags'] = FDB::resultFirst("select group_concat(tag_name SEPARATOR ' ') from ".FDB::table("share_tags")." where share_id = ".$share['share_id']);
$share_category = FDB::fetchAll("select c.cate_id,c.cate_name from ".FDB::table("share_category")." as sc left join ".FDB::table("goods_category")." as c on sc.cate_id = c.cate_id where sc.share_id = ".$share['share_id']);

$fanwe->cache->loadCache("goods_category");
include template('manage/share/edit');
display();
?>
