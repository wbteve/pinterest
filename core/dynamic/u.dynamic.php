<?php
/**
 * 右侧会员空间导航模块
 * @param $uid
 */
function getHomeNav($args)
{
	list($uid,$active) = explode(',',$args);
	unset($args);
	$args = array(
		'uid'=>$uid,
		'active'=>$active,
		'user'=>FS('User')->getUserById($uid),
		'user_show_name'=>FS('User')->getUserShowName($uid)
	);
	
	return tplFetch('inc/u/home_nav',$args);
}
?>