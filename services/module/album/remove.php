<?php
$aid = (int)$_FANWE['request']['aid'];
if(!$aid)
	exit;

$album = FS("Album")->getAlbumById($aid,false);
if(empty($album) || $album['uid'] != $_FANWE['uid'])
	exit;

FS("Album")->deleteAlbum($aid);
$result['status'] = 1;
outputJson($result);
?>