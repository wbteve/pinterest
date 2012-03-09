<?php
$id = intval($_FANWE['request']['id']);
if($id == 0)
	exit;

if(!checkAuthority('second','edit'))
	exit;

$manage_lock = checkIsManageLock('second',$id);
if($manage_lock !== false)
	exit;
	
$sql = "select * from ".FDB::table("second_goods")." where gid = ".$id;
$old = FDB::fetchFirst($sql);
if(empty($old))
{
	deleteManageLock('second',$id);
	exit;
}

$share_id = $old['share_id'];
$update = array(
	'name'=>htmlspecialchars(trim($_FANWE['request']['name'])),
	'content'=>htmlspecialchars(trim($_FANWE['request']['content'])),
	'sid'=>(int)$_FANWE['request']['cid'],
	'city_id'=>(int)$_FANWE['request']['city_id'],
	'num'=> (int)$_FANWE['request']['num'],
	'price'=> (float)$_FANWE['request']['price'],
	'transport_fee'=> (float)$_FANWE['request']['transport_fee'],
	'valid_time'=> str2Time($_FANWE['request']['valid_time']),
);

FDB::update('second_goods',$update,'gid = '.$id);
FS('Share')->updateShare($share_id,$update['title'],$update['content']);
createManageLog('second','edit',$id,lang('manage','manage_edit_success'));
deleteManageLock('second',$id);
$msg = lang('manage','manage_edit_success');
include template('manage/tooltip');
display();
?>