<?php
$user_name = trim($_FANWE['request']['user_name']);
if(empty($user_name))
	exit;

$result = array();
$user = FS('User')->getUsersByName($user_name);
if(empty($user))
{
	$result['status'] = -2;
}
else
{
	if(FS('User')->getIsFollowUId2($user['uid'],$_FANWE['uid']))
	{
		$result['status'] = 1;
		$result['user'] = $user;
	}
	else
	{
		$result['status'] = -1;
	}
}
outputJson($result);
?>