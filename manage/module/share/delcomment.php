<?php
$comment_id = intval($_FANWE['request']['comment_id']);
if($comment_id == 0)
	exit;

$share = FDB::fetchFirst('SELECT s.uid,s.share_id  
	FROM '.FDB::table('share_comment').' AS sc 
	INNER JOIN '.FDB::table('share').' AS s ON s.share_id = sc.share_id 
	WHERE sc.comment_id = '.$comment_id);

if(empty($share))
	exit;

$uid = intval($share['uid']);
if($uid != $_FANWE['uid'])
	exit;

FS('Share')->deleteShareComment($comment_id);

$result = array();
$result['status'] = 1;
	
outputJson($result);
?>