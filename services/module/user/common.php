<?php
//会员全局

//需要检测登陆的操作
$user_checks = array(
	'avatar',
	'editusertag',
	'follow',
	'follows',
	'removefans',
	'updateusertag',
	'smguser',
	'getfans',
	'sendmsg',
	'bind',
);

//检测操作是否需要会员登陆
if(in_array(ACTION_NAME,$user_checks))
{
	if($_FANWE['uid'] == 0)
		exit;
}
?>