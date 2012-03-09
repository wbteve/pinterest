<?php
class ClubModule
{
	function index()
	{
		global $_FANWE;
		$cache_args = array(
			'club_index',
		);
		$_FANWE['nav_title'] = lang('common','club');
		$cache_file = getTplCache('page/club/club_index',$cache_args);
		if(!@include($cache_file))
			include template('page/club/club_index');
		display($cache_args);
	}
	
	function detail()
	{
		global $_FANWE;
		$id = intval($_FANWE['request']['tid']);
		if($id == 0)
			fHeader('location: '.FU('club/index'));
		
		$topic = FS('Topic')->getTopicById($id);
		if(empty($topic))
			fHeader('location: '.FU('club/index'));
			
		$_FANWE['nav_title'] = lang('common','club');
		$_FANWE['nav_title'] = $topic['title'] .' - '. $_FANWE['nav_title'];
		 
		FDB::query('UPDATE '.FDB::table('share').' SET click_count = click_count + 1 WHERE share_id = '.$topic['share_id']);
		
		$topic['time'] = getBeforeTimelag($topic['create_time']);
		$topic['share'] = FS('Share')->getShareDetail($topic['share_id']);
		$user_share_collect = FS('Share')->getShareCollectUser($topic['share_id']);
		
		$forum_id= $topic['fid'];
		$forum = $_FANWE['cache']['forums']['all'][$forum_id];
		$root_forum = $forum;
		$is_root = true;
		if($forum['parent_id'] > 0)
		{
			$is_root = false;
			$root_forum = $_FANWE['cache']['forums']['all'][$forum['parent_id']];
		}
		
		FS('Topic')->updateTopicLooksCache($id);
		$topic_looks = FS('Topic')->getTopicLooks($id,33);
		
		$is_follow = FS('Topic')->getIsFollowTid($id);
		$follow_count = FS('Topic')->getTopicFollowCount($id);
		$follow_users = FS('Topic')->getTopicFollows($id,9);
		$user_new_topics = FS('Topic')->getUserNewTopicList($id,$topic['uid'],5);
		$forum_hot_topics = FS('Topic')->getHotTopicList($id,$root_forum['fid'],9);
		$forum_new_topics = FS('Topic')->getNowTopicList($id,$root_forum['fid'],10);
		$forum_hot_pics = FS('Topic')->getImgTopic('hot',9,1,$root_forum['fid']);
		$new_events = FS('Event')->getHotNewEvent(10);
		$best_topics = FS('Topic')->getImgTopic('hot',12,1);
		$best_topics = array_chunk($best_topics,3);
		
		$page_args = array(
			'tid'=>$id
		);
		
		$count = $topic['post_count'];
		$pager = buildPage('club/'.ACTION_NAME,$page_args,$count,$_FANWE['page'],10);
		$post_list = FS('Topic')->getTopicPostList($id,$pager['limit']);
		
		$args = array(
			'share_list'=>&$post_list,
			'pager'=>&$pager,
			'current_share_id'=>$topic['share_id']
		);
		$post_html = tplFetch("inc/share/post_share_list",$args);
		
		include template('page/club/club_detail');
		display();
	}
	
	function forum()
	{
		global $_FANWE;
		$forum_id= intval($_FANWE['request']['fid']);
		$forum = $_FANWE['cache']['forums']['all'][$forum_id];
		
		if(empty($forum))
			fHeader('location: '.FU('club/index'));
		
		$_FANWE['nav_title'] = $forum['name'].' - '.lang('common','club');
		
		$is_best = false;
		$is_root = true;
		$root_forum = $forum;
		$where = '';
		$fids = array();
		
		if($forum['parent_id'] > 0)
		{
			$is_root = false;
			$root_forum = $_FANWE['cache']['forums']['all'][$forum['parent_id']];
		}
		
		if(isset($forum['childs']))
			$fids = $forum['childs'];
		
		$fids[] = $forum_id;
		
		if(count($fids) == 1)
			$where = ' WHERE s.status = 1 AND ft.fid = '.$forum_id;
		else
			$where = ' WHERE s.status = 1 AND ft.fid IN ('.implode(',',$fids).')';
		
		$sort = '';
		$order = 'ft.is_top DESC,ft.tid DESC';
		
		if($_FANWE['request']['sort'] == 'post')
		{
			$order = 'ft.is_top DESC,ft.lastpost DESC,ft.tid DESC';
			$sort = 'post';
		}
			
		$page_args = array(
			'fid'=>$forum_id,
			'sort'=>$sort,
		);
		
		if($sort == '')
			unset($page_args['sort']);
	
		$count = FDB::resultFirst('SELECT COUNT(ft.tid) 
			FROM '.FDB::table('forum_thread').' AS ft 
			INNER JOIN '.FDB::table('share').' AS s ON s.share_id = ft.share_id '.$where);
		
		$pager = buildPage('club/forum',$page_args,$count,$_FANWE['page'],20);
		
		$topic_list = array();
		
		$sql = 'SELECT ft.fid,ft.tid,ft.title,ft.create_time,ft.lastpost,ft.lastposter,
			ft.uid,ft.post_count,ft.share_id,s.cache_data,ft.is_top,ft.is_best 
			FROM '.FDB::table('forum_thread').' AS ft 
			INNER JOIN '.FDB::table('share').' AS s ON s.share_id = ft.share_id 
			'.$where.' ORDER BY '.$order.' LIMIT '.$pager['limit'];
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			if(!empty($data['lastposter']))
				$data['last_time'] = getBeforeTimelag($data['lastpost']);
			
			$data['cache_data'] = fStripslashes(unserialize($data['cache_data']));
			$data['time'] = getBeforeTimelag($data['create_time']);
			$data['last_time'] = getBeforeTimelag($data['lastpost']);
			$data['url'] = FU('club/detail',array('tid'=>$data['tid']));
			FS('Share')->shareImageFormat($data,3);
            unset($data['cache_data']);
			$topic_list[$data['share_id']] = $data;
		}
		
		include template('page/club/club_forum');
		display();
	}
	
	function best()
	{
		global $_FANWE;
		$forum_id= intval($_FANWE['request']['fid']);
		$forum = $_FANWE['cache']['forums']['all'][$forum_id];
		
		if(empty($forum))
			fHeader('location: '.FU('club/index'));
		
		 $_FANWE['nav_title'] = $forum['name'].' - '.lang('common','club');
		$is_root = true;
		$is_best = true;
		$root_forum = $forum;
		$fids = array();
		
		if($forum['parent_id'] > 0)
		{
			$is_root = false;
			$root_forum = $_FANWE['cache']['forums']['all'][$forum['parent_id']];
		}
		
		if(isset($forum['childs']))
			$fids = $forum['childs'];
		
		$fids[] = $forum_id;
		
		$where = ' WHERE s.status = 1 AND ft.is_best = 1';
		
		if(count($fids) == 1)
			$where .= ' AND ft.fid = '.$forum_id;
		else
			$where .= ' AND ft.fid IN ('.implode(',',$fids).')';
		
		$sort = '';
		$order = 'ft.is_top DESC,ft.lastpost DESC,ft.tid DESC';
		
		if($_FANWE['request']['sort'] == 'tid')
		{
			$order = 'ft.is_top DESC,ft.tid DESC';
			$sort = 'tid';
		}
			
		$page_args = array(
			'fid'=>$forum_id,
			'sort'=>$sort,
		);
		
		if($sort == '')
			unset($page_args['sort']);
	
		$count = FDB::resultFirst('SELECT COUNT(ft.tid) 
			FROM '.FDB::table('forum_thread').' AS ft 
			INNER JOIN '.FDB::table('share').' AS s ON s.share_id = ft.share_id '.$where);
		
		$pager = buildPage('club/best',$page_args,$count,$_FANWE['page'],20);
		
		$topic_list = array();
		
		$sql = 'SELECT ft.fid,ft.tid,ft.title,ft.create_time,ft.lastpost,ft.lastposter,
			ft.uid,ft.post_count,ft.share_id,s.cache_data,ft.is_top,ft.is_best 
			FROM '.FDB::table('forum_thread').' AS ft 
			INNER JOIN '.FDB::table('share').' AS s ON s.share_id = ft.share_id 
			'.$where.' ORDER BY '.$order.' LIMIT '.$pager['limit'];
		
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			if(!empty($data['lastposter']))
				$data['last_time'] = getBeforeTimelag($data['lastpost']);
			
			$data['cache_data'] = fStripslashes(unserialize($data['cache_data']));
			$data['time'] = getBeforeTimelag($data['create_time']);
			$data['last_time'] = getBeforeTimelag($data['lastpost']);
			$data['url'] = FU('club/detail',array('tid'=>$data['tid']));
			FS('Share')->shareImageFormat($data,3);
            unset($data['cache_data']);
			$topic_list[$data['share_id']] = $data;
		}
		
		include template('page/club/club_forum');
		display();
	}
	
	function newtopic()
	{
		global $_FANWE;
		if($_FANWE['uid'] == 0)
			fHeader('location: '.FU('user/login'));
			
		$forum_id= intval($_FANWE['request']['fid']);
		$cache_args = array(
			'club_newtopic',
			$forum_id,
		);
		$_FANWE['nav_title'] = lang('common','club_newtopic');
		$current_fid = $forum_id;
		$forum = $_FANWE['cache']['forums']['all'][$forum_id];
		if(empty($forum))
			fHeader('location: '.FU('club/index'));
		
		$is_best = false;
		$is_root = true;
		$root_forum = $forum;
		if($forum['parent_id'] > 0)
		{
			$is_root = false;
			$root_forum = $_FANWE['cache']['forums']['all'][$forum['parent_id']];
		}
		else
		{
			if(isset($forum['childs']))
				$current_fid = current($forum['childs']);
		}
		
		include template('page/club/club_newtopic');
		
		display();
	}
	
	function donewtopic()
	{
		global $_FANWE;
		if($_FANWE['uid'] == 0)
			fHeader('location: '.FU('club/index'));
		
		$forum_id= intval($_FANWE['request']['fid']);
		if($forum_id == 0)
			fHeader('location: '.FU('club/index'));
			
		$forum = $_FANWE['cache']['forums']['all'][$forum_id];
		if(empty($forum))
			fHeader('location: '.FU('club/index'));
		
		$_FANWE['request']['title'] = trim($_FANWE['request']['title']);
		$_FANWE['request']['content'] = trim($_FANWE['request']['content']);
		if($_FANWE['request']['title'] == '' || $_FANWE['request']['content'] == '')
			fHeader('location: '.FU('club/index'));
		
		$_FANWE['request']['uid'] = $_FANWE['uid'];
		$_FANWE['request']['type'] = 'bar';
		
		if(!checkIpOperation("add_share",SHARE_INTERVAL_TIME))
		{
			showError('提交失败',lang('share','interval_tips'),-1);
		}
		
		$check_result = FS('Share')->checkWord($_FANWE['request']['content'],'content');
		if($check_result['error_code'] == 1)
		{
			showError('提交失败',$check_result['error_msg'],-1);
		}
		
		$check_result = FS('Share')->checkWord($_FANWE['request']['title'],'title');
		if($check_result['error_code'] == 1)
		{
			showError('提交失败',$check_result['error_msg'],-1);
		}
		
		$check_result = FS('Share')->checkWord($_FANWE['request']['tags'],'tag');
		if($check_result['error_code'] == 1)
		{
			showError('提交失败',$check_result['error_msg'],-1);
		}
		
		$share = FS('Share')->submit($_FANWE['request']);
		
		if($share['status'])
		{
			$thread = array();
			$thread['fid'] = $forum_id;
			$thread['share_id'] = $share['share_id'];
			$thread['uid'] = $_FANWE['uid'];
			$thread['title'] = htmlspecialchars($_FANWE['request']['title']);
			$thread['content'] = htmlspecialchars($_FANWE['request']['content']);
			$thread['create_time'] = fGmtTime();
			$tid = FDB::insert('forum_thread',$thread,true);
			FDB::query('UPDATE '.FDB::table('share').' SET rec_id = '.$tid.' 
				WHERE share_id = '.$share['share_id']);
			
			FDB::query("update ".FDB::table("user_count")." set forums = forums + 1,threads = threads + 1 where uid = ".$_FANWE['uid']);
			FDB::query("update ".FDB::table("forum")." set thread_count = thread_count+1 where fid = ".$forum_id);
			if($forum['parent_id'] > 0)
				FDB::query("update ".FDB::table("forum")." set thread_count = thread_count+1 where fid = ".$forum['parent_id']);
			
			FS('Medal')->runAuto($_FANWE['uid'],'forums');
			FS('User')->medalBehavior($_FANWE['uid'],'continue_forum');
		}
		fHeader('location: '.FU('club/forum',array('fid'=>$forum_id,'sort'=>'tid')));
	}
}
?>