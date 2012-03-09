<?php
$result = array('status'=>1,'msg'=>'');
$id = intval($_FANWE['request']['id']);
if($id == 0)
{
	$result['status'] = 0;
	outputJson($result);
}

if(!checkAuthority('daren','index'))
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

$daren = FDB::fetchFirst("select * from ".FDB::table("user_daren")." where id =".$id);
if(empty($daren))
{
	$result['status'] = 0;
	outputJson($result);
}

$result['status'] = 1;
if($daren['is_index'] == 0)
{
	$result['msg'] = lang('manage','manage_index_success');
	$daren['is_index'] = 1;
}
else
{
	$result['msg'] = lang('manage','manage_unindex_success');
	$daren['is_index'] = 0;
}

createManageLog('daren','index',$id,$result['msg']);
FDB::query('UPDATE '.FDB::table('user_daren').' SET is_index = '.$daren['is_index'].' WHERE id = '.$id);
outputJson($result);
?>