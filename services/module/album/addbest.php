<?php
$aid = (int)$_FANWE['request']['albumid'];
if(!$aid)
	exit;

if(empty($_FANWE['request']['content']))
	exit;

$check_result = FS('Share')->checkWord($_FANWE['request']['content'],'content');
if($check_result['error_code'] == 1)
{
	$result['status'] = -1;
	$result['msg_error'] = $check_result['error_msg'];
	outputJson($result);
}

$is_pub = (int)$_FANWE['request']['pub_out_check'];

$result['status'] = FS("Album")->bestAlbum($aid,$_FANWE['uid'],htmlspecialchars($_FANWE['request']['content']),$is_pub);
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