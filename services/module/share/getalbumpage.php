<?php
$select_aid = (int)$_FANWE['request']['aid'];
$page_size = (int)$_FANWE['request']['size'];
if($page_size == 0)
	$page_size = 8;

if($page_size > 20)
	$page_size = 20;

$album_count = $_FANWE['user']['albums'];
$pager = buildPageMini($album_count,$_FANWE['page'],$page_size);
$list = FS("Album")->getAlbumListByUid($_FANWE['uid'],$pager['limit']);
FanweService::instance()->cache->loadCache('albums');
$args = array(
	'list'=>&$list,
	'pager'=>&$pager,
	'select_aid'=>&$select_aid,
);

$result['html'] = tplFetch('services/share/album_list',$args);
$result['pager'] = &$pager;
outputJson($result);
?>