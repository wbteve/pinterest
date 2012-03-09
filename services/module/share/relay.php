<?php
$share_id = intval($_FANWE['request']['id']);
if($share_id == 0)
	exit;
	
//未登陆直接退出
$uid = $_FANWE['uid'];
if($uid == 0)
	exit;

$parent_data = FS('Share')->getShareById($share_id);
if(empty($parent_data))
	exit;

$parent_user = FS('User')->getUserCache($parent_data['uid']);
$content = '';
$title = $parent_data['content'];
$is_base = false;
if($parent_data['base_id'] > 0)
{
	$base_data = FS('Share')->getShareById($parent_data['base_id']);
	if(!empty($base_data))
	{
		$is_base = true;
		$base_user = FS('User')->getUserCache($base_data['uid']);
		$title = $base_data['content'];
	}
	
	$content = '//@'.$parent_user['user_name'].':'.$parent_data['content'];
}
include template("services/share/relay");
display();
?>