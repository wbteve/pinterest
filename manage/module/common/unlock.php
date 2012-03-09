<?php
$id = intval($_FANWE['request']['id']);
$module = strtolower(trim($_FANWE['request']['module']));
if($id == 0 || empty($module))
	exit;

if(!getIsManage($module))
	exit;

$lock = getManageLock($module,$id);
if($lock !== false)
{
	if($lock['uid'] == $_FANWE['uid'])
		deleteManageLock($module,$id);
}

echo 1;
?>