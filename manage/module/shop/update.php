<?php
$id = intval($_FANWE['request']['shop_id']);
if($id == 0)
	exit;

if(!checkAuthority('shop','edit'))
	exit;

$manage_lock = checkIsManageLock('shop',$id);
if($manage_lock !== false)
	exit;
	
$old = FDB::fetchFirst("select * from ".FDB::table("shop")." where shop_id =".$id);
if(empty($old))
{
	deleteManageLock('shop',$id);
	exit;
}
$shop_name = $_FANWE['shop_name'];
$shop_url = $_FANWE['shop_url'];


$res = FS("Image")->save('shop_logo','shop');
$shop = array(
	'shop_name'=>htmlspecialchars(trim($_FANWE['request']['shop_name'])),
	'shop_url'=>htmlspecialchars(trim($_FANWE['request']['shop_url'])),
	'cate_id'=>(int)$_FANWE['request']['cate_id'],
	'sort'=> (int)$_FANWE['request']['sort'],
);

if($res['url'])
{
	$shop['shop_logo'] = $res['url'];
	if(!empty($old['shop_logo']))
		@unlink(FANWE_ROOT.$old['shop_logo']);
}

FDB::update('shop',$shop,'shop_id = '.$id);
createManageLog('shop','edit',$id,lang('manage','manage_edit_success'));
deleteManageLock('shop',$id);
$msg = lang('manage','manage_edit_success');
include template('manage/tooltip');
display();
?>