<?php
function bindCacheUsercate()
{
	$categorys = array();
	$res = FDB::query("SELECT * FROM ".FDB::table('user_category')." WHERE status = 1");
	while($data = FDB::fetch($res))
	{
		$data['tags'] = FDB::fetchAll('SELECT ut.* 
			FROM '.FDB::table('user_category_tags').' AS uct 
			INNER JOIN '.FDB::table('user_tags').' AS ut ON ut.tag_id = uct.tag_id  
			WHERE uct.cate_id = '.$data['id'].' ORDER BY ut.sort ASC,ut.tag_id ASC');
		
		$categorys[$data['id']] = $data;
	}
	FanweService::instance()->cache->saveCache('usertagcate', $categorys);
}
?>