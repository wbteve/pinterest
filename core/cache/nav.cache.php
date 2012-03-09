<?php
function bindCacheNav()
{
	global $_FANWE;
	$list = array();
	$res = FDB::query("SELECT * FROM ".FDB::table('nav_category')." WHERE status = 1 ORDER BY sort ASC,id ASC");
	while($data = FDB::fetch($res))
	{
		$list['all'][$data['id']] = $data;
	}

    $list['root'] = array();
	foreach($list['all'] as $cate)
	{
		if($cate['parent_id'] > 0)
			$list['all'][$cate['parent_id']]['childs'][] = $cate['id'];
		else
			$list['root'][] = $cate['id'];
	}

	$res = FDB::query("SELECT * FROM ".FDB::table('nav')." WHERE status = 1 ORDER BY sort ASC,id ASC");
	while($data = FDB::fetch($res))
	{
		$list['navs'][$data['id']] = $data;
        $list['all'][$data['cid']]['navs'][] = $data['id'];
	}

	FanweService::instance()->cache->saveCache('navs', $list);
}
?>