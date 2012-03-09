<?php
$result = array('status'=>1,'msg'=>'');
$id = intval($_FANWE['request']['id']);
if($id == 0)
{
	$result['status'] = 0;
	outputJson($result);
}

if(!checkAuthority('ask','best'))
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
if($topic['is_best'] == 0)
{
	$result['msg'] = lang('manage','manage_best_success');
	$topic['is_best'] = 1;
}
else
{
	$result['msg'] = lang('manage','manage_unbest_success');
	$topic['is_best'] = 0;
}

createManageLog('ask','best',$id,$result['msg']);
FDB::query('UPDATE '.FDB::table('ask_thread').' SET is_best = '.$topic['is_best'].' WHERE tid = '.$id);
outputJson($result);
?>