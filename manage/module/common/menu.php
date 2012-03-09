<?php
$id = (int)$_FANWE['request']['id'];
$module = strtolower(trim($_FANWE['request']['module']));
if($id == 0 || empty($module))
	exit;

$old_module = $module;
if($module == 'dapei')
	$module = 'share';

if(!getIsManage($module))
	exit;

$tpl = 'manage/menu/common';
$manage_lock = checkIsManageLock($module,$id);
switch($module)
{
	case 'share':
		$manage_object = FDB::fetchFirst('SELECT * FROM '.FDB::table('share').' WHERE share_id = '.$id);
	break;

	case 'club':
		$manage_object = FDB::fetchFirst('SELECT * FROM '.FDB::table('forum_thread').' WHERE tid = '.$id);
	break;

	case 'ask':
		$manage_object = FDB::fetchFirst('SELECT * FROM '.FDB::table('ask_thread').' WHERE tid = '.$id);
	break;

	case 'shop':
		$manage_object = FDB::fetchFirst('SELECT * FROM '.FDB::table('shop').' WHERE shop_id = '.$id);
	break;

	case 'event':
		$manage_object = FDB::fetchFirst('SELECT * FROM '.FDB::table('event').' WHERE id = '.$id);
	break;

	case 'daren':
		$manage_object = FDB::fetchFirst('SELECT * FROM '.FDB::table('user_daren').' WHERE id = '.$id);
	break;

	case 'album':
		$manage_object = FDB::fetchFirst('SELECT * FROM '.FDB::table('share').' WHERE share_id = '.$id);
		$rec_object = FDB::fetchFirst('SELECT * FROM '.FDB::table('album').' WHERE id = '.$manage_object['rec_id']);
		$tpl = 'manage/menu/album';
	break;

	case 'second':
		$manage_object = FDB::fetchFirst('SELECT * FROM '.FDB::table('share').' WHERE share_id = '.$id);
		$rec_object = FDB::fetchFirst('SELECT * FROM '.FDB::table('second_goods').' WHERE share_id = '.$id);
		$tpl = 'manage/menu/second';
	break;
}

if(empty($manage_object))
	exit('该项已被删除');

include template($tpl);
display();
?>