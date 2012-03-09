<?php
//主题动态内容的函数

/**
 * FLASH最新图片推荐
 */
function getNewBestTopics()
{
	global $_FANWE;
	$args = array();
	$cache_file = getTplCache('inc/club/new_topic',array(),1);
	if(getCacheIsUpdate($cache_file,300))
	{
		$args['flash_list'] = FS('Topic')->getImgTopic('best',4,1);
		$args['best_list'] = array_chunk(FS('Topic')->getImgTopic('best',12,1,0,4),6);
		foreach($args['best_list'] as $key => $best_item)
		{
			$args['best_list'][$key] = array_chunk($best_item,3);
		}

		$args['event_list'] = FS('Event')->getHotNewEvent(3);
	}

	return tplFetch('inc/club/new_topic',$args,'',$cache_file);
}

/**
 * 主分类最新推荐主题
 */
function getRootForumBests()
{
	global $_FANWE;
	$args = array();
	$cache_file = getTplCache('inc/club/forum_list',array(),1);
	if(getCacheIsUpdate($cache_file,300))
	{
		$res = FDB::query('SELECT fid,thread_count FROM '.FDB::table('forum').' WHERE parent_id = 0');
		while($data = FDB::fetch($res))
		{
			$_FANWE['cache']['forums']['all'][$data['fid']]['thread_count'] = $data['thread_count'];
		}
		
		$forum_list = array();
		$chars = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N');
		$fids = $_FANWE['cache']['forums']['root'];
		$forum_index = 0;
		foreach($fids as $fid)
		{
			$forum = $_FANWE['cache']['forums']['all'][$fid];
			$forum['char'] = $chars[$forum_index];
			$forum['topics'] = FS('Topic')->getImgTopic('best',5,3,$fid);
			$forum_list[] = $forum;
			$forum_index++;
		}
		
		$forum_list = array_chunk($forum_list,2);
		$args['forum_list'] = &$forum_list;
	}

	return tplFetch('inc/club/forum_list',$args,'',$cache_file);
}

function getBestTopics()
{
	global $_FANWE;
	$args = array();
	$cache_file = getTplCache('inc/club/best_topic',array(),1);
	if(getCacheIsUpdate($cache_file,300))
	{
		$args['best_list'] = FS('Topic')->getImgTopic('best',5,4);
	}

	return tplFetch('inc/club/best_topic',$args,'',$cache_file);
}

function getBestFlashs($fid)
{
	global $_FANWE;
	$args = array();
	$cache_file = getTplCache('inc/club/flash_topic',array($fid),1);
	if(getCacheIsUpdate($cache_file,300))
	{
		$args['best_list'] = FS('Topic')->getImgTopic('best',6,1,$fid);
	}

	return tplFetch('inc/club/flash_topic',$args,'',$cache_file);
}
?>