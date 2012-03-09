<?php
// +----------------------------------------------------------------------
// | 方维购物分享网站系统 (Build on ThinkPHP)
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://fanwe.com All rights reserved.
// +----------------------------------------------------------------------

/**
 * ask.service.php
 *
 * 问答服务类
 *
 * @package service
 * @author awfigq <awfigq@qq.com>
 */
class AskService
{
	public function getTopicById($tid)
	{
		$tid = (int)$tid;
		if(!$tid)
			return false;
		
		static $list = array();
		if(!isset($list[$tid]))
		{
			$list[$tid] = FDB::fetchFirst('SELECT *
				FROM '.FDB::table('ask_thread').' WHERE tid = '.$tid);
		}
		return $list[$tid];
	}
	
	public function updateTopicRec($tid,$title)
	{
		$sql = 'SELECT share_id,content
            FROM '.FDB::table('share')."
            WHERE type='ask_post' AND rec_id = '$tid'";
        $res = FDB::query($sql);
        while($data = FDB::fetch($res))
        {
            FS("Share")->updateShare($data['share_id'],$title,$data['content']);
        }
	}

    public function deleteTopic($share_id)
	{
		if(intval($share_id) == 0)
			return false;

		$thread = FDB::fetchFirst('SELECT * FROM '.FDB::table('ask_thread').' WHERE share_id = '.$share_id);
		if(empty($thread))
			return true;

		FDB::delete('ask_thread','share_id = '.$share_id);
        FS('Share')->deleteShare($share_id);
        FDB::query("update ".FDB::table("ask")." set thread_count = thread_count - 1 where aid = ".$thread['aid']);

		FDB::query('UPDATE '.FDB::table('user_count').' SET
				threads = threads - 1,
				forums = forums - 1
				WHERE uid = '.$thread['uid']);

        $sql = 'SELECT share_id
            FROM '.FDB::table('ask_post').'
            WHERE tid = '.$thread['tid'];
        $res = FDB::query($sql);
        while($data = FDB::fetch($res))
        {
            $share_id = $data['share_id'];
            AskService::deletePost($share_id,false);
        }
		
		FS('Medal')->runAuto($thread['uid'],'ask');
	}

	public function deletePost($share_id,$is_score = true)
	{
		if(intval($share_id) == 0)
			return false;

		$post = FDB::fetchFirst('SELECT * FROM '.FDB::table('ask_post').' WHERE share_id = '.$share_id);
		if(empty($post))
			return true;

		FDB::delete('ask_post','share_id = '.$share_id);

		FDB::query('UPDATE '.FDB::table('ask_thread').' SET
				post_count = post_count - 1
				WHERE tid = '.$post['tid']);

		FDB::query('UPDATE '.FDB::table('user_count').' SET
				ask_posts = ask_posts - 1,
				ask_best_posts = ask_best_posts - '.$post['is_best'].'
				WHERE uid = '.$post['uid']);
		
		FS("Share")->deleteShare($share_id,$is_score);
		FS('Medal')->runAuto($post['uid'],'ask_posts');
	}

	public function getIsEdit($tid)
	{
		global $_FANWE;
		$is_edit = false;
		$topic = AskService::getTopicById($tid);
		if($topic['uid'] == $_FANWE['uid'])
			$is_edit = true;
		return $is_edit;
	}

	public function getTopicDynamic($tid)
	{
		return FDB::fetchFirst('SELECT post_count,click_count
			FROM '.FDB::table('ask_thread').'
			WHERE tid = '.$tid);
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
			FROM '.FDB::table('ask_post').' AS ap 
			INNER JOIN '.FDB::table('share').' AS s ON s.share_id = ap.share_id  
			WHERE s.status = 1 AND ap.tid = '.$tid.' ORDER BY pid DESC LIMIT '.$limit;
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
		$post['create_time'] = TIME_UTC;
		$id = FDB::insert('ask_post',$post,true);
		if($id > 0)
		{
			FDB::query('UPDATE '.FDB::table('ask_thread').'
				SET post_count = post_count + 1,lastpost = '.TIME_UTC.',lastposter = '.$_FANWE['uid'].'
				WHERE tid = '.$tid);
			FDB::query("update ".FDB::table("user_count")." set ask_posts = ask_posts + 1 where uid = ".$_FANWE['uid']);
			FS('Medal')->runAuto($_FANWE['uid'],'ask_posts');
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
		$uids = &AskService::getTopicFollowsCache($tid);
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
		$uids = &AskService::getTopicFollowsCache($tid);
		return count($uids);
	}

	/**
	 * 获取关注主题的会员编号集合缓存
	 * @return array(1,2,...)
	 */
	public function getTopicFollowsCache($tid)
	{
		global $_FANWE;
		$key = 'ask/thread/'.getDirsById($tid).'/follows';
		$data = getCache($key);
		if($data === NULL)
		{
			$data = array();
			$res = FDB::query('SELECT uid
				FROM '.FDB::table('user_attention').'
				WHERE type = \'ask\' AND rec_id = '.$tid);
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
		$uids = &AskService::getTopicFollowsCache($tid);
		switch($type)
		{
			case 'add':
				$uids[$uid] = 1;
			break;

			case 'delete':
				unset($uids[$uid]);
			break;
		}

		setCache('ask/thread/'.getDirsById($tid).'/follows',$uids);
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
			$uids = &AskService::getTopicFollowsCache($tid);
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

		if(AskService::getIsFollowTid($tid))
		{
			FDB::query('DELETE FROM '.FDB::table('user_attention').'
				WHERE type = \'ask\' AND uid = '.$_FANWE['uid'].' AND rec_id = '.$tid);
			AskService::updateTopicFollowsCache($tid,$_FANWE['uid'],'delete');
			return false;
		}
		else
		{
			$topic = AskService::getTopicById($tid);

			if(empty($topic) || $_FANWE['uid'] == $topic['uid'])
				return false;

			$attention = array(
				'uid'         => $_FANWE['uid'],
				'rec_id'      => $tid,
				'share_id'    => $topic['share_id'],
				'type'        => 'ask',
				'create_time' => fGmtTime()
			);

			FDB::insert('user_attention',$attention);

			$share['share'] = array(
				'share_id'    => $topic['share_id'],
				'content'     => '我关注了这个主题[强]',
				'is_no_post'  => 0,
			);

			FS('Share')->saveRelay($share);
			AskService::updateTopicFollowsCache($tid,$_FANWE['uid'],'add');
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
		$uids = AskService::getTopicLooksCache($tid);
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
	 * 获取浏览主题的会员编号集合缓存
	 * @return array(1,2,...)
	 */
	public function getTopicLooksCache($tid)
	{
		$key = 'ask/thread/'.getDirsById($tid).'/looks';
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

		$uids = AskService::getTopicLooksCache($tid);

		if(!isset($uids[$_FANWE['uid']]))
		{
			if(count($uids) > 100)
				array_shift($uids);
			$uids[$_FANWE['uid']] = 1;
			setCache('ask/thread/'.getDirsById($tid).'/looks',$uids);
		}
	}

	/**
	 * 获取带图片或商品分享的问题
	 * @return array
	 */
	public function getImgAsk($type,$num,$pic_num,$aid = 0,$begin = 0)
	{
		global $_FANWE;

		$where = ' WHERE s.status = 1 ';
		if($aid > 0)
			$where .= ' AND at.aid = '.$aid;

		$order = 'at.tid DESC';

		switch($type)
		{
			case 'solve';
				$where .= ' AND at.is_solve = 1';
				$order = 'at.is_solve DESC,at.tid DESC';
			break;
			case 'hot';
				$order = 'at.post_count DESC,at.tid DESC';
			break;
		}

		$list = array();
		$share_users = array();
		$sql = 'SELECT at.aid,at.tid,at.title,at.content,at.create_time,at.lastpost,at.lastposter,
			at.uid,at.post_count,at.share_id,s.cache_data
			FROM '.FDB::table('ask_thread').' AS at
			INNER JOIN '.FDB::table('share').' AS s ON s.share_id = at.share_id 
				AND s.share_data IN (\'goods\',\'photo\',\'goods_photo\')
			'.$where.' ORDER BY '.$order.' LIMIT '.$begin.','.$num;
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$data['cache_data'] = fStripslashes(unserialize($data['cache_data']));
			$data['time'] = getBeforeTimelag($data['create_time']);
			$data['last_time'] = getBeforeTimelag($data['lastpost']);
			$data['url'] = FU('ask/detail',array('tid'=>$data['tid']));
			FS('Share')->shareImageFormat($data,$pic_num);
			unset($data['cache_data']);
			$list[$data['share_id']] = $data;
		}
		return $list;
	}

	/**
	 * 获取分类下最新主题
	 */
	public function getNowTopicList($tid,$aid,$num)
	{
		global $_FANWE;

		$where = ' WHERE 1';
		if($aid > 0)
			$where .= ' AND aid = '.$aid;

		$list = array();
		$sql = 'SELECT aid,tid,title,create_time,lastpost,lastposter,
			uid,post_count,share_id
			FROM '.FDB::table('ask_thread').'
			'.$where.' AND tid <> '.$tid.' ORDER BY tid DESC LIMIT 0,'.$num;
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$data['time'] = getBeforeTimelag($data['create_time']);
			$data['last_time'] = getBeforeTimelag($data['lastpost']);
			$data['url'] = FU('ask/detail',array('tid'=>$data['tid']));
			$list[$data['share_id']] = $data;
		}
		return $list;
	}

	/**
	 * 获取热门问题
	 * @return array
	 */
	public function getHotAsk($aid = 0,$num=9)
	{
		$sql= 'SELECT * FROM '.FDB::table("ask_thread").' WHERE status = 1 ';
		if($aid > 0)
			$sql .=' AND aid = '.$aid;

		$sql .= ' ORDER BY post_count DESC,tid DESC LIMIT 0,'.$num;

		return FDB::fetchAll($sql);
	}
}
?>