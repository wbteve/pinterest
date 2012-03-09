<?php
$result = array('status'=>1,'msg'=>'');
$id = intval($_FANWE['request']['id']);
if($id == 0)
{
	$result['status'] = 0;
	outputJson($result);
}

if(!checkAuthority('share','index'))
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
$update = array();
if($share['is_index'] == 0)
{
	$result['msg'] = lang('manage','manage_index_success');
	$update['is_index'] = 1;
}
else
{
	$result['msg'] = lang('manage','manage_unindex_success');
	$update['is_index'] = 0;
	$update['index_img'] = '';
	if(!empty($share['index_img']))
		@unlink(FANWE_ROOT.$share['index_img']);
}

createManageLog('share','index',$id,$result['msg']);
FDB::update('share',$update,'share_id = '.$id);
outputJson($result);
?>