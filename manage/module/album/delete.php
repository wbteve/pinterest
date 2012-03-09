<?php
$result = array('status'=>1,'msg'=>'');
$id = intval($_FANWE['request']['id']);
if($id == 0)
{
	$result['status'] = 0;
	outputJson($result);
}

if(!checkAuthority('album','delete'))
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
$result['msg'] = lang('manage','manage_delete_success');
createManageLog('album','delete',$id,$result['msg']);

@set_time_limit(3600);
if(function_exists('ini_set'))
{
	ini_set('max_execution_time',3600);
	ini_set("memory_limit","256M");
}

FS('Album')->deleteAlbum($id,true);
deleteManageLock('album',$id);
outputJson($result);
?>