<?php
function bindCacheIpbanned()
{
	FDB::query("DELETE FROM ".FDB::table('ip_banned')." WHERE expiration<'".TIME_UTC."'");
	$data = array();
	$query = FDB::query("SELECT ip1, ip2, ip3, ip4, expiration FROM ".FDB::table('ip_banned'));

	if(FDB::numRows($query))
	{
		$data['expiration'] = 0;
		$data['regexp'] = $separator = '';
	}
	
	while($banned = FDB::fetch($query))
	{
		$data['expiration'] = !$data['expiration'] || $banned['expiration'] < $data['expiration'] ? $banned['expiration'] : $data['expiration'];
		$data['regexp'] .= $separator.
			($banned['ip1'] == '-1' ? '\\d+\\.' : $banned['ip1'].'\\.').
			($banned['ip2'] == '-1' ? '\\d+\\.' : $banned['ip2'].'\\.').
			($banned['ip3'] == '-1' ? '\\d+\\.' : $banned['ip3'].'\\.').
			($banned['ip4'] == '-1' ? '\\d+' : $banned['ip4']);
		$separator = '|';
	}

	FanweService::instance()->cache->saveCache('ipbanned', $data);
}
?>