<?php
class StyleModule
{
	public function index()
	{
		global $_FANWE;
		FanweService::instance()->cache->loadCache('style_category');
		
		$sort = $_FANWE['request']['sort'];
		$sort = !empty($sort) ? $sort : "pop";

		$category_data = $_FANWE['cache']['style_category']['all'][3];
		$category_tags = array();
		
		$_FANWE['nav_title'] = $category_data['cate_name'];

		$child_ids = array();
		if(isset($category_data['child']))
		{
			$child_ids = $category_data['child'];
			foreach($category_data['child'] as $child_id)
			{
				$child_cate = $_FANWE['cache']['style_category']['all'][$child_id];
				$tag_key = 'style_category_tags_'.$child_id;
				FanweService::instance()->cache->loadCache($tag_key);
				foreach($_FANWE['cache'][$tag_key] as $k => $tag)
				{
					$tagurlpara['tag'] = urlencode($tag['tag_name']);
					$tag['url'] = FU("style/".ACTION_NAME,$tagurlpara);
					$child_cate['tags'][] = $tag;
				}
				$category_tags[] = $child_cate;
			}
		}

		$condition = " WHERE sp.type = 'dapei' AND s.is_best = 1 AND s.status=1 ";
		$title = $category_data['short_name'];
		$is_match = false;
		$tag = urldecode($_FANWE['request']['tag']);
		if(!empty($tag))
		{
            $_FANWE['nav_title'] = $tag .' - '. $_FANWE['nav_title'];
			$title = htmlspecialchars($tag);
			$is_match = true;
			$match_key = segmentToUnicode($tag,'+');
			$condition.=" AND match(sm.content_match) against('".$match_key."' IN BOOLEAN MODE) ";
			$page_args['tag'] = urlencode($tag);
		}

		//输出排序URL
		$sort_page_args = $page_args;
		$sort_page_args['sort'] = 'hot7';

		$hot7_url['url'] = FU('style/'.ACTION_NAME,$sort_page_args);
		if($sort=='hot7')
			$hot7_url['act'] = 1;

		$sort_page_args['sort'] = 'hot30';
		$hot30_url['url'] = FU('style/'.ACTION_NAME,$sort_page_args);
		if($sort=='hot30')
			$hot30_url['act'] = 1;

		$sort_page_args['sort'] = 'new';
		$new_url['url'] = FU('style/'.ACTION_NAME,$sort_page_args);
		if($sort=='new')
			$new_url['act'] = 1;

		$sort_page_args['sort'] = 'pop';
		$pop_url['url'] = FU('style/'.ACTION_NAME,$sort_page_args);
		if($sort=='pop')
			$pop_url['act'] = 1;

		if(!empty($_FANWE['request']['sort']))
			$page_args['sort'] = $sort;
		else
			$page_args['sort'] = 'pop';

		$today_time = getTodayTime();
		$field = '';
		switch($sort)
		{
			//7天最热 点击次数
			case 'hot7':
				$day7_time = $today_time - 604800;
				$field = ",(s.create_time > $day7_time) AS time_sort ";
				$sort = " ORDER BY time_sort DESC,s.click_count DESC";
			break;
			//30天最热 点击次数
			case 'hot30':
				$day30_time = $today_time - 2592000;
				$field = ",(s.create_time > $day30_time) AS time_sort ";
				$sort = " ORDER BY time_sort DESC,s.click_count DESC";
			break;
			//最新
			case 'new':
				$field = '';
				$sort = " ORDER BY s.share_id DESC";
			break;
			//潮流  喜欢人数
			case 'pop':
			default:
				$day7_time = $today_time - 604800;
				$field = ",(s.create_time > $day7_time) AS time_sort ";
				$sort = " ORDER BY time_sort DESC,s.collect_count DESC";
			break;
		}

		$sql = 'SELECT s.* '.$field.'
				FROM '.FDB::table('share_photo').' AS sp 
				INNER JOIN  '.FDB::table('share').' AS s ON s.share_id = sp.share_id ';

		if($is_match)
			$sql .= 'INNER JOIN '.FDB::table('share_match').' AS sm ON sm.share_id = s.share_id ';

		$sql .= $condition.' GROUP BY s.share_id '.$sort;

		$sql_count = 'SELECT COUNT(DISTINCT s.share_id)
			FROM '.FDB::table('share_photo').' AS sp 
				INNER JOIN  '.FDB::table('share').' AS s ON s.share_id = sp.share_id ';

		if($is_match)
			$sql_count .= 'INNER JOIN '.FDB::table('share_match').' AS sm ON sm.share_id = s.share_id ';

		$sql_count .= $condition;

		$page_size = 20;
		$max_page = 100;
		$count = FDB::resultFirst($sql_count);
		if($count > $max_page * $page_size)
			$count = $max_page * $page_size;

		if($_FANWE['page'] > $max_page)
			$_FANWE['page'] = $max_page;

		$pager = buildPage('style/'.ACTION_NAME,$page_args,$count,$_FANWE['page'],$page_size,'',3);

		$share_datas = array();
		$sql  = $sql.' LIMIT '.$pager['limit'];

		$share_list = FDB::fetchAll($sql);
		$share_list = FS('Share')->getShareDetailList($share_list,true,true,true,true);
		
		//本周最热时尚搭配
		$day7_time = $today_time - 604800;
		$sql  = 'SELECT s.* ,(s.create_time > '.$day7_time.') AS time_sort 
			FROM '.FDB::table('share_photo').' AS sp 
			INNER JOIN '.FDB::table('share').' AS s ON s.share_id = sp.share_id 
			WHERE sp.type = \'dapei\' AND s.is_best = 1 
			GROUP BY s.share_id 
			ORDER BY time_sort DESC,s.click_count DESC 
			LIMIT 0,5';
		$share_week_hots = FDB::fetchAll($sql);
		$share_week_hots = FS('Share')->getShareDetailList($share_week_hots,true,true,true,true);
		
		include template('page/style/style_index');
		display();
	}
}
?>