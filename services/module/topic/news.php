<?php
$page = $_FANWE['page'];
if($page == 0)
{
	echo '{"status":0}';
	exit;
}

$size = intval($_FANWE['request']['size']);
if($size == 0 || $size > 50)
	$size = 12;

$begin = ($page * $size) + 4;

$args['best_list'] = array_chunk(FS('Topic')->getImgTopic('best',12,1,0,$begin),6);
foreach($args['best_list'] as $key => $best_item)
{
	$args['best_list'][$key] = array_chunk($best_item,3);
}

$html = tplFetch('inc/club/new_topic_item',$args);
outputJson(array('status'=>1,'html'=>$html));
?>