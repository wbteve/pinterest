<?php
$share_id = intval($_FANWE['request']['id']);
if($share_id == 0)
	exit;

$share = FS('Share')->getShareById($share_id);
//没有分享直接退出
if(empty($share))
	exit;

$comments = FS('Share')->getShareComments($share_id,10);
$is_more = false;
if($share['comment_count'] > 10)
{
	$is_more = true;
	$more_count = $share['comment_count'] - 10;
}

$is_remove_comment = FS('Share')->getIsRemoveComment($share);

include template("inc/share/comment_list");
display();
?>