<?php
//载入右侧会员模块的函数

/**
 * 展示右侧会员块的模块 
 * 未完成
 * @param $uid
 */
function getSideUserInfoBox($uid = 0)
{
	global $_FANWE;
	if($uid == 0)
		$uid = $_FANWE['uid'];
	$args['user'] = FS('User')->getUserById($uid);
	return tplFetch('inc/home/side_user_info',$args);
}

/**
 * 获取会员的关注栏
 * @param unknown_type $uid
 */
function getSideFocusBox($uid = 0)
{
	global $_FANWE;
	if($uid == 0)
		$uid = $_FANWE['uid'];
	$args['user'] = FS('User')->getUserById($uid);
	return tplFetch('inc/home/side_focus',$args);
}

/**
 * 勋章
 * @param $uid
 */
function getSideMedalBox($uid = 0)
{
	global $_FANWE;
	if($uid == 0)
		$uid = $_FANWE['uid'];
	
	$args['user'] = FS('User')->getUserById($uid);
	return tplFetch('inc/home/side_medal',$args);
}

/**
 * 喜欢的人的关注
 * @param unknown_type $uid
 */
function getSideMyFocusFav($uid = 0)
{
	return tplFetch('inc/side_myfocus_fav'); 
}

/**
 * 喜欢我宝贝的人
 */
function getSideFavUser($uid = 0)
{
	return tplFetch('inc/side_fav_user');  
}

/**
 * 我喜欢了谁的宝贝
 * @param unknown_type $uid
 */
function getSideMyFavUser($uid = 0)
{
	return tplFetch('inc/side_myfav_user');  
}

/**
 * 我感兴趣的会员
 * @param unknown_type $uid
 */
function getInterestUser($uid = 0)
{
	global $_FANWE;
	if($uid == 0)
		$uid = $_FANWE['uid'];
	
	$args['user_list'] = FS('User')->getInterestUser($uid);
	
	return tplFetch('inc/home/side_interest_user',$args);  
}


/**
 * 右侧进入会员空间的模块
 * @param $uid
 */
function getSideSpace($uid = 0)
{
	global $_FANWE;
	if($uid == 0)
		$uid = $_FANWE['uid'];
	$args['user'] = FS('User')->getUserById($uid);
	
	return tplFetch('inc/home/side_space',$args); 
}

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
		'user'=>FS('User')->getUserById($uid)
	);
	
	return tplFetch('inc/u/home_nav',$args);
}

/**
 * 获取会员相关值
 * @param $uid
 */
function getUserAttr($args)
{
	list($uid,$attr) = explode(',',$args);
	unset($args);
	
	$user = FS('User')->getUserById($uid);
	return $user[$attr];
}
?>