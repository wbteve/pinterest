<?php
$result = array('status'=>1,'msg'=>'');
$id = intval($_FANWE['request']['id']);
if($id == 0)
{
	$result['status'] = 0;
	outputJson($result);
}

if(!checkAuthority('event','delete'))
{
	$result['status'] = 0;
	outputJson($result);
}

$manage_lock = checkIsManageLock('event',$id);
if($manage_lock !== false)
{
	$result['status'] = 1;
	$result['msg'] = $manage_lock['user_name'].'，'.sprintf(lang('manage','manage_lock'),fToDate($manage_lock['time']));
	outputJson($result);
}

FS('Event')->removeEvent($id);
$result['status'] = 1;
$result['msg'] = lang('manage','manage_delete_success');
createManageLog('event','delete',$id,$result['msg']);
deleteManageLock('event',$id);
outputJson($result);
?>