<?php
class AskModule
{
	function index()
	{
		global $_FANWE;
		$_FANWE['nav_title'] = lang('common','ask');
		$ask_indexs = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
		$asks = $_FANWE['cache']['asks'];
		$index = 0;
		$ask_list = array();
		foreach($asks as $ask)
		{
            if($ask['aid'] > 0)
            {
                $ask['index_char'] = $ask_indexs[$index];
                $ask['list'] = FDB::fetchAll('SELECT tid,title,post_count,is_solve
                    FROM '.FDB::table('ask_thread').'
                    WHERE status=1 and aid='.$ask['aid'].' ORDER BY tid DESC LIMIT 0,3');

                if($index % 2 == 0)
                    $ask_list['l'][] = $ask;
                else
                    $ask_list['r'][] = $ask;
                $index++;
            }
		}

		$cache_file = getTplCache('page/ask/ask_index',$cache_args);

		//新发表的问题
		$new_asks= FDB::fetchAll("SELECT tid,title,is_solve FROM ".FDB::table("ask_thread")." WHERE status=1 order by tid desc limit 9");

		//热门有图问题
		$hot_asks = FS('Ask')->getImgAsk('hot',9);

		include template('page/ask/ask_index');
		display();
	}

	function newtopic()
	{
		global $_FANWE;
		if(intval($_FANWE['uid'])==0)
		{
			fHeader("location: ".FU('user/login'));
		}
		 $_FANWE['nav_title'] = lang('common','ask_newtopic');
		$current_aid = intval($_FANWE['request']["aid"]);
		$asks = $_FANWE['cache']['asks'];
		if($current_aid == 0 || !isset($asks[$current_aid]))
		{
			$current_ask = current($asks);
			$current_aid = $current_ask['aid'];
		}

		include template('page/ask/ask_newtopic');
		display();
	}

	function forum()
	{
		global $_FANWE;
		$current_aid = intval($_FANWE['request']['aid']);
		 $_FANWE['nav_title'] = lang('common','ask');
		FanweService::instance()->cache->loadCache('forums');
		$forum_list = array();
		foreach($_FANWE['cache']['forums']['root'] as $forum_rootid)
		{
			$forum_list[] = $_FANWE['cache']['forums']['all'][$forum_rootid];
		}

		$type = !empty($_FANWE['request']['type']) ? $_FANWE['request']['type'] : "all";
		$asks = $_FANWE['cache']['asks'];

		$user_where = '';
		$page_args = array();
		$where = ' WHERE at.status = 1 ';
		if($current_aid > 0)
		{
			$current_ask = $asks[$current_aid];
			if(!empty($current_ask))
			{
				$_FANWE['nav_title'] = $current_ask['name'] .' - '. $_FANWE['nav_title'];
				$where .= ' AND at.aid = '.$current_aid;
				$user_where .= ' AND at.aid = '.$current_aid;
				$page_args['aid'] = $current_aid;
			}
			else
				$current_aid = 0;
		}

		switch($type)
		{
			case 'best':
				$where .= ' AND at.is_best = 1';
				$best_type = 'current';
				$page_args['type'] = 'best';
			break;

			case 'over':
				$where .= ' AND at.is_solve = 1';
				$over_type = 'current';
				$page_args['type'] = 'over';
			break;

			case 'wait':
				$where .= ' AND at.is_solve = 0';
				$wait_type = 'current';
				$page_args['type'] = 'wait';
			break;

			case 'none':
				$where .= ' AND at.post_count = 0';
				$none_type = 'current';
				$page_args['type'] = 'none';
			break;

			default:
				$all_type = 'current';
			break;
		}

		//热门主题
		$hottopics = FS('Ask')->getHotAsk($current_aid);

		//本吧活跃分子
		$sql = 'SELECT u.uid,u.user_name,u.server_code
			FROM '.FDB::table('ask_thread').' AS at
			LEFT JOIN '.FDB::table('user_count').' AS uc ON uc.uid = at.uid
			LEFT JOIN '.FDB::table('user').' AS u ON u.uid = at.uid
			WHERE at.status = 1 '.$user_where.' GROUP BY at.uid
			ORDER BY uc.ask_posts DESC,uc.uid ASC LIMIT 0,9';
		$hotusers = FDB::fetchAll($sql);

		$ask_count = FDB::resultFirst('SELECT count(at.tid) FROM '.FDB::table('ask_thread').' AS at'.$where);
		$page_size = 30;
		$pager = buildPage('ask/'.ACTION_NAME,$page_args,$ask_count,$_FANWE['page'],$page_size);

		//获取数据列表
		$sql = 'SELECT at.tid,at.title,at.uid,at.is_solve,at.is_top,
			at.is_best,at.post_count,at.create_time,at.lastpost,at.lastposter,COUNT(ua.rec_id) AS follow_count
			FROM '.FDB::table('ask_thread').' AS at
			LEFT JOIN '.FDB::table('user_attention').' AS ua ON ua.rec_id = at.tid AND type = \'ask\'
			'.$where.' GROUP BY at.tid
			ORDER BY at.is_top DESC,at.sort ASC,at.tid DESC LIMIT '.$pager['limit'];

		$uids = array();
		$users = array();
		$lastposters = array();
		$res = FDB::query($sql);
		$ask_threads = array();
		while($data = FDB::fetch($res))
		{
			if(!empty($data['lastposter']))
				$data['last_time'] = fToDate($data['lastpost'],'Y-m-d');

			$data['time'] = fToDate($data['create_time'],'Y-m-d');
			$ask_threads[$data['tid']] = $data;
		}

		include template('page/ask/ask_list');
		display();
	}

	function detail()
	{
		global $_FANWE;
		$id = intval($_FANWE['request']['tid']);
		if($id == 0)
			fHeader('location: '.FU('ask/index'));

		$topic = FS('Ask')->getTopicById($id);
		if(empty($topic))
			fHeader('location: '.FU('ask/index'));

		$_FANWE['nav_title'] = lang('common','ask');
		$_FANWE['nav_title'] = $topic['title'] .' - '. $_FANWE['nav_title'];
		FDB::query('UPDATE '.FDB::table('share').' SET click_count = click_count + 1 WHERE share_id = '.$topic['share_id']);

		$topic['time'] = getBeforeTimelag($topic['create_time']);
		$topic['share'] = FS('Share')->getShareDetail($topic['share_id']);
		$user_share_collect = FS('Share')->getShareCollectUser($topic['share_id']);
		if(!isset($user_share_collect[$_FANWE['uid']]))
		{
			if(FS('Share')->getIsCollectByUid($topic['share_id'],$_FANWE['uid']))
				$user_share_collect[$_FANWE['uid']] = $_FANWE['uid'];
		}
		
		$ask_id= $topic['aid'];
		$ask = $_FANWE['cache']['asks'][$ask_id];

		FS('Ask')->updateTopicLooksCache($id);
		$topic_looks = FS('Ask')->getTopicLooks($id,33);

		$is_follow = FS('Ask')->getIsFollowTid($id);
		$follow_count = FS('Ask')->getTopicFollowCount($id);
		$follow_users = FS('Ask')->getTopicFollows($id,9);

		$ask_hot_topics = FS('Ask')->getHotAsk($ask_id,9);
		$ask_hot_pics = FS('Ask')->getImgAsk('hot',9);

		$new_events = FS('Event')->getHotNewEvent(10);
		$ask_new_topics = FS('Ask')->getNowTopicList($id,$ask_id,10);

		$best_topics = FS('Topic')->getImgTopic('hot',12,1);
		$best_topics = array_chunk($best_topics,3);

		$page_args = array(
			'tid'=>$id
		);

		$count = $topic['post_count'];
		$pager = buildPage('ask/'.ACTION_NAME,$page_args,$count,$_FANWE['page'],10);
		$post_list = FS('Ask')->getTopicPostList($id,$pager['limit']);

		$args = array(
			'share_list'=>&$post_list,
			'pager'=>&$pager,
			'current_share_id'=>$topic['share_id']
		);
		$post_html = tplFetch("inc/share/post_share_list",$args);

		include template('page/ask/ask_detail');
		display();
	}

	function donewtopic()
	{
		global $_FANWE;
		if($_FANWE['uid'] == 0)
			fHeader('location: '.FU('ask/index'));

		$aid= intval($_FANWE['request']['aid']);
		if($aid == 0)
			fHeader('location: '.FU('ask/index'));

		$asks = $_FANWE['cache']['asks'];
		if(!isset($asks[$aid]))
			fHeader('location: '.FU('ask/index'));

		$_FANWE['request']['title'] = trim($_FANWE['request']['title']);
		$_FANWE['request']['content'] = trim($_FANWE['request']['content']);
		if($_FANWE['request']['title'] == '' || $_FANWE['request']['content'] == '')
			fHeader('location: '.FU('ask/index'));

		$_FANWE['request']['uid'] = $_FANWE['uid'];
		$_FANWE['request']['type'] = 'ask';
		
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
			$thread['aid'] = $aid;
			$thread['share_id'] = $share['share_id'];
			$thread['uid'] = $_FANWE['uid'];
			$thread['title'] = htmlspecialchars($_FANWE['request']['title']);
			$thread['content'] = htmlspecialchars($_FANWE['request']['content']);
			$thread['create_time'] = fGmtTime();
			$tid = FDB::insert('ask_thread',$thread,true);
			FDB::query('UPDATE '.FDB::table('share').' SET rec_id = '.$tid.'
				WHERE share_id = '.$share['share_id']);

			FDB::query("update ".FDB::table("user_count")." set ask = ask + 1,threads = threads + 1 where uid = ".$_FANWE['uid']);
			FDB::query("update ".FDB::table("ask")." set thread_count = thread_count + 1 where aid = ".$aid);
			FS('Medal')->runAuto($_FANWE['uid'],'ask');
			FS('User')->medalBehavior($_FANWE['uid'],'continue_ask');
		}
		fHeader('location: '.FU('ask/forum',array('aid'=>$aid)));
	}
}
?>