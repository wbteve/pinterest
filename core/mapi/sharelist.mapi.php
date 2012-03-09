<?php
class sharelistMapi
{
	public function run()
	{
		global $_FANWE;	
		
		$root = array();
		$root['return'] = 1;

		$is_hot = (int)$_FANWE['requestData']['is_hot'];
		$is_new = (int)$_FANWE['requestData']['is_new'];
		$page = (int)$_FANWE['requestData']['page'];
		$page = max(1,$page);
		
		$today_time = getTodayTime();
		$field = '';
		$whrer = '';
		
		$book_photo_goods = (int)$_FANWE['setting']['book_photo_goods'];
		if($book_photo_goods == 0)
			$whrer = " WHERE share_data IN ('goods','photo','goods_photo')";
		elseif($book_photo_goods == 1)
			$whrer = " WHERE share_data IN ('photo','goods_photo')";
		elseif($book_photo_goods == 2)
			$whrer = " WHERE share_data IN ('goods','goods_photo')";

		if($is_hot == 1)
		{
			$day7_time = $today_time - 604800;
			$field = ",(create_time > $day7_time) AS time_sort ";
			$sort = " ORDER BY time_sort DESC,collect_count DESC";
		}

		if($is_new == 1)
			$sort = " ORDER BY share_id DESC";
				
		$sql_count = "SELECT COUNT(DISTINCT share_id) FROM ".FDB::table("share");
		$total = FDB::resultFirst($sql_count);
		$page_size = PAGE_SIZE;
		$max_page = 100;
		if($total > $max_page * $page_size)
			$total = $max_page * $page_size;

		if($page > $max_page)
			$page = $max_page;
		
		$page_total = ceil($total/$page_size);
		$limit = (($page - 1) * $page_size).",".$page_size;
		$sql = 'SELECT DISTINCT(share_id),cache_data '.$field.'
					FROM '.FDB::table('share').$whrer.$sort.' LIMIT '.$limit;
		$res = FDB::query($sql);
		$share_list = array();
		while($item = FDB::fetch($res))
		{
			$cache_data = fStripslashes(unserialize($item['cache_data']));
			$img = current($cache_data['imgs']['all']);
			$data = array();
			$data['share_id'] = $item['share_id'];
			$data['img'] = getImgName($img['img'],100,999,0,true);
			$data['height'] = $img['height'] * (100 / $img['width']);
			$share_list[] = $data;
		}

		$root['item'] = $share_list;
		$root['page'] = array("page"=>$page,"page_total"=>$page_total);
		
		m_display($root);
	}
}
?>