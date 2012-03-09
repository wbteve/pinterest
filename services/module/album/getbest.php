<?php
$aid = (int)$_FANWE['request']['id'];
if($aid == 0)
	exit;

$album = FS("Album")->getAlbumById($aid,false);
if(!$album)
	exit;

include template('services/album/getbest');		
display();
?>