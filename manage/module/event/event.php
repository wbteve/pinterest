<?php
$result = array('status'=>1,'msg'=>'');
$id = intval($_FANWE['request']['id']);
if($id == 0)
{
	$result['status'] = 0;
	outputJson($result);
}

if(!checkAuthority('event','event'))
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

$event = FDB::fetchFirst("select * from ".FDB::table("event")." where id =".$id);

if(empty($event))
{
	$result['status'] = 0;
	outputJson($result);
}

$result['status'] = 1;
if($album['is_event'] == 0)
{
	$result['msg'] = lang('manage','manage_event_success');
	$event['is_event'] = 1;
}
else
{
	$result['msg'] = lang('manage','manage_unevent_success');
	$event['is_event'] = 0;
}

FDB::query('UPDATE '.FDB::table('event').' SET is_event = '.$event['is_event'].' WHERE id = '.$id);
createManageLog('event','event',$id,$result['msg']);
outputJson($result);
?>