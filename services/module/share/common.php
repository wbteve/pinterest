<?php
//分享全局

//需要检测登陆的操作
$user_checks = array(
	'addcomment',
	'addgoods',
	'addrelay',
	'collectgoods',
	'comment',
	'fav',
	'relay',
	'removefav',
	'addpic',
	'uploadpic',
	'selectalbum',
	'savealbum',
	'atme',
	'relalbum',
	'getalbumpage',
);

//检测操作是否需要会员登陆
if(in_array(ACTION_NAME,$user_checks))
{
	if($_FANWE['uid'] == 0)
		exit;
}
?>