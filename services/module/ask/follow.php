<?php
if($_FANWE['uid'] == 0)
	exit;

$tid = $_FANWE['request']['tid'];
if($tid == 0)
	exit;

$is_follow = FS('Ask')->followTopic($tid);
$args['follow_count'] = FS('Ask')->getTopicFollowCount($tid);
$args['follow_users'] = FS('Ask')->getTopicFollows($tid,9);
$args['topic'] = FS('Ask')->getTopicById($tid);

$html = tplFetch('inc/ask/follow_user',$args);
outputJson(array('status'=>$is_follow,'html'=>$html));
?>