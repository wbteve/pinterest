<?php
function bindCacheCity()
{
	$citys = array();
	$res = FDB::query("SELECT * FROM ".FDB::table('region')." ORDER BY id ASC");
	while($data = FDB::fetch($res))
	{
		if($data['parent_id'] == 0)
			$citys['province'][] = $data['id'];
		elseif($data['parent_id'] > 0)
			$citys['city'][$data['parent_id']][] = $data['id'];
		
		$citys['all'][$data['id']] = $data;
	}
	
	include_once fimport('class/json');
	$json = new JSON();
	writeFile(PUBLIC_ROOT.'./js/city.js','var CITYS = '.$json->encode($citys).';');
	
	FanweService::instance()->cache->saveCache('citys', $citys);
}
?>