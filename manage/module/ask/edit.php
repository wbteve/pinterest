<?php
$id = intval($_FANWE['request']['id']);
if($id == 0)
	exit;

if(!checkAuthority('ask','edit'))
	exit;

$manage_lock = checkIsManageLock('ask',$id);
if($manage_lock === false)
	createManageLock('ask',$id);
else
	exit;

$topic = FS('Ask')->getTopicById($id);
if(empty($topic))
{
	deleteManageLock('ask',$id);
	exit;
}

$fanwe->cache->loadCache("asks");
include template('manage/ask/edit');
display();
?>
