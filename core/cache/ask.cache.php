<?php
function bindCacheAsk()
{
	$asks = array();
	$res = FDB::query("SELECT a.* FROM ".FDB::table('ask')." as a 
				WHERE status = 1");
	while($data = FDB::fetch($res))
	{
		$data['url'] = FU('ask/forum',array('aid'=>$data['aid']));
		$asks[$data['aid']] = $data;
	}
	
	FanweService::instance()->cache->saveCache('asks', $asks);
}
?>