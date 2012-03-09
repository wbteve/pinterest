<?php
if($_FANWE['uid'] == 0)
	exit;

$fanwe->cache->loadCache('usertagcate');

$user_tags = FS('User')->getUserTags($_FANWE['uid']);

include template('page/user_forgetpassword');
?>