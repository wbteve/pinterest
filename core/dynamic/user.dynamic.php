<?php
/**
 * 获取登陆后的转向
 */
function getUserRefer()
{
	global $_FANWE;
	if(intval($_FANWE['setting']['regresult_to_bind'])==1){
		$refer = FU("settings/bind");
	}
	else{
		$refer = $_FANWE['request']['refer'];
		if(empty($refer))
			$refer = FU('u/index');
	}
	return $refer;
}

function getTipUserFollow($uid)
{
	global $_FANWE;
	
	$is_follow = false;
	if($_FANWE['uid'] > 0 && $_FANWE['uid'] != $uid)
		$is_follow = FS('User')->getIsFollowUId($uid);
		
	$args = array(
		'uid'=>$uid,
		'is_follow'=>$is_follow,
	);
	return tplFetch('services/user/tip_follow',$args);
}
?>