<?php
$select_aid = (int)$_FANWE['request']['aid'];
$album_count = $_FANWE['user']['albums'];
$pager = buildPageMini($album_count,$_FANWE['page'],8);
$list = FS("Album")->getAlbumListByUid($_FANWE['uid'],$pager['limit']);
FanweService::instance()->cache->loadCache('albums');
$args = array(
	'list'=>&$list,
	'pager'=>&$pager,
	'select_aid'=>&$select_aid,
);

$result['html'] = tplFetch('services/share/select_album',$args);
$result['pager'] = &$pager;
outputJson($result);
?>