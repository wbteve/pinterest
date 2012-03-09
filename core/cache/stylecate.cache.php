<?php
function bindCacheStylecate()
{
	$categorys = array();
	$res = FDB::query("SELECT * FROM ".FDB::table('style_category')." WHERE status = 1 ORDER BY sort ASC");
	while($data = FDB::fetch($res))
	{
		$tags = array();
		$tres = FDB::query('SELECT gt.* 
			FROM '.FDB::table('style_category_tags').' AS gct 
			INNER JOIN '.FDB::table('goods_tags').' AS gt ON gt.tag_id = gct.tag_id 
			WHERE gct.cate_id = '.$data['cate_id'].' ORDER BY gct.sort ASC');
		while($tag = FDB::fetch($tres))
		{
			$tag['url_tag'] = urlencode($tag['tag_name']);
			$tags[] = $tag;
		}
		FanweService::instance()->cache->saveCache('style_category_tags_'.$data['cate_id'], $tags);
		$categorys['all'][$data['cate_id']] = $data;
	}
	
	foreach($categorys['all'] as $key => $val)
	{
		if($val['parent_id'] > 0)
			$categorys['all'][$val['parent_id']]['child'][] = $key;
	}
	
	FanweService::instance()->cache->saveCache('style_category', $categorys);
}
?>