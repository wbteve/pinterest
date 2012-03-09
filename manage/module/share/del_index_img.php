<?php
$result = array();
$share_id = intval($_FANWE['request']['share_id']);

if($share_id == 0)
{
	$result['isErr'] = 1;
	outputJson($result);
	exit;
}

if(!checkAuthority('share','is_index'))
{
	$result['isErr'] = 1;
	outputJson($result);
	exit;
}

$old_index_img = FDB::resultFirst('SELECT index_img FROM '.FDB::table("share").' where share_id='.$share_id);

if(!empty($old_index_img))
	@unlink(FANWE_ROOT.$old_index_img);
	
if(FDB::query("UPDATE ".FDB::table("share")." set index_img='' "))
{
	$result['isErr'] = 0;
	outputJson($result);
	exit;
}

?>
