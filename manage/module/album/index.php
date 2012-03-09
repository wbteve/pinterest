<?php
$result = array('status'=>1,'msg'=>'');
$id = intval($_FANWE['request']['id']);
if($id == 0)
{
	$result['status'] = 0;
	outputJson($result);
}

if(!checkAuthority('album','index'))
{
	$result['status'] = 0;
	outputJson($result);
}

$manage_lock = checkIsManageLock('album',$id);
if($manage_lock !== false)
{
	$result['status'] = 1;
	$result['msg'] = $manage_lock['user_name'].'，'.sprintf(lang('manage','manage_lock'),fToDate($manage_lock['time']));
	outputJson($result);
}

$album = FS('Album')->getAlbumById($id,false);
if(empty($album))
{
	$result['status'] = 0;
	outputJson($result);
}

$result['status'] = 1;
if($album['is_index'] == 0)
{
	$result['msg'] = lang('manage','manage_index_success');
	$album['is_index'] = 1;
}
else
{
	$result['msg'] = lang('manage','manage_unindex_success');
	$album['is_index'] = 0;
}

createManageLog('album','index',$id,$result['msg']);
FDB::query('UPDATE '.FDB::table('album').' SET is_index = '.$album['is_index'].' WHERE id = '.$id);
outputJson($result);
?>