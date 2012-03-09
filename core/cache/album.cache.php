<?php
function bindCacheAlbum()
{
	$list = array();
	$res = FDB::query("SELECT * FROM ".FDB::table('album_category')." WHERE status = 1 ORDER BY sort ASC,id ASC");
	while($data = FDB::fetch($res))
	{
		$data['url'] = FU('album/category',array('id'=>$data['id']));
		$list['category'][$data['id']] = $data;
	}
	
	$res = FDB::query("SELECT name,val FROM ".FDB::table('sys_conf')." WHERE group_id = 8");
	while($data = FDB::fetch($res))
	{
		if($data['name'] == 'ALBUM_DEFAULT_TAGS')
			$data['val'] = unserialize($data['val']);
		
		$data['name'] = strtolower($data['name']);
		$list['setting'][$data['name']] = $data['val'];
	}
	FanweService::instance()->cache->saveCache('albums', $list);
}
?>