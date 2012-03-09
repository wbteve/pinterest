<?php
function bindCacheGoodscate()
{
	$categorys = array();
	$res = FDB::query("SELECT * FROM ".FDB::table('goods_category')." WHERE status = 1 ORDER BY sort ASC");
	while($data = FDB::fetch($res))
	{
		$tags = array();
		$tres = FDB::query('SELECT gt.* 
			FROM '.FDB::table('goods_category_tags').' AS gct 
			INNER JOIN '.FDB::table('goods_tags').' AS gt ON gt.tag_id = gct.tag_id 
			WHERE gct.cate_id = '.$data['cate_id'].' ORDER BY gt.sort ASC,gt.tag_id ASC');
		while($tag = FDB::fetch($tres))
		{
			$tag['url_tag'] = urlencode($tag['tag_name']);
			$tags[] = $tag;
		}
		FanweService::instance()->cache->saveCache('goods_category_tags_'.$data['cate_id'], $tags);
		$categorys['all'][$data['cate_id']] = $data;
		
		if($data['is_root'] == 1)
			$categorys['root'] = $data['cate_id'];
		elseif($data['parent_id'] == 0)
			$categorys['parent'][] = $data['cate_id'];
		
		if($data['is_index'] == 1)
			$categorys['index'][] = $data['cate_id'];
		
		if(!empty($data['cate_code']))
			$categorys['cate_code'][$data['cate_code']] = $data['cate_id'];
	}
	
	foreach($categorys['all'] as $key => $val)
	{
		if($val['parent_id'] > 0)
			$categorys['all'][$val['parent_id']]['child'][] = $key;
	}
	
	FanweService::instance()->cache->saveCache('goods_category', $categorys);
}
?>