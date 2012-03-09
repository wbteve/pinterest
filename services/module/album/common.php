<?php
//专辑全局

//需要检测登陆的操作
$user_checks = array(
	'addbest',
	'getbest',
	'removebest',
	'remove',
);

//检测操作是否需要会员登陆
if(in_array(ACTION_NAME,$user_checks))
{
	if($_FANWE['uid'] == 0)
		exit;
}
?>