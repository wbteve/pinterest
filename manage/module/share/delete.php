<?php
require fimport('service/share');
$result = array();
$id = intval($_FANWE['request']['id']);

if($id == 0)
	exit;

$share = FS('Share')->getShareById($id);
if(empty($share))
{
	$result['status'] = 1;
	outputJson($result);
}

if(checkAuthority('share','delete'))
	$authoritys = 3;
else
	$authoritys = FS('Share')->getIsEditShare($share);

if($authoritys == 0)
{
	$result['status'] = 0;
	outputJson($result);
}

$type = intval($_FANWE['request']['type']);
if($type == 0)
{
	if($share['type'] == 'bar_post')
		FS('Topic')->deletePost($id);
	elseif($share['type'] == 'ask_post')
		FS('Ask')->deletePost($id);
	elseif($share['type'] == 'album')
		FS('Album')->deleteAlbum($share['rec_id']);
	elseif($share['type'] == 'album_item')
		FS('Album')->deleteAlbumItem($id);
	elseif($share['type'] == 'ershou')
		FS("Second")->deleteGoods($share['rec_id']);
	else
		FS('Share')->deleteShare($id);
	$result['status'] = 1;
}
elseif($type == 1)
{
	if($authoritys < 2)
	{
		$result['status'] = 0;
		outputJson($result);
	}
	
	FDB::query('UPDATE '.FDB::table('share').' SET 
		type = \'default\',
		rec_id = 0,
		parent_id = 0 
		WHERE share_id = '.$id);
	if($share['type'] == 'bar_post')
		FS('Topic')->deletePost($id);
	elseif($share['type'] == 'ask_post')
		FS('Ask')->deletePost($id);
	FS('Share')->deleteShareCache($id);
	$result['status'] = 1;
}

outputJson($result);
?>