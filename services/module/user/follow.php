<?php
if($_FANWE['uid'] == 0)
	exit;

$uid = intval($_FANWE['request']['uid']);
if($uid == 0)
	exit;

$type = intval($_FANWE['request']['type']);

if(!FS('User')->getUserExists($uid))
	exit;

if($type == 0)
{
	if(FS('User')->followUser($uid))
		$is_follow = 1;
	else
		$is_follow = 0;
}
elseif($type == 1)
{
	if(!FS('User')->getIsFollowUId($uid))
		FS('User')->followUser($uid);
		
	$is_follow = 1;
}
elseif($type == 2)
{
	if(FS('User')->getIsFollowUId($uid))
		FS('User')->followUser($uid);
	$is_follow = 0;
}

$args = array(
	'is_follow'=>$is_follow
);
$html = tplFetch('services/user/follow',$args);
outputJson(array('status'=>$is_follow,'html'=>$html));
?>