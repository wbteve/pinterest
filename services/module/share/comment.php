<?php
$share_id = intval($_FANWE['request']['id']);
if($share_id == 0)
	exit;
	
//未登陆直接退出
$uid = $_FANWE['uid'];
if($uid == 0)
	exit;

$share_data = FS('Share')->getShareById($share_id);
if(empty($share_data))
	exit;
$is_remove_comment = FS('Share')->getIsRemoveComment($share_data);
include template("services/share/comment_add");
display();
?>