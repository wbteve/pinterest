<?php
$result = array('status'=>1,'msg'=>'');
$id = intval($_FANWE['request']['id']);
if($id == 0)
{
	$result['status'] = 0;
	outputJson($result);
}

if(!checkAuthority('album','best'))
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
if($album['is_best'] == 0)
{
	$result['msg'] = lang('manage','manage_best_success');
	$album['is_best'] = 1;
}
else
{
	$result['msg'] = lang('manage','manage_unbest_success');
	$album['is_best'] = 0;
}

createManageLog('album','best',$id,$result['msg']);
FDB::query('UPDATE '.FDB::table('album').' SET is_best = '.$album['is_best'].' WHERE id = '.$id);
outputJson($result);
?>