<?php
class BookModule
{
	public function cate()
	{
		global $_FANWE;
		$category = urldecode($_FANWE['request']['cate']);
		if(!isset($_FANWE['cache']['goods_category']['cate_code'][$category]))
			fHeader('location: '.FU('book/shopping'));

		BookModule::getList();
	}

	public function shopping()
	{
		BookModule::getList();
	}

	public function search()
	{
		BookModule::getList();
	}
	
	public function dapei()
	{
		global $_FANWE;
		$_FANWE['user_click_share_id'] = (int)$_FANWE['request']['sid'];
		unset($_FANWE['request']['sid']);
		$cache_file = getTplCache('page/book/book_dapei',$_FANWE['request'],2);
		if(getCacheIsUpdate($cache_file,BOOK_CACHE_PAGE_TIME,1))
		{
			FanweService::instance()->cache->loadCache('style_category');
			
			$sort = $_FANWE['request']['sort'];
			$sort = !empty($sort) ? $sort : "pop";
	
			$category_data = $_FANWE['cache']['style_category']['all'][1];
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
						$tag['url'] = FU("book/".ACTION_NAME,$tagurlpara);
						$child_cate['tags'][] = $tag;
					}
					$category_tags[] = $child_cate;
				}
			}
	
			$condition = " WHERE sp.type = 'dapei' AND s.status=1 ";
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
	
			$hot7_url['url'] = FU('book/'.ACTION_NAME,$sort_page_args);
			if($sort=='hot7')
				$hot7_url['act'] = 1;
	
			$sort_page_args['sort'] = 'hot30';
			$hot30_url['url'] = FU('book/'.ACTION_NAME,$sort_page_args);
			if($sort=='hot30')
				$hot30_url['act'] = 1;
	
			$sort_page_args['sort'] = 'new';
			$new_url['url'] = FU('book/'.ACTION_NAME,$sort_page_args);
			if($sort=='new')
				$new_url['act'] = 1;
	
			$sort_page_args['sort'] = 'pop';
			$pop_url['url'] = FU('book/'.ACTION_NAME,$sort_page_args);
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
			
			$sql = 'SELECT DISTINCT(s.share_id),s.uid,s.content,s.collect_count,s.comment_count,s.create_time,s.cache_data '.$field.'
					FROM '.FDB::table('share_photo').' AS sp 
					INNER JOIN  '.FDB::table('share').' AS s ON s.share_id = sp.share_id ';
	
			if($is_match)
				$sql .= 'INNER JOIN '.FDB::table('share_match').' AS sm ON sm.share_id = s.share_id ';
	
			$sql .= $condition.$sort;
	
			$sql_count = 'SELECT COUNT(DISTINCT s.share_id)
				FROM '.FDB::table('share_photo').' AS sp 
					INNER JOIN  '.FDB::table('share').' AS s ON s.share_id = sp.share_id ';
	
			if($is_match)
				$sql_count .= 'INNER JOIN '.FDB::table('share_match').' AS sm ON sm.share_id = s.share_id ';
	
			$sql_count .= $condition;
	
			$page_size = 40;
			$max_page = 100;
			$count = FDB::resultFirst($sql_count);
			if($count > $max_page * $page_size)
				$count = $max_page * $page_size;
	
			if($_FANWE['page'] > $max_page)
				$_FANWE['page'] = $max_page;
	
			$pager = buildPage('book/'.ACTION_NAME,$page_args,$count,$_FANWE['page'],$page_size,'',3);
	
			$share_datas = array();
			$sql  = $sql.' LIMIT '.$pager['limit'];
			
			$share_list = FDB::fetchAll($sql);
			$share_list = FS('Share')->getShareDetailList($share_list,false,false,false,true,5);
			$sid = (int)$_FANWE['request']['sid'];
			if($sid > 0)
			{
				if(isset($share_list[$sid]))
				{
					$temp_share = $share_list[$sid];
					unset($share_list[$sid]);
					array_unshift($share_list,$temp_share);
				}
				else
				{
					$temp_share[] = FS('Share')->getShareById($sid);
					if($temp_share)
					{
						$temp_share = FS('Share')->getShareDetailList($temp_share,false,false,false,true,5);
						array_unshift($share_list,$temp_share[$sid]);
					}
				}
			}
	
			$col = 4;
			$index = 0;
			$share_display = array();
			foreach($share_list as $share)
			{
				$mod = $index % $col;
				$share_display['col'.$mod][] = $share;
				$index++;
			}
	
			include template('page/book/book_dapei');
			display($cache_file);
			exit;
		}
		else
		{
			include $cache_file;
			display();
		}
	}
	
	public function look()
	{
		global $_FANWE;
		$_FANWE['user_click_share_id'] = (int)$_FANWE['request']['sid'];
		unset($_FANWE['request']['sid']);
		$cache_file = getTplCache('page/book/book_look',$_FANWE['request'],2);
		if(getCacheIsUpdate($cache_file,BOOK_CACHE_PAGE_TIME,1))
		{
			FanweService::instance()->cache->loadCache('style_category');
			
			$sort = $_FANWE['request']['sort'];
			$sort = !empty($sort) ? $sort : "pop";
	
			$category_data = $_FANWE['cache']['style_category']['all'][2];
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
						$tag['url'] = FU("book/".ACTION_NAME,$tagurlpara);
						$child_cate['tags'][] = $tag;
					}
					$category_tags[] = $child_cate;
				}
			}
	
			$condition = " WHERE sp.type = 'look' AND s.status=1 ";
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
	
			$hot7_url['url'] = FU('book/'.ACTION_NAME,$sort_page_args);
			if($sort=='hot7')
				$hot7_url['act'] = 1;
	
			$sort_page_args['sort'] = 'hot30';
			$hot30_url['url'] = FU('book/'.ACTION_NAME,$sort_page_args);
			if($sort=='hot30')
				$hot30_url['act'] = 1;
	
			$sort_page_args['sort'] = 'new';
			$new_url['url'] = FU('book/'.ACTION_NAME,$sort_page_args);
			if($sort=='new')
				$new_url['act'] = 1;
	
			$sort_page_args['sort'] = 'pop';
			$pop_url['url'] = FU('book/'.ACTION_NAME,$sort_page_args);
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
	
			$sql = 'SELECT DISTINCT(s.share_id),s.uid,s.content,s.collect_count,s.comment_count,s.create_time,s.cache_data '.$field.'
					FROM '.FDB::table('share_photo').' AS sp 
					INNER JOIN  '.FDB::table('share').' AS s ON s.share_id = sp.share_id ';
	
			if($is_match)
				$sql .= 'INNER JOIN '.FDB::table('share_match').' AS sm ON sm.share_id = s.share_id ';
	
			$sql .= $condition.$sort;
	
			$sql_count = 'SELECT COUNT(DISTINCT s.share_id)
				FROM '.FDB::table('share_photo').' AS sp 
					INNER JOIN  '.FDB::table('share').' AS s ON s.share_id = sp.share_id ';
	
			if($is_match)
				$sql_count .= 'INNER JOIN '.FDB::table('share_match').' AS sm ON sm.share_id = s.share_id ';
	
			$sql_count .= $condition;
	
			$page_size = 40;
			$max_page = 100;
			$count = FDB::resultFirst($sql_count);
			if($count > $max_page * $page_size)
				$count = $max_page * $page_size;
	
			if($_FANWE['page'] > $max_page)
				$_FANWE['page'] = $max_page;
	
			$pager = buildPage('book/'.ACTION_NAME,$page_args,$count,$_FANWE['page'],$page_size,'',3);
	
			$share_datas = array();
			$sql  = $sql.' LIMIT '.$pager['limit'];
			
			$share_list = FDB::fetchAll($sql);
			$share_list = FS('Share')->getShareDetailList($share_list,false,false,false,true);
			
			$col = 4;
			$index = 0;
			$share_display = array();
			foreach($share_list as $share)
			{
				$mod = $index % $col;
				$share_display['col'.$mod][] = $share;
				$index++;
			}
	
			include template('page/book/book_look');
			display($cache_file);
			exit;
		}
		else
		{
			include $cache_file;
			display();
		}
	}

	private function getList()
	{
		global $_FANWE;
		$_FANWE['user_click_share_id'] = (int)$_FANWE['request']['sid'];
		unset($_FANWE['request']['sid']);
		
		$cache_file = getTplCache('page/book/book_index',$_FANWE['request'],2);
		if(getCacheIsUpdate($cache_file,BOOK_CACHE_PAGE_TIME,1))
		{
			$category = urldecode($_FANWE['request']['cate']);
			$is_root = false;
			$page_args = array();
			if(isset($_FANWE['cache']['goods_category']['cate_code'][$category]))
			{
				$page_args['cate'] = $_FANWE['request']['cate'];
				$cate_id = $_FANWE['cache']['goods_category']['cate_code'][$category];
				$goods_cate_code = $category;
			}
			else
			{
				$is_root = true;
				$cate_id = $_FANWE['cache']['goods_category']['root'];
			}
	
			$sort = $_FANWE['request']['sort'];
			$sort = !empty($sort) ? $sort : "pop";
	
			$category_data = $_FANWE['cache']['goods_category']['all'][$cate_id];
			$category_tags = array();
	
			$_FANWE['nav_title'] = $category_data['cate_name'];
			
			if(!empty($category_data['seo_keywords']))
			{
				$_FANWE['seo_keywords'] = $category_data['seo_keywords'];
				$_FANWE['setting']['site_keywords'] = '';
			}
			
			if(!empty($category_data['seo_desc']))
			{
				$_FANWE['seo_description'] = $category_data['seo_desc'];
				$_FANWE['setting']['site_description'] = '';
			}
	
			$child_ids = array();
			if(isset($category_data['child']))
			{
				$child_ids = $category_data['child'];
				if(!$is_root)
					$tagurlpara['cate'] = urlencode($category_data['cate_code']);
				foreach($category_data['child'] as $child_id)
				{
					$child_cate = $_FANWE['cache']['goods_category']['all'][$child_id];
					$tag_key = 'goods_category_tags_'.$child_id;
					FanweService::instance()->cache->loadCache($tag_key);
					foreach($_FANWE['cache'][$tag_key] as $k => $tag)
					{
						$tagurlpara['tag'] = urlencode($tag['tag_name']);
						$tag['url'] = FU("book/".ACTION_NAME,$tagurlpara);
						$child_cate['tags'][] = $tag;
						if($k > 16)
							break;
					}
					$category_tags[] = $child_cate;
				}
			}
	
			$hot_tags = array();
			if(!$is_root)
			{
				$child_ids[] = $cate_id;
				require fimport("function/share");
				$hot_tags  = getHotTags($child_ids,$category,10);
			}
			if(intval($_FANWE['setting']['book_photo_goods'])==0)
				$condition = " WHERE s.share_data IN ('goods','photo','goods_photo')";
			elseif(intval($_FANWE['setting']['book_photo_goods'])==1)
				$condition = " WHERE s.share_data IN ('photo','goods_photo')";
			elseif(intval($_FANWE['setting']['book_photo_goods'])==2)
				$condition = " WHERE s.share_data IN ('goods','goods_photo')";
			
			if(!$is_root)
			{
				$cids = array();
				FS('Share')->getChildCids($cate_id,$cids);
				$condition .= " AND sc.cate_id IN (".implode(',',$cids).")";
			}
	
			$title = $category_data['short_name'];
			$is_match = false;
			$tag = urldecode($_FANWE['request']['tag']);
			if(!empty($tag))
			{
				$_FANWE['nav_title'] = $tag .' - '. $_FANWE['nav_title'];
				$title = htmlspecialchars($tag);
				$is_match = true;
				//$match_key = FS('Words')->segment($tag,10);
				//$match_key = tagToUnicode($match_key,'+');
				$match_key = segmentToUnicode($tag,'+');
				$condition.=" AND match(sm.content_match) against('".$match_key."' IN BOOLEAN MODE) ";
				$page_args['tag'] = urlencode($tag);
			}
	
			//输出排序URL
			$sort_page_args = $page_args;
			$sort_page_args['sort'] = 'hot7';
	
			$hot7_url['url'] = FU('book/'.ACTION_NAME,$sort_page_args);
			if($sort=='hot7')
				$hot7_url['act'] = 1;
	
			$sort_page_args['sort'] = 'hot30';
			$hot30_url['url'] = FU('book/'.ACTION_NAME,$sort_page_args);
			if($sort=='hot30')
				$hot30_url['act'] = 1;
	
			$sort_page_args['sort'] = 'new';
			$new_url['url'] = FU('book/'.ACTION_NAME,$sort_page_args);
			if($sort=='new')
				$new_url['act'] = 1;
	
			$sort_page_args['sort'] = 'pop';
			$pop_url['url'] = FU('book/'.ACTION_NAME,$sort_page_args);
			if($sort=='pop')
				$pop_url['act'] = 1;
	
			if(!empty($_FANWE['request']['sort']))
				$page_args['sort'] = $sort;
			else
				$page_args['sort'] = 'pop';
	
			$today_time = getTodayTime();
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
	
			$sql = 'SELECT DISTINCT(s.share_id),s.uid,s.content,s.collect_count,s.comment_count,s.create_time,s.cache_data '.$field.'
					FROM '.FDB::table('share').' AS s ';
	
			if(!$is_root)
				$sql .= 'INNER JOIN '.FDB::table('share_category').' AS sc ON s.share_id = sc.share_id ';
	
			if($is_match)
				$sql .= 'INNER JOIN '.FDB::table('share_match').' AS sm ON sm.share_id = s.share_id ';
			$condition .=" and s.status=1 ";
			$sql .= $condition.$sort;
	
			$sql_count = 'SELECT COUNT(DISTINCT s.share_id)
				FROM '.FDB::table('share').' AS s ';
	
			if(!$is_root)
				$sql_count .= 'INNER JOIN '.FDB::table('share_category').' AS sc ON s.share_id = sc.share_id ';
	
			if($is_match)
				$sql_count .= 'INNER JOIN '.FDB::table('share_match').' AS sm ON sm.share_id = s.share_id ';
	
			$sql_count .= $condition;
	
			$page_size = 40;
			$max_page = 100;
			$count = FDB::resultFirst($sql_count);
			if($count > $max_page * $page_size)
				$count = $max_page * $page_size;
	
			if($_FANWE['page'] > $max_page)
				$_FANWE['page'] = $max_page;
	
			$action = ACTION_NAME;
			if($action == 'search')
				$action = 'shopping';
	
			$pager = buildPage('book/'.$action,$page_args,$count,$_FANWE['page'],$page_size,'',3);
	
			$share_datas = array();
			$sql  = $sql.' LIMIT '.$pager['limit'];
	
			$share_list = FDB::fetchAll($sql);
			$share_list = FS('Share')->getShareDetailList($share_list,false,false,false,true,2);
			
			$col = 4;
			$index = 0;
			$share_display = array();
			foreach($share_list as $share)
			{
				$mod = $index % $col;
				$share_display['col'.$mod][] = $share;
				$index++;
			}
	
			include template('page/book/book_index');
			display($cache_file);
			exit;
		}
		else
		{
			include $cache_file;
			display();
		}
	}
}
?>