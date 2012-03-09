<?php
$result = array('status'=>1,'msg'=>'');
$id = intval($_FANWE['request']['id']);
if($id == 0)
{
	$result['status'] = 0;
	outputJson($result);
}

if(!checkAuthority('ask','delete'))
{
	$result['status'] = 0;
	outputJson($result);
}

$manage_lock = checkIsManageLock('ask',$id);
if($manage_lock !== false)
{
	$result['status'] = 1;
	$result['msg'] = $manage_lock['user_name'].'，'.sprintf(lang('manage','manage_lock'),fToDate($manage_lock['time']));
	outputJson($result);
}

$topic = FS('Ask')->getTopicById($id);
if(empty($topic))
{
	$result['status'] = 0;
	outputJson($result);
}

$result['status'] = 1;
$result['msg'] = lang('manage','manage_delete_success');
createManageLog('ask','delete',$id,$result['msg']);
FS('Ask')->deleteTopic($id);
deleteManageLock('ask',$id);
outputJson($result);
?>