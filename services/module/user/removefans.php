<?php
if($_FANWE['uid'] == 0)
	exit;

$uid = $_FANWE['request']['uid'];
if($uid == 0)
	exit;

if(!FS('User')->getUserExists($uid))
	exit;

$is_delete = FS('User')->removeFans($uid);

outputJson(array('status'=>$is_delete));
?>