<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**
 * topic.service.php
 *
 * 主题服务类
 *
 * @package service
 * @author awfigq <awfigq@qq.com>
 */
class TopicService
{
	public function getForumIDs($fid)
	{
		global $_FANWE;
		$forum = $_FANWE['cache']['forums']['all'][$fid];
		if(empty($forum))
			return array();

		$fids = array();
		if(isset($forum['childs']))
			$fids = $forum['childs'];

		$fids[] = $fid;

		return $fids;
	}

	public function getIsEdit($tid)
	{
		global $_FANWE;
		$is_edit = false;
		$topic = TopicService::getTopicById($tid);
		if($topic['uid'] == $_FANWE['uid'])
			$is_edit = true;
		return $is_edit;
	}

	/**
	 * 获取带图片或商品分享的主题
	 * @return array
	 */
	public function getImgTopic($type,$num,$pic_num,$fid = 0,$begin = 0,$ids = array())
	{
		global $_FANWE;

		$where = 'WHERE s.status = 1';
		if($fid > 0)
		{
			$fids = TopicService::getForumIDs($fid);
			if(count($fids) == 1)
				$where .= ' AND ft.fid = '.$fid;
			else
				$where .= ' AND ft.fid IN ('.implode(',',$fids).')';
		}

		if(!empty($ids))
		{
			$ids = implode(',',$ids);
			if(!empty($ids))
				$where .= ' AND ft.tid NOT IN ('.$ids.')';
		}

		$order = 'ft.tid DESC';

		switch($type)
		{
			case 'top';
				$order = 'ft.is_top DESC,ft.tid DESC';
			break;
			case 'best';
				$order = 'ft.is_best DESC,ft.tid DESC';
			break;
			case 'hot';
				$order = 'ft.post_count DESC,ft.tid DESC';
			break;
		}

		$list = array();
		$sql = 'SELECT ft.fid,ft.tid,ft.title,ft.content,ft.create_time,ft.lastpost,ft.lastposter,
			ft.uid,ft.post_count,ft.share_id,s.cache_data 
			FROM '.FDB::table('forum_thread').' AS ft
			INNER JOIN '.FDB::table('share').' AS s ON s.share_id = ft.share_id
				AND s.share_data IN (\'goods\',\'photo\',\'goods_photo\')
			'.$where.' ORDER BY '.$order.' LIMIT '.$begin.','.$num;
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$data['cache_data'] = fStripslashes(unserialize($data['cache_data']));
			$data['time'] = getBeforeTimelag($data['create_time']);
			$data['last_time'] = getBeforeTimelag($data['lastpost']);
			$data['url'] = FU('club/detail',array('tid'=>$data['tid']));
			FS('Share')->shareImageFormat($data,$pic_num);
			unset($data['cache_data']);
			$list[$data['share_id']] = $data;
		}
		return $list;
	}

	public function getTopicById($tid)
	{
		$tid = (int)$tid;
		if(!$tid)
			return false;
		
		static $list = array();
		if(!isset($list[$tid]))
		{
			$list[$tid] = FDB::fetchFirst('SELECT *
			FROM '.FDB::table('forum_thread').' WHERE tid = '.$tid);
		}
		return $list[$tid];
	}
	
	/**
	 * 获取主题回应列表
	 * @return array
	 */
	public function getTopicPostList($tid,$limit)
	{
		$tid = (int)$tid;
		if(!$tid)
			return array();
		
		$sql = 'SELECT s.* 
			FROM '.FDB::table('forum_post').' AS fp 
			INNER JOIN '.FDB::table('share').' AS s ON s.share_id = fp.share_id  
			WHERE s.status = 1 AND fp.tid = '.$tid.' ORDER BY pid DESC LIMIT '.$limit;
		$list = FDB::fetchAll($sql);
		return FS('Share')->getShareDetailList($list,true,true,true);
	}

	public function saveTopicPost($tid,$content,$share_id = 0)
	{
		global $_FANWE;
		$post = array();
		$post['tid'] = $tid;
		$post['share_id'] = $share_id;
		$post['uid'] = $_FANWE['uid'];
		$post['content'] = $content;
		$post['create_time'] = fGmtTime();
		$id = FDB::insert('forum_post',$post,true);
		if($id > 0)
		{
			FDB::query('UPDATE '.FDB::table('forum_thread').'
				SET post_count = post_count + 1,lastpost = '.fGmtTime().',lastposter = '.$_FANWE['uid'].'
				WHERE tid = '.$tid);

			FDB::query("update ".FDB::table("user_count")." set forum_posts = forum_posts + 1 where uid = ".$_FANWE['uid']);
			FS('Medal')->runAuto($_FANWE['uid'],'forum_posts');
		}

		return $id;
	}

	/**
	 * 获取关注主题的会员编号集合
	 */
	public function getTopicFollows($tid,$num = 9)
	{
		global $_FANWE;
		$uid = intval($_FANWE['uid']);
		$uids = &TopicService::getTopicFollowsCache($tid);
		$list = array_slice($uids,-$num,$num,true);

		if(isset($uids[$uid]))
		{
			if(!isset($list[$uid]))
			{
				array_shift($list);
				$list[$uid] = 1;
			}
			else
			{
				unset($list[$uid]);
				$list[$uid] = 1;
			}
		}

		$list = array_reverse($list,true);

		return $list;
	}

	/**
	 * 获取关注主题的会员数量
	 */
	public function getTopicFollowCount($tid)
	{
		$uids = &TopicService::getTopicFollowsCache($tid);
		return count($uids);
	}

	/**
	 * 获取关注主题的会员编号集合缓存
	 * @return array(1,2,...)
	 */
	public function getTopicFollowsCache($tid)
	{
		global $_FANWE;
		$key = 'topic/thread/'.getDirsById($tid).'/follows';
		$data = getCache($key);
		if($data === NULL)
		{
			$data = array();
			$res = FDB::query('SELECT uid
				FROM '.FDB::table('user_attention').'
				WHERE type = \'bar\' AND rec_id = '.$tid);
			while($user = FDB::fetch($res))
			{
				$data[$user['uid']] = 1;
			}
			setCache($key,$data);
		}

		return $data;
	}

	/**
	 * 更新关注主题的会员编号集合缓存
	 */
	public function updateTopicFollowsCache($tid,$uid,$type='add')
	{
		global $_FANWE;
		$uids = &TopicService::getTopicFollowsCache($tid);
		switch($type)
		{
			case 'add':
				$uids[$uid] = 1;
			break;

			case 'delete':
				unset($uids[$uid]);
			break;
		}

		setCache('topic/thread/'.getDirsById($tid).'/follows',$uids);
	}

	/**
	 * 获取登陆会员是否已关注此主题编号
	 * @param int $tid 主题编号
	 * @return bool
	 */
	public function getIsFollowTid($tid)
	{
		global $_FANWE;
		static $follows = array();
		$uid = $_FANWE['uid'];
		if($uid == 0)
			return false;

		if(!isset($follows[$tid][$uid]))
		{
			$uids = &TopicService::getTopicFollowsCache($tid);
			if(isset($uids[$uid]))
				$follows[$tid][$uid] = true;
			else
				$follows[$tid][$uid] = false;
		}
		return $follows[$tid][$uid];
	}

	/**
	 * 关注主题
	 如果已经关注此主题，则删除关注，返回false
	 如果没有关注此主题，则添加关注，返回true
	 * @param int $tid 主题编号
	 * @return bool
	 */
	public function followTopic($tid)
	{
		global $_FANWE;
		if($_FANWE['uid'] == 0)
			return false;

		if(TopicService::getIsFollowTid($tid))
		{
			FDB::query('DELETE FROM '.FDB::table('user_attention').'
				WHERE type = \'bar\' AND uid = '.$_FANWE['uid'].' AND rec_id = '.$tid);
			TopicService::updateTopicFollowsCache($tid,$_FANWE['uid'],'delete');
			return false;
		}
		else
		{
			$topic = TopicService::getTopicById($tid);

			if(empty($topic) || $_FANWE['uid'] == $topic['uid'])
				return false;

			$attention = array(
				'uid'         => $_FANWE['uid'],
				'rec_id'      => $tid,
				'share_id'    => $topic['share_id'],
				'type'        => 'bar',
				'create_time' => fGmtTime()
			);

			FDB::insert('user_attention',$attention);

			$share['share'] = array(
				'share_id'    => $topic['share_id'],
				'content'     => '我关注了这个主题[强]',
				'is_no_post'  => 0,
			);

			FS('Share')->saveRelay($share);
			TopicService::updateTopicFollowsCache($tid,$_FANWE['uid'],'add');
			return true;
		}
	}

	/**
	 * 获取浏览主题的会员编号集合
	 * @return array(1,2,...)
	 */
	public function getTopicLooks($tid,$num)
	{
		global $_FANWE;
		$uid = intval($_FANWE['uid']);
		$uids = TopicService::getTopicLooksCache($tid);
		$list = array_slice($uids,-$num,$num,true);

		if(isset($uids[$uid]))
		{
			if(!isset($list[$uid]))
			{
				array_shift($list);
				$list[$uid] = 1;
			}
			else
			{
				unset($list[$uid]);
				$list[$uid] = 1;
			}
		}

		return array_reverse($list,true);
	}

	/**
	 * 获取浏览主题的会员编号集合缓存
	 * @return array(1,2,...)
	 */
	public function getTopicLooksCache($tid)
	{
		$key = 'topic/thread/'.getDirsById($tid).'/looks';
		$data = getCache($key);
		if($data === NULL)
			$data = array();
		return $data;
	}

	/**
	 * 更新浏览主题的会员编号集合缓存
	 */
	public function updateTopicLooksCache($tid,$uid)
	{
		global $_FANWE;
		if($_FANWE['uid'] == 0 || $_FANWE['uid'] == $uid)
			return;

		$uids = TopicService::getTopicLooksCache($tid);

		if(!isset($uids[$_FANWE['uid']]))
		{
			if(count($uids) > 100)
				array_shift($uids);
			$uids[$_FANWE['uid']] = 1;
			setCache('topic/thread/'.getDirsById($tid).'/looks',$uids);
		}
	}

	/**
	 * 获取会员最近发表的主题
	 */
	public function getUserNewTopicList($tid,$uid,$num)
	{
		global $_FANWE;
		$list = array();
		$sql = 'SELECT fid,tid,title,create_time,lastpost,lastposter,
			uid,post_count,share_id
			FROM '.FDB::table('forum_thread').'
			WHERE uid = '.$uid.' AND tid <> '.$tid.' ORDER BY tid DESC LIMIT 0,'.$num;
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$data['time'] = getBeforeTimelag($data['create_time']);
			$data['last_time'] = getBeforeTimelag($data['lastpost']);
			$data['url'] = FU('club/detail',array('tid'=>$data['tid']));
			$list[$data['share_id']] = $data;
		}
		return $list;
	}

	/**
	 * 获取分类下最新主题
	 */
	public function getNowTopicList($tid,$fid,$num)
	{
		global $_FANWE;
		
		$tid = (int)$tid;
		$fid = (int)$fid;
		$num = (int)$num;
		if($num == 0)
			$num = 9;
		
		$where = '';
		if($fid > 0)
		{
			$fids = TopicService::getForumIDs($fid);
			if(count($fids) == 1)
				$where .= ' AND fid = '.$fid;
			else
				$where .= ' AND fid IN ('.implode(',',$fids).')';
		}
		
		if($tid > 0)
		{
			$where .= ' AND tid <> '.$tid;
		}
		
		if(!empty($where))
		{
			$where = 'WHERE' . $where;
			$where = str_replace('WHERE AND','WHERE',$where);
		}
		

		$list = array();
		$sql = 'SELECT fid,tid,title,create_time,lastpost,lastposter,
			uid,post_count,share_id
			FROM '.FDB::table('forum_thread').'
			'.$where.' ORDER BY tid DESC LIMIT 0,'.$num;
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$data['time'] = getBeforeTimelag($data['create_time']);
			$data['last_time'] = getBeforeTimelag($data['lastpost']);
			$data['url'] = FU('club/detail',array('tid'=>$data['tid']));
			$list[$data['share_id']] = $data;
		}
		return $list;
	}

	/**
	 * 获取分类下热门主题
	 */
	public function getHotTopicList($tid,$fid,$num)
	{
		global $_FANWE;

		$where = '';
		if($fid > 0)
		{
			$fids = TopicService::getForumIDs($fid);

			if(count($fids) == 1)
				$where .= ' AND fid = '.$fid;
			else
				$where .= ' AND fid IN ('.implode(',',$fids).')';
		}

		if($tid > 0)
			$where .= ' AND tid <> '.$tid;
		
		if(!empty($where))
		{
			$where = 'WHERE' . $where;
			$where = str_replace('WHERE AND','WHERE',$where);
		}

		$list = array();
		$sql = 'SELECT fid,tid,title,create_time,lastpost,lastposter,
			uid,post_count,share_id
			FROM '.FDB::table('forum_thread').'
			'.$where.' ORDER BY post_count DESC,tid DESC LIMIT 0,'.$num;
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$data['time'] = getBeforeTimelag($data['create_time']);
			$data['last_time'] = getBeforeTimelag($data['lastpost']);
			$data['url'] = FU('club/detail',array('tid'=>$data['tid']));
			$list[$data['share_id']] = $data;
		}
		return $list;
	}

	public function deletePost($share_id,$is_score = true)
	{
		if(intval($share_id) == 0)
			return false;

		$post = FDB::fetchFirst('SELECT * FROM '.FDB::table('forum_post').' WHERE share_id = '.$share_id);
		if(empty($post))
			return true;

		FDB::delete('forum_post','share_id = '.$share_id);

		FDB::query('UPDATE '.FDB::table('forum_thread').' SET
				post_count = post_count - 1
				WHERE tid = '.$post['tid']);

		FDB::query('UPDATE '.FDB::table('user_count').' SET
				forum_posts = forum_posts - 1
				WHERE uid = '.$post['uid']);
				
		FS('Share')->deleteShare($share_id,$is_score);
		FS('Medal')->runAuto($post['uid'],'ask_posts');
	}

    public function updateTopicCache($tid)
	{
		$key = 'topic/thread/'.getDirsById($tid).'/detail';
		deleteCache($key);
	}
	
	public function updateTopicRec($tid,$title)
	{
		$sql = 'SELECT share_id,content
            FROM '.FDB::table('share')."
            WHERE type='bar_post' AND rec_id = '$tid'";
        $res = FDB::query($sql);
        while($data = FDB::fetch($res))
        {
            FS("Share")->updateShare($data['share_id'],$title,$data['content']);
        }
	}

	public function deleteTopic($tid)
	{
		global $_FANWE;
		$topic = TopicService::getTopicById($tid);
		if(empty($topic))
			return;

		FanweService::instance()->cache->loadCache('forums');

		$forum_id= $topic['fid'];
		$forum = $_FANWE['cache']['forums']['all'][$forum_id];

		$share_id = $topic['share_id'];
		$share = FS('Share')->getShareById($share_id);
		FS('Share')->deleteShare($share_id);

		$res = FDB::query('SELECT * FROM '.FDB::table('forum_post').' WHERE tid = '.$tid);
		while($data = FDB::fetch($res))
		{
            TopicService::deletePost($data['share_id'],false);
		}
		FDB::query('DELETE FROM '.FDB::table('forum_post').' WHERE tid = '.$tid);
		FDB::query('DELETE FROM '.FDB::table('user_attention').' WHERE type=\'bar\' AND rec_id = '.$tid);
		FDB::query('DELETE FROM '.FDB::table('forum_thread').' WHERE tid = '.$tid);

		FDB::query('UPDATE '.FDB::table('forum').' SET thread_count = thread_count - 1 WHERE fid = '.$forum_id);
		if($forum['parent_id'] > 0)
		{
			FDB::query('UPDATE '.FDB::table('forum').' SET thread_count = thread_count - 1 WHERE fid = '.$forum['parent_id']);
		}
		FS('Medal')->runAuto($share['uid'],'forums');
		clearCacheDir('topic/thread/'.getDirsById($tid).'/');
	}
}
?>