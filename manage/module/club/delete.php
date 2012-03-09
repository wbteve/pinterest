<?php
$result = array('status'=>1,'msg'=>'');
$id = intval($_FANWE['request']['id']);
if($id == 0)
{
	$result['status'] = 0;
	outputJson($result);
}

if(!checkAuthority('club','delete'))
{
	$result['status'] = 0;
	outputJson($result);
}

$manage_lock = checkIsManageLock('club',$id);
if($manage_lock !== false)
{
	$result['status'] = 1;
	$result['msg'] = $manage_lock['user_name'].'，'.sprintf(lang('manage','manage_lock'),fToDate($manage_lock['time']));
	outputJson($result);
}

$topic = FS('Topic')->getTopicById($id);
if(empty($topic))
{
	$result['status'] = 0;
	outputJson($result);
}

$result['status'] = 1;
$result['msg'] = lang('manage','manage_delete_success');
createManageLog('club','delete',$id,$result['msg']);
FS('Topic')->deleteTopic($topic['share_id']);
deleteManageLock('club',$id);
outputJson($result);
?>