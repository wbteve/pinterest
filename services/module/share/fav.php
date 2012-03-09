<?php
$share_id = intval($_FANWE['request']['id']);
if($share_id == 0)
	exit;

//未登陆直接退出
$uid = $_FANWE['uid'];
if($uid == 0)
	exit;
	
$share = FS('Share')->getShareById($share_id);
//没有分享直接退出
if(empty($share))
	exit;

$result = array();
if($share['uid'] == $uid)
{
	$result['status'] = 3; 
	outputJson($result);
}

if(FS('Share')->getIsCollectByUid($share_id,$uid))
{
	$result['status'] = 2;
	outputJson($result);
}

FS('Share')->saveFav($share);

$share_dynamic = FS('Share')->getShareDynamic($share_id);
$result['count'] = $share_dynamic['collect_count'];

$result['status'] = 4;
$img_size = intval($_FANWE['request']['size']);
if($img_size == 0)
	$img_size = 12;

$user_list = FS('Share')->getShareCollectUser($share_id,$_FANWE['uid'],$img_size);
$args = array(
	'collects'=>$user_list,
	'img_size'=>32,
);

$result['collects'] = tplFetch("inc/share/collect_list",$args);
outputJson($result);
?>