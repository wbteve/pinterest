<?php
function bindCacheBusiness()
{
	$list = array();
	$res = FDB::query("SELECT * FROM ".FDB::table('sharegoods_module')." 
		WHERE status = 1 ORDER BY sort ASC,id ASC");
	while($data = FDB::fetch($res))
	{
		if($api_data  = @unserialize($data['api_data']))
		{
			$data = array_merge($data,$api_data);
		}
		unset($data['api_data']);
		$list[$data['class']] = $data;
	}
	FanweService::instance()->cache->saveCache('business', $list);
}
?>