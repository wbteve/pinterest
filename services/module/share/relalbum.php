<?php
$id = (int)$_FANWE['request']['id'];
if($id == 0)
	exit;

$type = $_FANWE['request']['type'];
if(!in_array($type,array('photo','goods')))
	exit;

$rec_obj = array();
switch($type)
{
	case "photo":
		$rec_obj = FDB::fetchFirst('SELECT * FROM '.FDB::table('share_photo').' WHERE photo_id = '.$id);
		if($rec_obj['base_id'] > 0)
		{
			$base_obj = FDB::fetchFirst('SELECT * FROM '.FDB::table('share_photo').' WHERE photo_id = '.$rec_obj['base_id']);
			if($base_obj)
				$rec_obj = $base_obj;
		}
	break;
	
	case "goods":
		$rec_obj = FDB::fetchFirst('SELECT * FROM '.FDB::table('share_goods').' WHERE goods_id = '.$id);
		if($rec_obj['base_id'] > 0)
		{
			$base_obj = FDB::fetchFirst('SELECT * FROM '.FDB::table('share_goods').' WHERE goods_id = '.$rec_obj['base_id']);
			if($base_obj)
				$rec_obj = $base_obj;
		}
	break;
}

if(empty($rec_obj))
	exit;

$rec_img = $rec_obj['img'];
$rec_obj['type'] = $type;
$rec_data = authcode(serialize($rec_obj),'ENCODE');
FanweService::instance()->cache->loadCache('albums');
$album_count = $_FANWE['user']['albums'];
$pager = buildPageMini($album_count,$_FANWE['page'],6);
$album_list = FS("Album")->getAlbumListByUid($_FANWE['uid'],$pager['limit']);

include template('services/share/addalbum');		
display();
?>