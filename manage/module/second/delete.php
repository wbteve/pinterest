<?php
$result = array('status'=>1,'msg'=>'');
$id = intval($_FANWE['request']['id']);
if($id == 0)
{
	$result['status'] = 0;
	outputJson($result);
}

if(!checkAuthority('second','delete'))
{
	$result['status'] = 0;
	outputJson($result);
}

$manage_lock = checkIsManageLock('second',$id);
if($manage_lock !== false)
{
	$result['status'] = 1;
	$result['msg'] = $manage_lock['user_name'].'，'.sprintf(lang('manage','manage_lock'),fToDate($manage_lock['time']));
	outputJson($result);
}

$sql = "select * from ".FDB::table("second_goods")." where gid = ".$id;
$goods = FDB::fetchFirst($sql);
if(empty($goods))
{
	$result['status'] = 0;
	outputJson($result);
}

$result['status'] = 1;
$result['msg'] = lang('manage','manage_delete_success');
createManageLog('second','delete',$id,$result['msg']);
FS('Second')->deleteGoods($id);
deleteManageLock('second',$id);
outputJson($result);
?>