<?php
$result = array('status'=>1,'msg'=>'');
$id = intval($_FANWE['request']['id']);
if($id == 0)
{
	$result['status'] = 0;
	outputJson($result);
}

if(!checkAuthority('daren','is_daren'))
{
	$result['status'] = 0;
	outputJson($result);
}

$manage_lock = checkIsManageLock('daren',$id);
if($manage_lock !== false)
{
	$result['status'] = 1;
	$result['msg'] = $manage_lock['user_name'].'，'.sprintf(lang('manage','manage_lock'),fToDate($manage_lock['time']));
	outputJson($result);
}

$daren = FDB::fetchFirst("select * from ".FDB::table("user")." where is_daren = 1 and uid =".$id);
if(empty($daren))
{
	$result['status'] = 0;
	outputJson($result);
}


if(FS("Daren")->removeDaren($id))
{
	$result['status'] = 1;
}
else
{
	$result['status'] = 0;
	outputJson($result);
}


$result['msg'] = lang('manage','manage_is_daren_success');

createManageLog('daren','is_daren',$id,$result['msg']);

outputJson($result);
?>