<?php
$id = intval($_FANWE['request']['id']);
if($id == 0)
	exit;

if(!checkAuthority('album','edit'))
	exit;

$manage_lock = checkIsManageLock('album',$id);
if($manage_lock === false)
	createManageLock('album',$id);
else
	exit;

$album = FS('Album')->getAlbumById($id);
if(empty($album))
{
	deleteManageLock('album',$id);
	exit;
}

$fanwe->cache->loadCache("albums");
include template('manage/album/edit');
display();
?>