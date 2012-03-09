<?php
function bindCacheShop()
{
	$list = array();
	$res = FDB::query("SELECT * FROM ".FDB::table('shop_category')." 
		ORDER BY sort ASC,id ASC");
	while($data = FDB::fetch($res))
	{
		$data['url'] = FU('shop/index',array('cid'=>$data['id']));
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
	
	FanweService::instance()->cache->saveCache('shops', $list);
}
?>