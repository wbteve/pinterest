<?php
$share_id = intval($_FANWE['request']['share_id']);
if($share_id == 0)
	exit;

//未登陆直接退出
$uid = $_FANWE['uid'];
if($uid == 0)
	exit;

$share = FS('Share')->getShareById($share_id);
//没有分享直接退出
if(empty($share))
	exit;
	
$result = array('status'=>1,'html'=>'','error'=>'');
if(!checkIpOperation("add_comment",SHARE_INTERVAL_TIME))
{
	$result['status'] = 0;
	$result['error'] = lang('share','interval_tips');
	outputJson($result);
}

$check_result = FS('Share')->checkWord($_FANWE['request']['content'],'content');
if($check_result['error_code'] == 1)
{
	$result['status'] = 0;
	$result['error'] = $check_result['error_msg'];
	outputJson($result);
}

$comment_id = FS('Share')->saveComment($_FANWE['request']);
//FS("User")->updateUserScore($_FANWE['uid'],'share','comments',$_FANWE['request']['content'],$comment_id);

$comment = FS('Share')->getShareComment($comment_id);
$comment['user'] = array('uid'=>$_FANWE['uid'],'user_name'=>$_FANWE['user_name'],'url'=>FU('u/index',array('uid'=>$_FANWE['uid'])));
$comment['time'] = getBeforeTimelag($comment['create_time']);
$is_remove_comment = FS('Share')->getIsRemoveComment($share);

$args = array(
	'comment'=>$comment,
	'is_remove_comment'=>$is_remove_comment,
);

if($_FANWE['request']['comment_type'] == 'album')
	$result['html'] = tplFetch('services/album/comment_item',$args);
else
	$result['html'] = tplFetch('inc/share/comment_item',$args);
outputJson($result);
?>