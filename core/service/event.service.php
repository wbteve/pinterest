<?php
class EventService
{
	function deleteEvent($share_id)
	{
		$share_id = (int)$share_id;
		$event_id = (int)FDB::resultFirst("select id from ".FDB::table("event")." 
			where share_id = $share_id");
		if($event_id > 0)
		{
			$share = FDB::fetchFirst("select share_id,uid from ".FDB::table("event_share")." 
				where event_id = $event_id order by share_id asc limit 0,1");
			if($share)
			{
				$share_id = (int)$share['share_id'];	
				$uid = (int)$share['uid'];
				FDB::query("update ".FDB::table("event")." set thread_count = thread_count - 1,share_id = $share_id,uid = $uid where id = $event_id");
				FDB::delete('event_share','share_id = '.$share_id);
			}
			else
			{
				FDB::delete('event','id = '.$event_id);
			}
		}
		else
		{
			FDB::delete('event_share','share_id = '.$share_id);
			FDB::query("update ".FDB::table("event")." set thread_count = thread_count - 1 where id = $event_id");
		}
	}

	function removeEvent($id)
	{
		$id = (int)$id;
		$event = FDB::fetchFirst("select id from ".FDB::table("event")." where id = $id");
		if($event)
		{
			$res = FDB::query("select share_id from ".FDB::table("event_share")." where event_id = $id");
			while($data = FDB::fetch($res))
			{
				FS('Share')->deleteShare($data['share_id']);
			}

			FDB::delete("event_share","event_id = $id");
			FS('Share')->deleteShare($event['share_id']);
			FDB::delete("event","id = $id");
		}
	}
	
	/**
	 * 获取热门最新活动话题
	 * @return array
	 */
	public function getHotNewEvent($num,$ids = array())
	{
		global $_FANWE;

		$where = '';
		if(!empty($ids))
			$where .= ' AND id NOT IN ('.implode(',',$ids).')';

		$list = array();
		$sql = 'SELECT id,title,uid,thread_count,create_time,last_share,last_time,share_id 
			FROM '.FDB::table('event').'
			WHERE is_event = 1'.$where.' ORDER BY is_hot DESC,id DESC LIMIT 0,'.$num;
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$data['time'] = getBeforeTimelag($data['create_time']);
			$data['last_time'] = getBeforeTimelag($data['last_time']);
			$data['url'] = FU('event/detail',array('id'=>$data['id']));
			$list[$data['share_id']] = $data;
		}
		return $list;
	}

	/**
	 * 热门有图活动主题
	 * @return array
	 */
	public function getHotImgEvent($num)
	{
		global $_FANWE;
		$list = array();
		$sql = 'SELECT e.id,e.title,e.uid,e.thread_count,e.create_time,e.last_share,e.last_time,e.share_id,
			s.content,s.cache_data 
			FROM '.FDB::table('event').' AS e 
			INNER JOIN '.FDB::table('share').' AS s ON s.share_id = e.share_id
				AND s.share_data IN (\'goods\',\'photo\',\'goods_photo\')
			WHERE e.is_event = 1 ORDER BY e.is_hot DESC,e.thread_count DESC LIMIT 0,'.$num;
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$data['cache_data'] = fStripslashes(unserialize($data['cache_data']));
			$data['time'] = getBeforeTimelag($data['create_time']);
			$data['last_time'] = getBeforeTimelag($data['last_time']);
			$data['url'] = FU('event/detail',array('id'=>$data['id']));
			FS('Share')->shareImageFormat($data);
			unset($data['cache_data']);
			$list[$data['share_id']] = $data;
		}
		return $list;
	}
	
	/**
	 * 获取热门的话题
	 */
	function getHotEvent($num=10,$begin=0)
	{
		global $_FANWE;
		$list = FDB::fetchAll("select * from ".FDB::table("event")." where thread_count > 0 order by thread_count desc,id desc limit $begin,$num");
		if(empty($list))
			return false;
		return $list;
	}
	/**
	 * 获取最新的话题
	 */
	function getNewEvent($num=10)
	{
		global $_FANWE;
		$list = FDB::fetchAll("select * from ".FDB::table("event")." order by id desc limit $num");
		if(empty($list))
			return false;
		return $list;
	}
	
	/**
	 * 获取用户发布的话题
	 */
	function getUserEvent($uid,$num=10)
	{
		$list = FDB::fetchAll("select * from ".FDB::table("event")." where uid ={$uid} order by id desc limit $num");
		return $list;
	}
	
	/**
	 * 获取用户参与的话题
	 */
	function getUserJoinevent($uid,$num)
	{
		$uid = (int)$uid;
		$num = (int)$num;
		
		$sql = 'SELECT DISTINCT event_id FROM '.FDB::table('event_share').' 
			WHERE uid = '.$uid." ORDER BY share_id DESC LIMIT 0,$num";
		$ids = array();
		$res = FDB::query($sql);
		while($data = FDB::fetch($res))
		{
			$ids[] = $data['event_id'];
		}
		
		$list = array();
		if(count($ids) > 0)
		{
			$ids = implode(',',$ids);
			$list = FDB::fetchAll("select * from ".FDB::table("event")." where id IN (".$ids.") order by id desc limit $num");
		}
		return $list;
	}
}
?>
