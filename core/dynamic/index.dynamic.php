<?php
//首页动态内容的函数

/**
 * 获取今日达人
 */
function getIndexTodayDaren()
{
	$args['today_daren'] = FS('Daren')->getIndexTodayDaren();
	return tplFetch('inc/index/today_daren',$args);
}

/**
 * 正在分享
 */
function getNewShare()
{
	$args['shares'] = FS('Share')->getNewShare();
	return tplFetch('inc/index/new_share',$args);
}

/**
 * 最新活动,热门主题
 */
function getHotTopic()
{
	$cache_file = getTplCache('inc/index/hot_topic',array(),1);
	if(getCacheIsUpdate($cache_file,600))
	{
		$args['new_events'] = FS('Event')->getHotNewEvent(3);
		$args['hot_topics'] = FS('Topic')->getHotTopicList(0,0,3);
	}

	return tplFetch('inc/index/hot_topic',$args,'',$cache_file);
}

/**
 * 分类最近7天热门分享
 */
function getDayCateShare()
{
	$args = array();
	$cache_file = getTplCache('inc/index/cate_share',array(),1);
	if(getCacheIsUpdate($cache_file,600))
	{
		$args['cate_list'] = FS('Share')->getIndexShareHotTags();
	}

	return tplFetch('inc/index/cate_share',$args,'',$cache_file);
}

/**
 * 分类最新的主题
 */
function getIndexTopic()
{
	global $_FANWE;
	$args = array();
	$cache_file = getTplCache('inc/index/new_topic',array(),1);
	if(getCacheIsUpdate($cache_file,600))
	{
		$res = FDB::query('SELECT fid,thread_count FROM '.FDB::table('forum').' WHERE parent_id = 0');
		while($data = FDB::fetch($res))
		{
			$_FANWE['cache']['forums']['all'][$data['fid']]['thread_count'] = $data['thread_count'];
		}

		$args['new_list'] = FS('Topic')->getImgTopic('new',7,4);
		$args['ask_list'] = FS('Ask')->getImgAsk('new',2,1);
	}

	return tplFetch('inc/index/topics',$args,'',$cache_file);
}

/**
 * 分类最新的主题
 */
function getDarenLists()
{
	$args['daren_list'] = FS('Daren')->getDarens();
	return tplFetch('inc/index/daren_list',$args);
}

/**
 * 搭配秀列表
 */
function getDapeiLists()
{
	$args = array();
	$cache_file = getTplCache('inc/index/dapei_list',array(),1);
	if(getCacheIsUpdate($cache_file,600))
	{
		$args['dapei_list'] = FS('Share')->getPhotoListByType("dapei");
	}
	return tplFetch('inc/index/dapei_list',$args,'',$cache_file);
}
/**
 * 晒货列表
 */
function getLookLists()
{
	$args = array();
	$cache_file = getTplCache('inc/index/look_list',array(),1);
	if(getCacheIsUpdate($cache_file,600))
	{
		$args['look_list'] = FS('Share')->getPhotoListByType("look");
	}
	return tplFetch('inc/index/look_list',$args,'',$cache_file);
}

/**
 * 热门杂志列表
 */
function getIndexHotAblum(){
	$args = array();
	$cache_file = getTplCache('inc/index/album_list',array(),1);
	if(getCacheIsUpdate($cache_file,600))
	{
		$args['album_list'] = FS("Album")->getIndexAlbums(6);
	}
	return tplFetch('inc/index/album_list',$args,'',$cache_file);
}

/**
 * 首页分类推荐分享
 */
function getIndexCateShare()
{
 	$args = array();
	$cache_file = getTplCache('inc/index/index_cate_share',array(),1);
	if(getCacheIsUpdate($cache_file,600))
	{
	 	global $_FANWE;
	 	FanweService::instance()->cache->loadCache('goods_category');
	 	$cate_indexs = $_FANWE['cache']['goods_category']['all'];
	 	foreach($cate_indexs as $k=>$v)
		{
			if($v['parent_id']==0 && $v['is_index']==1)
			{
				$cids = array();
				FS('Share')->getChildCids($v['cate_id'],$cids);
				/*$tags = FDB::fetchAll("select gt.tag_name,gt.tag_code from ".FDB::table("goods_category_tags")." as gct INNER JOIN ".FDB::table("goods_tags")." as gt on gt.tag_id = gct.tag_id where gct.cate_id in (".implode(",",$cids).") order by  gct.weight desc,gt.count desc,gt.tag_id desc limit 12");
				foreach($tags as $tk=>$tv){
					$tags[$tk]["encode"] = urlencode($tv['tag_code']);
				}
				$v['hot_tags'] = $tags ;
				*/
				$v['share_count'] = FDB::resultFirst("select count(DISTINCT s.uid) from ".FDB::table("share")." as s INNER JOIN ".FDB::table("share_category")." as sc ON sc.share_id = s.share_id where sc.cate_id in (".implode(",",$cids).")");
				$v['user'] = array();
				$users = FDB::fetchAll("select DISTINCT(s.uid) as uid from ".FDB::table("share")." as s INNER JOIN ".FDB::table("share_category")." as sc ON sc.share_id = s.share_id where sc.cate_id in (".implode(",",$cids).") AND s.status=1 order by s.share_id desc limit 10");
				foreach ($users as $kk=>$vv)
				{	$user = FS("User")->getUserShowName($vv['uid']);
					$v['user'][$vv['uid']] = $user['name'];
				}
				$sql = "SELECT s.*,st.tag_name FROM ".FDB::table("share")." as s 
						INNER JOIN ".FDB::table("share_category")." as sc ON sc.share_id = s.share_id 
						INNER JOIN ".FDB::table("share_tags")." as st ON st.share_id = s.share_id
						INNER JOIN ".FDB::table("goods_category_tags")." as gct ON gct.cate_id = sc.cate_id
						WHERE gct.cate_id in (".implode(",",$cids).") AND s.is_index=1 AND s.status=1 GROUP BY s.share_id ORDER BY s.sort desc,s.share_id desc limit 30";
				$v['share_list'] = FDB::fetchAll($sql);
				foreach($v['share_list'] as $kk=>$vv){
					$tag['tag_name'] = $vv['tag_name'];
					$tag['tag_code'] = $vv['tag_name'];
					$tag['encode'] = urlencode($vv['tag_name']);
					$v['hot_tags'][$vv['tag_name']] = $tag;
				}
				$v['share_list'] = FS("Share")->getShareDetailList($v['share_list']);
				$args['cate_list'][] = $v;
			}
		}
		unset($cate_indexs);
	}
	return tplFetch('inc/index/index_cate_share',$args,'',$cache_file);
}
?>