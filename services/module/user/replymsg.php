<?php
$mlid = intval($_FANWE['request']['lid']);
$message = trim($_FANWE['request']['message']);

if(empty($mlid) || empty($message))
	exit;

$message = cutStr($message,200);
$result['status'] = FS('Message')->replyMsg($mlid,$_FANWE['uid'],$_FANWE['user_name'],$message);
if($result['status'] > 0)
{
	$result['time'] = getBeforeTimelag(TIME_UTC);
	express($message);
	$result['message'] = nl2br($message);
}
outputJson($result);
?>