<?php
function bindCacheSecond()
{
	$list = array();
	$res = FDB::query("SELECT * FROM ".FDB::table('second')." WHERE status = 1 ORDER BY sort ASC,sid ASC");
	while($data = FDB::fetch($res))
	{
		$list[$data['sid']] = $data;
	}
	FanweService::instance()->cache->saveCache('seconds', $list);
}
?>