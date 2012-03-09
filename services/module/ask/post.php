<?php
$tid = intval($_FANWE['request']['tid']);
if($tid == 0)
	exit;

$topic = FS('Ask')->getTopicById($tid);
if(empty($topic))
	exit;

$_FANWE['request']['uid'] = $_FANWE['uid'];
$_FANWE['request']['rec_id'] = $tid;
$_FANWE['request']['parent_id'] = $topic['share_id'];
$_FANWE['request']['type'] = 'ask_post';
$_FANWE['request']['title'] = addslashes($topic['title']);

$result = array();
if(!checkIpOperation("add_share",SHARE_INTERVAL_TIME))
{
	$result['status'] = 0;
	$result['error_msg'] = lang('share','interval_tips');
	outputJson($result);
}

$share = FS('Share')->submit(&$_FANWE['request']);
if($share['status'])
{
	$content = htmlspecialchars(trim($_FANWE['request']['content']));
	$post_id = (int)FS('Ask')->saveTopicPost($tid,$content,$share['share_id']);
	
	$result['status'] = 1;
	$list[] = FS('Share')->getShareById($share['share_id']);
	$list = FS('Share')->getShareDetailList($list,true,true,true);
	$args = array(
		'share_item'=>current($list),
		'current_share_id'=>$topic['share_id']
	);
	$result['html'] = tplFetch('services/share/share_item',$args);
}
else
{
	$result['status'] = 0;
	$result['error_code'] = $share['error_code'];
	$result['error_msg'] = $share['error_msg'];
}

outputJson($result);
?>