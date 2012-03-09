<?php
function bindCacheLogin()
{
	$list = array();
	$res = FDB::query("SELECT * FROM ".FDB::table('login_module')." WHERE status = 1 AND app_key <> '' AND app_secret <> '' ORDER BY sort ASC,id ASC");
	while($data = FDB::fetch($res))
	{
		$list[$data['code']] = $data;
	}
	
	FanweService::instance()->cache->saveCache('logins', $list);
}
?>