<?php
$user_name = trim($_FANWE['request']['user_name']);
$message = trim($_FANWE['request']['message']);

if(empty($user_name) || empty($message))
	exit;

$user = FS('User')->getUsersByName($user_name);
if(empty($user))
	$result['status'] = -2;
else
{
	if(!FS('User')->getIsFollowUId2($user['uid'],$_FANWE['uid']))
		$result['status'] = -1;
	else
	{
		$message = cutStr($message,200);
		$result['status'] = FS('Message')->sendMsg($_FANWE['uid'],$_FANWE['user_name'],array($user['uid']), '', $message);
	}
}
outputJson($result);
?>