<?php
$share_id = intval($_FANWE['request']['share_id']);
if($share_id == 0)
	exit;

//未登陆直接退出
$uid = $_FANWE['uid'];
if($uid == 0)
	exit;

$share = FS('Share')->getShareById($share_id);
if(empty($share))
	exit;

if($share['uid'] != $uid)
	exit;

$tags = trim($_FANWE['request']['tags']);
$check_result = FS('Share')->checkWord($tags,'tag');
if($check_result['error_code'] == 1)
{
	$result['status'] = 0;
	$result['error'] = $check_result['error_msg'];
	outputJson($result);
}

$tags = str_replace('***','',$tags);
$tags = str_replace('　',' ',$tags);
$tags = explode(' ',$tags);
$tags = array_unique($tags);
$tags = array_slice($tags,0,$_FANWE['setting']['share_tag_count']);

$share_tags['user'] = trim($_FANWE['request']['tags']);
FS('Share')->updateShareTags($share_id,$share_tags);
$args['tags'] = FS('Share')->getShareTags($share_id);
$args['tags'] = $args['tags']['user'];
FS('Share')->tagsFormat($args['tags']);
$result['tags'] = implode(' ',$tags);
$result['status'] = 1;
$result['html'] = tplFetch("services/share/tag_list",$args);
outputJson($result);
?>