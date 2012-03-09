<?php
function bindCacheForum()
{
	$list = array();	
	$res = FDB::query("SELECT * FROM ".FDB::table('forum')." AS f 
		 LEFT JOIN ".FDB::table('forum_fields')." AS ff USING(fid) 
		 WHERE f.status = 1 ORDER BY f.sort ASC");
	
	while($data = FDB::fetch($res))
	{
		$data['url'] = FU('club/forum',array('fid'=>$data['fid']));
		$list['all'][$data['fid']] = $data;
	}
	
	$list['root'] = array();
	foreach($list['all'] as $cate)
	{
		if($cate['parent_id'] > 0)
			$list['all'][$cate['parent_id']]['childs'][] = $cate['fid'];
		else
			$list['root'][] = $cate['fid'];
	}
	
	FanweService::instance()->cache->saveCache('forums', $list);
}
?>