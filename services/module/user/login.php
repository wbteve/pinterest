<?php
$cache_file = getTplCache('services/user/login');
if(!@include($cache_file))
{
	$login_modules = getLoginModuleList();
	include template('services/user/login');
}
display($cache_file);
?>