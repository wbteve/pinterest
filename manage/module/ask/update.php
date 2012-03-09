<?php
$id = intval($_FANWE['request']['tid']);
if($id == 0)
	exit;

if(!checkAuthority('ask','edit'))
	exit;

$manage_lock = checkIsManageLock('ask',$id);
if($manage_lock !== false)
	exit;
	
$old = FS('Ask')->getTopicById($id);
if(empty($old))
{
	deleteManageLock('ask',$id);
	exit;
}

$share_id = $old['share_id'];
$topic = array(
	'title'=>htmlspecialchars(trim($_FANWE['request']['title'])),
	'content'=>htmlspecialchars(trim($_FANWE['request']['content'])),
	'aid'=>$_FANWE['request']['aid'],
	'is_best'=> isset($_FANWE['request']['is_best']) ? intval($_FANWE['request']['is_best']) : 0,
	'is_top'=>isset($_FANWE['request']['is_top']) ? intval($_FANWE['request']['is_top']) : 0,
);

FDB::update('ask_thread',$topic,'tid = '.$id);
FS('Share')->updateShare($share_id,$topic['title'],$topic['content']);
createManageLog('ask','edit',$id,lang('manage','manage_edit_success'));
deleteManageLock('ask',$id);
$msg = lang('manage','manage_edit_success');
include template('manage/tooltip');
display();
?>