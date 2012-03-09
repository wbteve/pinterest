<?php
if($_FANWE['uid'] == 0)
	exit;

$tid = $_FANWE['request']['tid'];
if($tid == 0)
	exit;

$is_follow = FS('Topic')->followTopic($tid);
$args['follow_count'] = FS('Topic')->getTopicFollowCount($tid);
$args['follow_users'] = FS('Topic')->getTopicFollows($tid,9);
$args['topic'] = FS('Topic')->getTopicById($tid);

$html = tplFetch('inc/club/follow_user',$args);
outputJson(array('status'=>$is_follow,'html'=>$html));
?>