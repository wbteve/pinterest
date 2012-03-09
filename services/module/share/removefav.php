<?php
$share_id = intval($_FANWE['request']['id']);
if($share_id == 0)
	exit;

//未登陆直接退出
$uid = $_FANWE['uid'];
if($uid == 0)
	exit;

$result = array();
if(FS('Share')->deleteShareCollectUser($share_id,$uid))
{
	$result['status'] = 1;
	$result['uid'] = $uid;
	$result['share_id'] = $share_id;
	
	$share_dynamic = FS('Share')->getShareDynamic($share_id);
	$result['count'] = $share_dynamic['collect_count'];
	
	$img_size = intval($_FANWE['request']['size']);
	if($img_size == 0)
		$img_size = 32;
	
	$args = array(
		'collects'=>FS('Share')->getShareCollectUser($share_id,$_FANWE['uid']),
		'img_size'=>$img_size,
	);
	$result['collects'] = tplFetch("inc/share/collect_list",$args);
}
else
{
	$result['status'] = 0;
}
outputJson($result);
?>