<?php
class EventModule
{
	public function index()
	{
		global $_FANWE;
		$_FANWE['nav_title'] = lang('common','event');
        $_FANWE['seo_description'] = lang('common','event');
        $_FANWE['setting']['site_description'] = '';
		//左侧热门话题
		$hot_l_event_list = FS("event")->getHotEvent(12);
		//最新话题
		$new_l_event_list = FS("event")->getNewEvent(12);
		//右侧热门话题
		$hot_event_list= FS("event")->getHotEvent(10);
		if(intval($_FANWE['uid'])>0)
		{
			//我发布的
			$me_event_list = FS("event")->getUserEvent($_FANWE['uid'],5);
			//我参与的
			$me_join_event_list = FS("event")->getUserJoinevent($_FANWE['uid'],5);
		}
		include template('page/event/event_index');
		display();		
	}
	
	public function detail()
	{
		global $_FANWE;
		$id = intval($_FANWE['request']['id']);
		if($id == 0)
			fHeader("location: ".FU('index/index'));
		
		include fimport('dynamic/u');
		//获取相关的分享ID
		$eventinfo  =  FDB::fetchFirst("select * from ".FDB::table("event")." where id=".$id);
		if(intval($eventinfo['share_id']) == 0)
		{
			fHeader("location: ".FU('index/index'));
		}
		
		$eventinfo['share'] = FS('Share')->getShareDetail($eventinfo['share_id']);
		if($share_detail === false)
			fHeader("location: ".FU('index'));
		
		$user_share_collect = FS('Share')->getShareCollectUser($eventinfo['share_id']);
		$page_title = preg_replace("/\[[^\]]+\]/i","",$eventinfo['title']);
		$_FANWE['nav_title'] = $page_title.' - '.lang('common','event');
        $_FANWE['seo_description'] = $page_title;
        $_FANWE['setting']['site_description'] = '';
		
		//分享评论
		$page_args = array(
			'id'=>$id
		);

		$count = $eventinfo['thread_count'];
		$post_list = array();
		if($count > 0)
		{
			$pager = buildPage('event/'.ACTION_NAME,$page_args,$count,$_FANWE['page'],10);
			$sql = 'SELECT share_id FROM '.FDB::table('event_share').' 
				WHERE event_id = '.$id.' ORDER BY share_id DESC LIMIT '.$pager['limit'];
			$ids = array();
			$res = FDB::query($sql);
			while($data = FDB::fetch($res))
			{
				$ids[] = $data['share_id'];
			}
			$ids = implode(',',$ids);
			
			$sql = 'SELECT * from '.FDB::table('share').' where share_id IN ('.$ids.') ORDER BY share_id';
			$list = FDB::fetchAll($sql);
			$post_list = FS('Share')->getShareDetailList($list,true,true,true);
		}
		
		$args = array(
			'share_list'=>&$post_list,
			'pager'=>&$pager,
			'current_share_id'=>$eventinfo['share_id']
		);
		$post_html = tplFetch("inc/share/post_share_list",$args);
		
		//热门话题
		$hot_event_list= FS("event")->getHotEvent(10);
		if(intval($_FANWE['uid'])>0)
		{
			//我发布的
			$me_event_list = FS("event")->getUserEvent($_FANWE['uid'],5);
			//我参与的
			$me_join_event_list = FS("event")->getUserJoinevent($_FANWE['uid'],5);
		}
		
		if(intval($_FANWE['page'])==1)
			FDB::query('UPDATE '.FDB::table('share').' SET click_count = click_count + 1 WHERE share_id = '.$eventinfo['share_id']);
		
		include template('page/event/event_detail');
		display();		
	}
	
	public function listdetail()
	{
		global $_FANWE;
		if(!isset($_FANWE['request']['type']))
			$type = "hot";
		else
			$type = $_FANWE['request']['type'];
			
		$page_args = array();
		
		$where = "";
		if(!isset($_FANWE['request']['order']))
			$order = "time";
		else
			$order = $_FANWE['request']['order'];
		
		switch($order)
		{
			case "time":
				$sort = 'e.last_share DESC';
				$page_args['sort'] = 'time';
			break;
			case "thread_count":
				$sort = 'e.thread_count DESC';
				$page_args['sort'] = 'pop';
			break;
		}
		
		$is_query = true;
		switch($type)
		{
			case "new":
				$sort = 'e.id DESC';
				unset($page_args['sort']);
			break;
			case "me":
				if($_FANWE['uid'] == 0)
					fHeader("location: ".FU('user/login'));
				$where .= ' AND e.uid = '.$_FANWE['uid'];
			break;
			case "reply":
				if($_FANWE['uid'] == 0)
					fHeader("location: ".FU('user/login'));
				
				$count = FDB::resultFirst('SELECT COUNT(DISTINCT event_id) FROM '.FDB::table('event_share').' WHERE uid = '.$_FANWE['uid']);
				if($count == 0)
					$is_query = false;
				else
				{
					$sql = 'SELECT DISTINCT event_id FROM '.FDB::table('event_share').' 
						WHERE uid = '.$_FANWE['uid'];
					$ids = array();
					$res = FDB::query($sql);
					while($data = FDB::fetch($res))
					{
						$ids[] = $data['event_id'];
					}
					$ids = implode(',',$ids);
					$where .= ' AND e.id IN ('.$ids.')';
				}
			break;
			default:
				$sort = 'e.thread_count DESC';
				unset($page_args['sort']);
				$type = "hot";
			break;
		}
		
		if(!empty($where))
		{
			$where = ' WHERE'.$where;
			$where = str_replace('WHERE AND','WHERE',$where);
		}
		
		$detaillist = array();
		if($is_query)
		{
			if($type != 'reply')
				$count = FDB::resultFirst('SELECT COUNT(DISTINCT e.id) FROM '.FDB::table('event').' AS e'.$where);
			
			$pager = buildPage('event/'.ACTION_NAME,$page_args,$count,$_FANWE['page'],20);
			
			if(empty($sort))
				$sort = 'e.id DESC';
			else
				$sort .= ',e.id DESC';
			
			$sql = 'SELECT DISTINCT e.id,e.title,e.uid,e.create_time,e.last_share,e.last_share time,e.thread_count FROM '.FDB::table('event').' AS e'.$where.' ORDER BY '.$sort.' LIMIT '.$pager['limit'];
			$list_users = array();
			$res = FDB::query($sql);
			while($data = FDB::fetch($res))
			{
				$detaillist[$data['id']] = $data;
				$detaillist[$data['id']]['user'] = &$list_users[$data['uid']];
			}
			FS('User')->usersFormat($list_users);
		}
		
		//热门话题
		$hot_event_list= FS("event")->getHotEvent(10);
		if(intval($_FANWE['uid']) > 0)
		{
			//我发布的
			$me_event_list = FS("event")->getUserEvent($_FANWE['uid'],5);
			//我参与的
			$me_join_event_list = FS("event")->getUserJoinevent($_FANWE['uid'],5);
		}
		include template('page/event/event_list');
		display();
	}
}
?>
