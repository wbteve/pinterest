<?php
$result = array('status'=>1,'msg'=>'');
$id = intval($_FANWE['request']['id']);
if($id == 0)
{
	$result['status'] = 0;
	outputJson($result);
}

if(!checkAuthority('share','share_best'))
{
	$result['status'] = 0;
	outputJson($result);
}

$manage_lock = checkIsManageLock('share',$id);
if($manage_lock !== false)
{
	$result['status'] = 1;
	$result['msg'] = $manage_lock['user_name'].'，'.sprintf(lang('manage','manage_lock'),fToDate($manage_lock['time']));
	outputJson($result);
}

$share = FS("Share")->getShareById($id);
if(empty($share))
{
	$result['status'] = 0;
	outputJson($result);
}

$result['status'] = 1;
if($share['is_best'] == 0)
{
	$result['msg'] = lang('manage','manage_share_best_success');
	$share['is_best'] = 1;
}
else
{
	$result['msg'] = lang('manage','manage_unshare_best_success');
	$share['is_best'] = 0;
}

createManageLog('share','share_best',$id,$result['msg']);
FDB::query('UPDATE '.FDB::table('share').' SET is_best = '.$share['is_best'].' WHERE share_id = '.$id);
outputJson($result);
?>