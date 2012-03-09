<?php
$aid = (int)$_FANWE['request']['aid'];
if(!$aid)
	exit;

$result['status'] = FS("Album")->bestAlbum($aid,$_FANWE['uid']);
if($result['status'] == -1)
	exit;

$best_count = (int)FDB::resultFirst('SELECT best_count FROM '.FDB::table('album').' WHERE id = '.$aid);
$args = array(
	'album_id'=>&$aid,
	'is_best'=>&$result['status'],
	'best_count'=>&$best_count,
);
$result['html'] = tplFetch('services/album/beststatus',$args);
outputJson($result);
?>