<?php
class commentlistMapi
{
	public function run()
	{
		global $_FANWE;	
		
		$root = array();
		$root['return'] = 1;

		$share_id = (int)$_FANWE['requestData']['share_id'];
		$page = (int)$_FANWE['requestData']['page'];
		$page = max(1,$page);
		
		$sql_count = "SELECT COUNT(DISTINCT comment_id) FROM ".FDB::table("share_comment")." WHERE share_id = ".$share_id;
		$total = FDB::resultFirst($sql_count);
		$page_size = PAGE_SIZE;
		$max_page = 100;
		if($total > $max_page * $page_size)
			$total = $max_page * $page_size;

		if($page > $max_page)
			$page = $max_page;
		
		$page_total = ceil($total/$page_size);
		$limit = (($page - 1) * $page_size).",".$page_size;
		$sql = 'SELECT c.*,u.user_name,u.server_code FROM '.FDB::table('share_comment').' AS c 
			INNER JOIN '.FDB::table('user').' AS u ON u.uid = c.uid 
			WHERE c.share_id = '.$share_id.' ORDER BY c.comment_id DESC LIMIT '.$limit;
		$res = FDB::query($sql);
		$list = array();
		while($item = FDB::fetch($res))
		{
			$item['user_avatar'] = avatar($item['uid'],'m',$item['server_code'],1,true);
			$item['time'] = getBeforeTimelag($item['create_time']);
			$parses = m_express(&$item['content']);
			$item['parse_users'] = $parses['users'];
			$item['parse_events'] = $parses['events'];
			$list[] = $item;
		}

		$root['item'] = $list;
		$root['page'] = array("page"=>$page,"page_total"=>$page_total);
		m_display($root);
	}
}
?>