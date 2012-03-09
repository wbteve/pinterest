<?php
$aid = (int)$_FANWE['request']['albumid'];
$rec_data = $_FANWE['request']['rec_data'];

if($aid == 0 || empty($rec_data))
	exit;

$rec_obj = unserialize(authcode($rec_data,'DECODE'));
$rec_type = $rec_obj['type'];
$rec_share_id = (int)$rec_obj['share_id'];
$rec_id = 0;
switch($rec_type)
{
	case "photo":
		if((int)$rec_obj['base_id'] > 0)
		{
			$rec_id = (int)$rec_obj['base_id'];
			$rec_share_id = (int)$rec_obj['base_share'];
		}
		else
			$rec_id = (int)$rec_obj['photo_id'];
	break;
	
	case "goods":
		if((int)$rec_obj['base_id'] > 0)
		{
			$rec_id = (int)$rec_obj['base_id'];
			$rec_share_id = (int)$rec_obj['base_share'];
		}
		else
			$rec_id = (int)$rec_obj['goods_id'];
	break;
	
	default:
		exit;
	break;
}

$rec_uid = (int)$rec_obj['uid'];
if($rec_share_id == 0 || $rec_uid == 0 || $rec_id == 0 || $aid == 0)
	exit;

$result['status'] = 0;
$_FANWE['request']['content'] = trim($_FANWE['request']['content']);
if($_FANWE['request']['content'] == lang('template','add_album_default'))
	$_FANWE['request']['content'] = '';

if(!empty($_FANWE['request']['content']))
{
	$check_result = FS('Share')->checkWord($_FANWE['request']['content'],'content');
	if($check_result['error_code'] == 1)
	{
		$result['error_msg'] = $check_result['error_msg'];
		outputJson($result);
	}
}

$album = FS('Album')->getAlbumById($aid,false);
if(!$album || $album['uid'] != $_FANWE['uid'])
	exit;

if(empty($_FANWE['request']['content']))
	$_FANWE['request']['content'] = sprintf(lang('album','rel_album_empty_content'),$album['title']);

$sql = 'SELECT COUNT(ashare_id) FROM '.FDB::table('album_rec')." WHERE album_id = $aid AND share_id = $rec_share_id AND rec_id = $rec_id AND type = '$rec_type'";
if((int)FDB::resultFirst($sql) > 0)
{
	$result['error_msg'] = lang('album','add_rel_album_err');
	outputJson($result);
}

$sql = 'SELECT COUNT(share_id) FROM '.FDB::table('album_share')." WHERE album_id = $aid AND share_id = $rec_share_id";
if((int)FDB::resultFirst($sql) > 0)
{
	$result['error_msg'] = lang('album','add_rel_album_err');
	outputJson($result);
}

$share_data = array();
$share_data['content'] = htmlspecialchars($_FANWE['request']['content']);
$share_data['uid'] = $_FANWE['uid'];
$share_data['rec_id'] = $aid;
$share_data['rec_uid'] = $rec_uid;
$share_data['title'] = addslashes($album['title']);
$share_data['type'] = 'album_item';
$data['share'] = $share_data;

$data['rel_goods'] = array();
$data['rel_photo'] = array();
$base_share = $rec_share_id;
switch($rec_type)
{
	case "photo":
		$data['rel_photo'][] = array(
			'type' => 'default',
			'img' => $rec_obj['img'],
			'sort' => 1,
			'base_id' => $rec_id,
			'base_share' => $rec_share_id,
			'img_width' => $rec_obj['img_width'],
			'img_height' => $rec_obj['img_height'],
			'server_code' => $rec_obj['server_code'],
		);
	break;
	
	case "goods":
		$data['rel_goods'][] = array(
			'name' => addslashes(htmlspecialchars($rec_obj['name'])),
			'url' => $rec_obj['url'],
			'taoke_url' => $rec_obj['taoke_url'],
			'price' => $rec_obj['price'],
			'sort' => 1,
			'shop_id' => $rec_obj['shop_id'],
			'goods_key' => $rec_obj['goods_key'],
			'img' => $rec_obj['img'],
			'base_id' => $rec_id,
			'base_share' => $rec_share_id,
			'img_width' => $rec_obj['img_width'],
			'img_height' => $rec_obj['img_height'],
			'server_code' => $rec_obj['server_code'],
		);
		$data['share_tag'] = FS('Words')->segment($rec_obj['name'],5);
	break;
}
$data['pub_out_check'] = (int)$_FANWE['request']['pub_out_check'];
$share = FS("Share")->save($data);
if($share['status'])
{
	$rec_share_code = FDB::resultFirst('SELECT server_code FROM '.FDB::table('share')." WHERE share_id = $rec_share_id");
	$bln = FDB::query("INSERT INTO ".FDB::table('share_rec')."(share_id,rec_count,server_code) VALUES('$rec_share_id',1,'$rec_share_code')", 'SILENT');
	if(!$bln)
		FDB::query("UPDATE ".FDB::table('share_rec')." SET rec_count = rec_count + 1 WHERE share_id = $rec_share_id");
	
	$album_rec = array();
	$album_rec['album_id'] = $aid;
	$album_rec['ashare_id'] = $share['share_id'];
	$album_rec['share_id'] = $rec_share_id;
	$album_rec['rec_id'] = $rec_id;
	$album_rec['type'] = $rec_type;
	FDB::insert('album_rec',$album_rec);
	
	$album_share = array();
	$album_share['album_id'] = $aid;
	$album_share['share_id'] = $share['share_id'];
	$album_share['cid'] = $album['cid'];
	$album_share['create_day'] = getTodayTime();
	FDB::insert("album_share",$album_share);
	
	FS('Album')->updateAlbumByShare($aid,$share['share_id']);
	FS('Album')->updateAlbum($aid);
	
	$result['status'] = 1;
}
else
{
	$result['status'] = 0;
}
outputJson($result);
?>