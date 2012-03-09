<?php
$result = array('status'=>1,'msg'=>'');
$id = intval($_FANWE['request']['id']);
if($id == 0)
{
	$result['status'] = 0;
	outputJson($result);
}

if(!checkAuthority('club','top'))
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
if($topic['is_top'] == 0)
{
	$result['msg'] = lang('manage','manage_top_success');
	$topic['is_top'] = 1;
}
else
{
	$result['msg'] = lang('manage','manage_untop_success');
	$topic['is_top'] = 0;
}

FDB::query('UPDATE '.FDB::table('forum_thread').' SET is_top = '.$topic['is_top'].' WHERE tid = '.$id);

createManageLog('club','top',$id,$result['msg']);
outputJson($result);
?>