<?php
$result = array('status'=>1,'msg'=>'');
$id = intval($_FANWE['request']['id']);
if($id == 0)
{
	$result['status'] = 0;
	outputJson($result);
}

if(!checkAuthority('shop','delete'))
{
	$result['status'] = 0;
	outputJson($result);
}

$manage_lock = checkIsManageLock('shop',$id);
if($manage_lock !== false)
{
	$result['status'] = 1;
	$result['msg'] = $manage_lock['user_name'].'，'.sprintf(lang('manage','manage_lock'),fToDate($manage_lock['time']));
	outputJson($result);
}

$shop = FDB::fetchFirst("select * from ".FDB::table("shop")." where shop_id = ".$id);
if(empty($shop))
{
	$result['status'] = 0;
	outputJson($result);
}

$result['status'] = 1;
$result['msg'] = lang('manage','manage_delete_success');
createManageLog('shop','delete',$id,$result['msg']);
$sql = "delete from ".FDB::table("shop")." where shop_id = ".$id;
FDB::query($sql);
if(!empty($shop['shop_logo']))
	@unlink(FANWE_ROOT.$shop['shop_logo']);
deleteManageLock('shop',$id);
outputJson($result);
?>