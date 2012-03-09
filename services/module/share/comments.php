<?php
$share_id = intval($_FANWE['request']['share_id']);
if($share_id == 0)
	exit;

$share = FS('Share')->getShareById($share_id);
//没有分享直接退出
if(empty($share))
	exit;

$dynamic = FS('Share')->getShareDynamic($share_id);
$share = array_merge($share,$dynamic);

$pager = buildPage('',array(),$share['comment_count'],$_FANWE['page'],10);
$comments = FS('Share')->getShareCommentList($share_id,$pager['limit']);
$is_remove_comment = FS('Share')->getIsRemoveComment($share);

include template("inc/share/comments");
display();
?>